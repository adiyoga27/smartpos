<?php

namespace App\Http\Controllers;

use App\Models\BusinessLocation;
use App\Models\Contact;
use App\Models\InvoiceScheme;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\VariationLocationDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $businessId = auth()->user()->business_id;
        $locations = BusinessLocation::where('business_id', $businessId)->get();

        $defaultLocation = BusinessLocation::where('business_id', $businessId)->first();
        $selectedLocationId = session('pos_location_id', $defaultLocation?->id);

        if (! $locations->pluck('id')->contains($selectedLocationId)) {
            $selectedLocationId = $defaultLocation?->id;
            session(['pos_location_id' => $selectedLocationId]);
        }

        $customers = Contact::where('business_id', $businessId)->customers()->orderBy('first_name')->get();
        $taxRates = TaxRate::where('business_id', $businessId)->with('subTaxes')->get();

        $taxRatesJson = $taxRates->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'amount' => $t->amount,
            'is_tax_group' => $t->is_tax_group,
            'sub_taxes' => $t->subTaxes->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'amount' => $s->amount,
            ])->values(),
        ]);

        $page = $request->get('page', 1);
        $products = Product::where('business_id', $businessId)
            ->where('is_inactive', false)
            ->where('not_for_selling', false)
            ->with(['variations.locationDetails', 'tax.subTaxes'])
            ->orderByRaw("(SELECT COALESCE(SUM(vld.qty_available), 0) FROM variation_location_details vld JOIN variations v ON v.id = vld.variation_id WHERE v.product_id = products.id AND vld.location_id = {$selectedLocationId}) > 0 DESC")
            ->orderBy('name')
            ->paginate(24, ['*'], 'page', $page);

        $productsJson = $products->map(function ($p) use ($selectedLocationId) {
            $v = $p->variations->first();
            $stockDetail = $v?->locationDetails?->where('location_id', $selectedLocationId)->first();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'barcode' => $v?->sub_sku,
                'image' => $p->image ? Storage::url($p->image) : null,
                'variation_id' => $v?->id,
                'sell_price' => $v?->default_sell_price,
                'sell_price_inc_tax' => $v?->sell_price_inc_tax,
                'purchase_price' => $v?->default_purchase_price,
                'tax_type' => $p->tax_type,
                'tax_id' => $p->tax_id,
                'enable_stock' => $p->enable_stock,
                'qty_available' => (float) ($stockDetail?->qty_available ?? 0),
            ];
        });

        return view('sale_pos.create', compact(
            'locations', 'selectedLocationId', 'customers', 'taxRates', 'taxRatesJson',
            'products', 'productsJson',
        ));
    }

    public function setLocation(Request $request): JsonResponse
    {
        $locationId = $request->location_id;
        $location = BusinessLocation::where('business_id', auth()->user()->business_id)
            ->find($locationId);

        if (! $location) {
            return response()->json(['success' => false, 'message' => 'Lokasi tidak ditemukan'], 404);
        }

        session(['pos_location_id' => $location->id]);

        return response()->json([
            'success' => true,
            'location' => ['id' => $location->id, 'name' => $location->name],
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
            'location_id' => 'nullable|exists:business_locations,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'required|exists:variations,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:tax_rates,id',
            'payment_method' => 'required|in:cash,card,bank_transfer,other',
            'payment_amount' => 'required|numeric|min:0',
            'payment_note' => 'nullable|string|max:255',
            'shipping_details' => 'nullable|string|max:1000',
            'shipping_charges' => 'nullable|numeric|min:0',
        ]);

        $businessId = auth()->user()->business_id;
        $locationId = $request->location_id ?? BusinessLocation::where('business_id', $businessId)->first()?->id;

        $stockErrors = [];
        foreach ($request->items as $item) {
            $product = Product::with('variations')->find($item['product_id']);
            if (! $product || ! $product->enable_stock) {
                continue;
            }

            $qtyAvailable = VariationLocationDetail::where('variation_id', $item['variation_id'])
                ->where('location_id', $locationId)
                ->value('qty_available') ?? 0;

            if ((float) $item['quantity'] > (float) $qtyAvailable) {
                $stockErrors[] = "Stok '{$product->name}' tidak mencukupi. Tersedia: {$qtyAvailable}, diminta: {$item['quantity']}.";
            }
        }

        if (! empty($stockErrors)) {
            $errorMessage = implode(' ', $stockErrors);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'stock_errors' => $stockErrors,
                ], 422);
            }

            return back()->withErrors(['items' => $errorMessage])->withInput();
        }

        $invoiceNo = $this->generateInvoiceNo($businessId);

        $subTotal = collect($request->items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']);
        $discountAmount = 0;

        if ($request->discount_type && $request->discount_amount) {
            $discountAmount = $request->discount_type === 'percentage'
                ? $subTotal * ($request->discount_amount / 100)
                : $request->discount_amount;
        }

        $totalBeforeTax = $subTotal - $discountAmount;
        $taxAmount = 0;
        $taxRate = null;
        if ($request->tax_id) {
            $taxRate = TaxRate::with('subTaxes')->find($request->tax_id);
            if ($taxRate) {
                $taxAmount += $totalBeforeTax * ($taxRate->amount / 100);
                foreach ($taxRate->subTaxes as $subTax) {
                    $taxAmount += $totalBeforeTax * ($subTax->amount / 100);
                }
            }
        }

        $shippingCharges = (float) ($request->shipping_charges ?? 0);
        $finalTotal = $totalBeforeTax + $taxAmount + $shippingCharges;
        $change = max(0, $request->payment_amount - $finalTotal);

        $totalPaid = collect($request->payments ?? [])->sum('amount') ?: (float) $request->payment_amount;

        $transaction = DB::transaction(function () use ($request, $businessId, $locationId, $invoiceNo, $discountAmount, $totalBeforeTax, $taxAmount, $finalTotal, $shippingCharges, $totalPaid) {
            $transaction = Transaction::create([
                'business_id' => $businessId,
                'location_id' => $locationId,
                'type' => 'sell',
                'status' => 'final',
                'payment_status' => $totalPaid >= $finalTotal ? 'paid' : ($totalPaid > 0 ? 'partial' : 'due'),
                'contact_id' => $request->contact_id,
                'invoice_no' => $invoiceNo,
                'transaction_date' => now(),
                'total_before_tax' => $totalBeforeTax,
                'tax_id' => $request->tax_id,
                'tax_amount' => $taxAmount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
                'shipping_details' => $request->shipping_details,
                'shipping_charges' => $shippingCharges,
                'final_total' => $finalTotal,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $lineTax = 0;
                if ($request->tax_id && $product->tax_type === 'exclusive') {
                    $lineTax = $lineTotal * (($taxRate?->amount ?? 0) / 100);
                    if ($taxRate && $taxRate->is_tax_group) {
                        foreach ($taxRate->subTaxes as $subTax) {
                            $lineTax += $lineTotal * ($subTax->amount / 100);
                        }
                    }
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_price_inc_tax' => $item['unit_price'] + $lineTax,
                    'item_tax' => $lineTax,
                    'tax_id' => $request->tax_id,
                    'item_type' => 'sell',
                ]);

                VariationLocationDetail::where('variation_id', $item['variation_id'])
                    ->where('location_id', $locationId)
                    ->decrement('qty_available', $item['quantity']);
            }

            $payments = $request->payments ?? [[
                'method' => $request->payment_method ?? 'cash',
                'amount' => $request->payment_amount ?? 0,
                'note' => $request->payment_note ?? null,
            ]];

            foreach ($payments as $payment) {
                if (($payment['amount'] ?? 0) > 0) {
                    TransactionPayment::create([
                        'transaction_id' => $transaction->id,
                        'business_id' => $businessId,
                        'amount' => $payment['amount'] ?? 0,
                        'method' => $payment['method'] ?? 'cash',
                        'paid_on' => now(),
                        'created_by' => auth()->id(),
                        'payment_ref_no' => $payment['note'] ?? null,
                    ]);
                }
            }

            return $transaction;
        });

        $transaction->load(['items.product', 'items.variation', 'contact', 'payments', 'location', 'tax']);

        $message = "Penjualan {$invoiceNo} berhasil. Kembalian: Rp ".number_format($change, 0, ',', '.');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'change' => $change,
                'change_formatted' => 'Rp '.number_format($change, 0, ',', '.'),
                'transaction' => $this->formatTransactionForJson($transaction),
            ]);
        }

        return redirect()->route('sales.show', $transaction)->with('success', $message);
    }

    private function formatTransactionForJson(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'invoice_no' => $transaction->invoice_no,
            'transaction_date' => $transaction->transaction_date->format('d/m/Y H:i'),
            'status' => $transaction->status,
            'payment_status' => $transaction->payment_status,
            'discount_type' => $transaction->discount_type,
            'discount_amount' => (float) $transaction->discount_amount,
            'tax_amount' => (float) $transaction->tax_amount,
            'total_before_tax' => (float) $transaction->total_before_tax,
            'final_total' => (float) $transaction->final_total,
            'location' => $transaction->location?->only(['id', 'name', 'landmark', 'city', 'state', 'country', 'zip_code', 'mobile']),
            'contact' => $transaction->contact?->only(['id', 'first_name', 'full_name', 'mobile']),
            'items' => $transaction->items->map(fn ($item) => [
                'product_name' => $item->product?->name,
                'variation_name' => $item->variation?->name,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'unit_price_inc_tax' => (float) $item->unit_price_inc_tax,
                'line_total' => (float) ($item->quantity * $item->unit_price),
            ])->values(),
            'payments' => $transaction->payments->map(fn ($p) => [
                'method' => $p->method,
                'amount' => (float) $p->amount,
                'paid_on' => $p->paid_on->format('d/m/Y H:i'),
                'method_label' => match ($p->method) {
                    'cash' => 'Tunai',
                    'card' => 'Kartu',
                    'bank_transfer' => 'Transfer Bank',
                    default => 'Lainnya',
                },
            ])->values(),
            'tax' => $transaction->tax?->only(['id', 'name', 'amount']),
        ];
    }

    public function printThermal(Transaction $transaction): View
    {
        $transaction->load(['items.product', 'items.variation', 'contact', 'payments', 'location', 'creator']);

        return view('sale_pos.print_thermal', [
            'transaction' => $transaction,
            'location' => $transaction->location ?? BusinessLocation::where('business_id', auth()->user()->business_id)->first(),
        ]);
    }

    public function printA4(Transaction $transaction): View
    {
        $transaction->load(['items.product', 'items.variation', 'contact', 'payments', 'location', 'creator']);

        return view('sale_pos.print_a4', [
            'transaction' => $transaction,
            'location' => $transaction->location ?? BusinessLocation::where('business_id', auth()->user()->business_id)->first(),
        ]);
    }

    private function generateInvoiceNo(int $businessId): string
    {
        $scheme = InvoiceScheme::where('business_id', $businessId)->where('is_default', true)->first();
        if (! $scheme) {
            return 'INV-'.now()->format('Ymd').'-'.str_pad(Transaction::where('business_id', $businessId)->count() + 1, 4, '0', STR_PAD_LEFT);
        }

        $scheme->increment('invoice_count');
        $count = $scheme->invoice_count;

        $prefix = $scheme->prefix ?? '';
        if ($scheme->scheme_type === 'year') {
            $prefix .= now()->format('y');
        }

        return $prefix.str_pad($count, $scheme->total_digits ?? 4, '0', STR_PAD_LEFT);
    }
}
