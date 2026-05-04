<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ExpenseCategory::where('business_id', auth()->user()->business_id)->orderBy('name')->paginate(20);

        return view('expense_category.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        ExpenseCategory::create($request->validate(['name' => 'required|string|max:255', 'code' => 'nullable|string']) + [
            'business_id' => auth()->user()->business_id,
        ]);

        return back()->with('success', 'Kategori biaya berhasil ditambahkan.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->update($request->validate(['name' => 'required|string|max:255', 'code' => 'nullable|string']));

        return back()->with('success', 'Kategori biaya berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->delete();

        return back()->with('success', 'Kategori biaya berhasil dihapus.');
    }
}
