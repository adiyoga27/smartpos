<?php

namespace App\Http\Controllers;

use App\Models\BusinessLocation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\VariationLocationDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function adjustments(Request $request): View
    {
        $query = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'stock_adjustment')
            ->with(['location', 'items.product'])
            ->latest('transaction_date');

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        $adjustments = $query->paginate(20);
        $locations = BusinessLocation::where('business_id', auth()->user()->business_id)->get();

        return view('stock.adjustments', compact('adjustments', 'locations'));
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $request->validate([
            'location_id' => 'required|exists:business_locations,id',
            'adjustment_type' => 'required|in:normal,abnormal',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'required|exists:variations,id',
            'items.*.quantity' => 'required|numeric',
            'additional_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $transaction = Transaction::create([
                'business_id' => auth()->user()->business_id,
                'location_id' => $request->location_id,
                'type' => 'stock_adjustment',
                'status' => 'final',
                'adjustment_type' => $request->adjustment_type,
                'transaction_date' => now(),
                'additional_notes' => $request->additional_notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'quantity' => abs($item['quantity']),
                    'quantity_adjusted' => abs($item['quantity']),
                    'unit_price' => 0,
                    'unit_price_inc_tax' => 0,
                    'item_type' => 'adjustment',
                ]);

                if ($item['quantity'] > 0) {
                    VariationLocationDetail::where('variation_id', $item['variation_id'])
                        ->where('location_id', $request->location_id)
                        ->increment('qty_available', $item['quantity']);
                } else {
                    VariationLocationDetail::where('variation_id', $item['variation_id'])
                        ->where('location_id', $request->location_id)
                        ->decrement('qty_available', abs($item['quantity']));
                }
            }
        });

        return back()->with('success', 'Stok adjustment berhasil disimpan.');
    }

    public function transfers(): View
    {
        $transfers = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'stock_transfer')
            ->with(['location', 'items.product'])
            ->latest()
            ->paginate(20);

        $locations = BusinessLocation::where('business_id', auth()->user()->business_id)->get();

        return view('stock.transfers', compact('transfers', 'locations'));
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $request->validate([
            'from_location_id' => 'required|exists:business_locations,id',
            'to_location_id' => 'required|different:from_location_id|exists:business_locations,id',
            'items' => 'required|array',
            'items.*.variation_id' => 'required|exists:variations,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'additional_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $transaction = Transaction::create([
                'business_id' => auth()->user()->business_id,
                'location_id' => $request->from_location_id,
                'type' => 'stock_transfer',
                'status' => 'final',
                'transaction_date' => now(),
                'additional_notes' => $request->additional_notes,
                'shipping_details' => json_encode(['to_location_id' => $request->to_location_id]),
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => 0,
                    'unit_price_inc_tax' => 0,
                    'item_type' => 'transfer',
                ]);

                VariationLocationDetail::where('variation_id', $item['variation_id'])
                    ->where('location_id', $request->from_location_id)
                    ->decrement('qty_available', $item['quantity']);

                $toDetail = VariationLocationDetail::firstOrCreate(
                    [
                        'variation_id' => $item['variation_id'],
                        'location_id' => $request->to_location_id,
                    ],
                    [
                        'product_id' => $item['product_id'],
                        'qty_available' => 0,
                    ]
                );
                $toDetail->increment('qty_available', $item['quantity']);
            }
        });

        return back()->with('success', 'Transfer stok berhasil.');
    }
}
