<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $accounts = Account::where('business_id', auth()->user()->business_id)
            ->with('accountType')
            ->orderBy('name')
            ->paginate(20);

        return view('account.index', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        Account::create($request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string',
            'account_type_id' => 'nullable|exists:account_types,id',
            'note' => 'nullable|string',
        ]) + [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $account->update($request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string',
            'account_type_id' => 'nullable|exists:account_types,id',
            'note' => 'nullable|string',
        ]));

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $account->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    public function transactions(Request $request): View
    {
        $query = AccountTransaction::whereHas('account', fn ($q) => $q->where('business_id', auth()->user()->business_id))
            ->with('account')
            ->latest('operation_date');

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        $transactions = $query->paginate(20);
        $accounts = Account::where('business_id', auth()->user()->business_id)->get();

        return view('account.transactions', compact('transactions', 'accounts'));
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        AccountTransaction::create($request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]) + [
            'operation_date' => now(),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Transaksi akun berhasil dicatat.');
    }

    public function updateTransaction(Request $request, AccountTransaction $transaction): RedirectResponse
    {
        $transaction->update($request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]));

        return back()->with('success', 'Transaksi akun berhasil diperbarui.');
    }

    public function destroyTransaction(AccountTransaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return back()->with('success', 'Transaksi akun berhasil dihapus.');
    }
}
