<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $sales = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'sell')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->with(['contact', 'payments'])
            ->latest()
            ->paginate(20);

        $totalSales = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'sell')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->sum('final_total');

        $totalPaid = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'sell')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->where('payment_status', 'paid')
            ->sum('final_total');

        return view('report.sales', compact('sales', 'totalSales', 'totalPaid', 'fromDate', 'toDate'));
    }

    public function purchases(Request $request): View
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $purchases = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'purchase')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->with(['contact'])
            ->latest()
            ->paginate(20);

        $totalPurchases = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'purchase')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->sum('final_total');

        return view('report.purchases', compact('purchases', 'totalPurchases', 'fromDate', 'toDate'));
    }

    public function stock(): View
    {
        $products = Product::where('business_id', auth()->user()->business_id)
            ->with(['variations.locationDetails', 'category', 'unit'])
            ->where('enable_stock', true)
            ->paginate(20);

        return view('report.stock', compact('products'));
    }

    public function profitLoss(Request $request): View
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $totalSales = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'sell')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->sum('final_total');

        $totalPurchases = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'purchase')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->sum('final_total');

        $totalExpenses = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->sum('final_total');

        $grossProfit = $totalSales - $totalPurchases;
        $netProfit = $grossProfit - $totalExpenses;

        $dailyData = Transaction::where('business_id', auth()->user()->business_id)
            ->whereIn('type', ['sell', 'purchase', 'expense'])
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->selectRaw('DATE(transaction_date) as date, type, SUM(final_total) as total')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return view('report.profit_loss', compact(
            'totalSales', 'totalPurchases', 'totalExpenses',
            'grossProfit', 'netProfit', 'dailyData', 'fromDate', 'toDate'
        ));
    }

    public function tax(Request $request): View
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $taxReport = Transaction::where('business_id', auth()->user()->business_id)
            ->whereIn('type', ['sell', 'purchase'])
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->with('tax')
            ->whereNotNull('tax_id')
            ->selectRaw('tax_id, SUM(tax_amount) as total_tax, COUNT(*) as transaction_count')
            ->groupBy('tax_id')
            ->get();

        return view('report.tax', compact('taxReport', 'fromDate', 'toDate'));
    }
}
