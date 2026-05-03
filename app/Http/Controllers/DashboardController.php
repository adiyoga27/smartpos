<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $businessId = auth()->user()->business_id;

        $todaySales = Transaction::where('business_id', $businessId)
            ->where('type', 'sell')
            ->whereDate('transaction_date', today())
            ->sum('final_total');

        $todayPurchases = Transaction::where('business_id', $businessId)
            ->where('type', 'purchase')
            ->whereDate('transaction_date', today())
            ->sum('final_total');

        $todayExpenses = Transaction::where('business_id', $businessId)
            ->where('type', 'expense')
            ->whereDate('transaction_date', today())
            ->sum('final_total');

        $monthSales = Transaction::where('business_id', $businessId)
            ->where('type', 'sell')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('final_total');

        $monthPurchases = Transaction::where('business_id', $businessId)
            ->where('type', 'purchase')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('final_total');

        $totalProducts = Product::where('business_id', $businessId)->count();
        $totalCustomers = Contact::where('business_id', $businessId)->customers()->count();
        $totalSuppliers = Contact::where('business_id', $businessId)->suppliers()->count();

        $recentSales = Transaction::with('contact')
            ->where('business_id', $businessId)
            ->where('type', 'sell')
            ->latest()
            ->take(5)
            ->get();

        $salesByDay = Transaction::where('business_id', $businessId)
            ->where('type', 'sell')
            ->whereDate('transaction_date', '>=', now()->subDays(7))
            ->selectRaw('DATE(transaction_date) as date, SUM(final_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $salesByDay->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('d M'));
        $chartData = $salesByDay->pluck('total');

        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.business_id', $businessId)
            ->where('transactions.type', 'sell')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->selectRaw('products.name, SUM(transaction_items.quantity) as qty, SUM(transaction_items.quantity * transaction_items.unit_price) as total')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('home.index', compact(
            'todaySales', 'todayPurchases', 'todayExpenses',
            'monthSales', 'monthPurchases',
            'totalProducts', 'totalCustomers', 'totalSuppliers',
            'recentSales', 'chartLabels', 'chartData', 'topProducts'
        ));
    }
}
