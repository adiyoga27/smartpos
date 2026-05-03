<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'sell')
            ->with(['contact', 'payments'])
            ->latest('transaction_date');

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $sales = $query->paginate(20);
        $totalSales = $query->sum('final_total');

        return view('sale.index', compact('sales', 'totalSales'));
    }

    public function show(Transaction $sale): View
    {
        $sale->load(['items.product', 'items.variation', 'contact', 'payments', 'location', 'tax']);

        return view('sale.show', compact('sale'));
    }

    public function drafts(): View
    {
        $drafts = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'sell')
            ->where('is_suspend', true)
            ->with(['contact'])
            ->latest()
            ->get();

        return view('sale.drafts', compact('drafts'));
    }

    public function destroy(Transaction $sale): RedirectResponse
    {
        $sale->items()->delete();
        $sale->payments()->delete();
        $sale->delete();

        return back()->with('success', 'Penjualan berhasil dihapus.');
    }
}
