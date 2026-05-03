<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::where('business_id', auth()->user()->business_id)
            ->where('type', 'expense')
            ->with(['expenseCategory', 'contact'])
            ->latest('transaction_date');

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $expenses = $query->paginate(20);
        $categories = ExpenseCategory::where('business_id', auth()->user()->business_id)->get();

        return view('expense.index', compact('expenses', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_for' => 'nullable|exists:contacts,id',
            'ref_no' => 'nullable|string',
            'final_total' => 'required|numeric|min:0',
            'additional_notes' => 'nullable|string',
            'document' => 'nullable|string',
        ]);

        Transaction::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'type' => 'expense',
            'status' => 'final',
            'payment_status' => 'paid',
            'transaction_date' => now(),
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Biaya berhasil dicatat.');
    }

    public function update(Request $request, Transaction $expense): RedirectResponse
    {
        $expense->update($request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'final_total' => 'required|numeric|min:0',
            'additional_notes' => 'nullable|string',
        ]));

        return back()->with('success', 'Biaya berhasil diperbarui.');
    }

    public function destroy(Transaction $expense): RedirectResponse
    {
        $expense->delete();

        return back()->with('success', 'Biaya berhasil dihapus.');
    }
}
