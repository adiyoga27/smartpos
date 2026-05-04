<?php

namespace App\Http\Controllers;

use App\Models\BusinessLocation;
use App\Models\Contact;
use App\Models\TaxRate;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\VariationLocationDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'purchase')
            ->with(['contact', 'payments'])
            ->latest('transaction_date');

        if ($request->filled('search')) {
            $query->where('ref_no', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $purchases = $query->paginate(20);

        return view('purchase.index', compact('purchases'));
    }

    public function create(): View
    {
        $suppliers = Contact::where('business_id', auth()->user()->business_id)->suppliers()->orderBy('supplier_business_name')->get();
        $locations = BusinessLocation::where('business_id', auth()->user()->business_id)->get();
        $taxRates = TaxRate::where('business_id', auth()->user()->business_id)->get();

        $taxRatesJson = $taxRates->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'amount' => $t->amount,
        ]);

        return view('purchase.create', compact('suppliers', 'locations', 'taxRates', 'taxRatesJson'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'location_id' => 'required|exists:business_locations,id',
            'ref_no' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'required|exists:variations,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:tax_rates,id',
            'payment_method' => 'nullable|in:cash,bank_transfer,other',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);

        $businessId = auth()->user()->business_id;

        $subTotal = collect($request->items)->sum(fn ($i) => $i['quantity'] * $i['purchase_price']);
        $discountAmount = 0;
        if ($request->discount_type && $request->discount_amount) {
            $discountAmount = $request->discount_type === 'percentage'
                ? $subTotal * ($request->discount_amount / 100)
                : $request->discount_amount;
        }

        $totalBeforeTax = $subTotal - $discountAmount;
        $taxAmount = 0;
        if ($request->tax_id) {
            $taxRate = TaxRate::find($request->tax_id);
            $taxAmount = $totalBeforeTax * ($taxRate->amount / 100);
        }

        $finalTotal = $totalBeforeTax + $taxAmount;

        $transaction = DB::transaction(function () use ($request, $businessId, $discountAmount, $totalBeforeTax, $taxAmount, $finalTotal) {
            $transaction = Transaction::create([
                'business_id' => $businessId,
                'location_id' => $request->location_id,
                'type' => 'purchase',
                'status' => 'final',
                'payment_status' => ($request->payment_amount ?? 0) >= $finalTotal ? 'paid' : 'due',
                'contact_id' => $request->contact_id,
                'ref_no' => $request->ref_no,
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
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['purchase_price'],
                    'unit_price_inc_tax' => $item['purchase_price'],
                    'purchase_price' => $item['purchase_price'],
                    'purchase_price_inc_tax' => $item['purchase_price'],
                    'item_type' => 'purchase',
                ]);

                // Increase stock
                VariationLocationDetail::where('variation_id', $item['variation_id'])
                    ->where('location_id', $request->location_id)
                    ->increment('qty_available', $item['quantity']);
            }

            if ($request->payment_amount > 0) {
                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'business_id' => $businessId,
                    'amount' => $request->payment_amount,
                    'method' => $request->payment_method ?? 'cash',
                    'paid_on' => now(),
                    'created_by' => auth()->id(),
                ]);
            }

            return $transaction;
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan.');
    }

    public function show(Transaction $purchase): View
    {
        $purchase->load(['items.product', 'items.variation', 'contact', 'payments', 'location']);

        return view('purchase.show', compact('purchase'));
    }

    public function addPayment(Request $request, Transaction $purchase): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank_transfer,other',
        ]);

        $totalPaid = $purchase->payments()->sum('amount');
        $maxAmount = max(0, $purchase->final_total - $totalPaid);

        if ($request->amount > $maxAmount) {
            return back()->withErrors(['amount' => 'Jumlah pembayaran melebihi sisa tagihan (Rp '.number_format($maxAmount, 0, ',', '.').').']);
        }

        TransactionPayment::create([
            'transaction_id' => $purchase->id,
            'business_id' => auth()->user()->business_id,
            'amount' => $request->amount,
            'method' => $request->method,
            'paid_on' => now(),
            'created_by' => auth()->id(),
        ]);

        $newTotalPaid = $totalPaid + $request->amount;
        if ($newTotalPaid >= $purchase->final_total) {
            $purchase->update(['payment_status' => 'paid']);
        } else {
            $purchase->update(['payment_status' => 'partial']);
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembayaran berhasil dicatat.');
    }
}
