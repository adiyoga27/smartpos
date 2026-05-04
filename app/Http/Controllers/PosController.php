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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $locations = BusinessLocation::where('business_id', auth()->user()->business_id)->get();
        $customers = Contact::where('business_id', auth()->user()->business_id)->customers()->orderBy('first_name')->get();
        $taxRates = TaxRate::where('business_id', auth()->user()->business_id)->with('subTaxes')->get();

        return view('sale_pos.create', compact('locations', 'customers', 'taxRates'));
    }

    public function store(Request $request): RedirectResponse
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
        ]);

        $businessId = auth()->user()->business_id;
        $locationId = $request->location_id ?? BusinessLocation::where('business_id', $businessId)->first()?->id;

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

        $finalTotal = $totalBeforeTax + $taxAmount;
        $change = max(0, $request->payment_amount - $finalTotal);

        $transaction = DB::transaction(function () use ($request, $businessId, $locationId, $invoiceNo, $discountAmount, $totalBeforeTax, $taxAmount, $finalTotal) {
            $transaction = Transaction::create([
                'business_id' => $businessId,
                'location_id' => $locationId,
                'type' => 'sell',
                'status' => 'final',
                'payment_status' => $request->payment_amount >= $finalTotal ? 'paid' : 'due',
                'contact_id' => $request->contact_id,
                'invoice_no' => $invoiceNo,
                'transaction_date' => now(),
                'total_before_tax' => $totalBeforeTax,
                'tax_id' => $request->tax_id,
                'tax_amount' => $taxAmount,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
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

                // Reduce stock
                VariationLocationDetail::where('variation_id', $item['variation_id'])
                    ->where('location_id', $locationId)
                    ->decrement('qty_available', $item['quantity']);
            }

            TransactionPayment::create([
                'transaction_id' => $transaction->id,
                'business_id' => $businessId,
                'amount' => $request->payment_amount,
                'method' => $request->payment_method,
                'paid_on' => now(),
                'created_by' => auth()->id(),
            ]);

            return $transaction;
        });

        return redirect()->route('sales.show', $transaction)->with('success', "Penjualan {$invoiceNo} berhasil. Kembalian: Rp ".number_format($change, 0, ',', '.'));
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
