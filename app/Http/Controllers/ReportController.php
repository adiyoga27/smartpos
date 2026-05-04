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

        $rawDaily = Transaction::where('business_id', auth()->user()->business_id)
            ->whereIn('type', ['sell', 'purchase', 'expense'])
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->selectRaw("DATE_FORMAT(transaction_date, '%Y-%m-%d') as date, type, SUM(final_total) as total")
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $dailyData = $rawDaily->map(function ($rows, $date) {
            return [
                'date' => $date,
                'sales' => (float) $rows->where('type', 'sell')->sum('total'),
                'purchases' => (float) $rows->where('type', 'purchase')->sum('total'),
                'expenses' => (float) $rows->where('type', 'expense')->sum('total'),
                'profit' => (float) ($rows->where('type', 'sell')->sum('total')
                    - $rows->where('type', 'purchase')->sum('total')
                    - $rows->where('type', 'expense')->sum('total')),
            ];
        })->values();

        return view('report.profit_loss', compact(
            'totalSales', 'totalPurchases', 'totalExpenses',
            'grossProfit', 'netProfit', 'dailyData', 'fromDate', 'toDate'
        ));
    }

    public function tax(Request $request): View
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $taxData = Transaction::where('business_id', auth()->user()->business_id)
            ->whereIn('type', ['sell', 'purchase'])
            ->whereBetween('transaction_date', [$fromDate, $toDate.' 23:59:59'])
            ->whereNotNull('tax_id')
            ->with('tax')
            ->get()
            ->groupBy('tax_id')
            ->map(function ($transactions) {
                $tax = $transactions->first()->tax;

                return [
                    'name' => $tax?->name ?? 'Unknown',
                    'transaction_count' => $transactions->count(),
                    'total_tax_amount' => $transactions->sum('tax_amount'),
                ];
            })
            ->values();

        $totalTransactionCount = $taxData->sum('transaction_count');
        $totalTaxAmount = $taxData->sum('total_tax_amount');

        return view('report.tax', compact('taxData', 'totalTransactionCount', 'totalTaxAmount', 'fromDate', 'toDate'));
    }
}
