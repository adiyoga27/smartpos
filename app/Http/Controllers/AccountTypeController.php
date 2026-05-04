<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountTypeController extends Controller
{
    public function index(): View
    {
        $accountTypes = AccountType::where('business_id', auth()->user()->business_id)
            ->with('parent')
            ->orderBy('name')
            ->paginate(20);

        return view('account_type.index', compact('accountTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        AccountType::create($request->validate(['name' => 'required|string|max:255', 'parent_account_type_id' => 'nullable|exists:account_types,id']) + [
            'business_id' => auth()->user()->business_id,
        ]);

        return back()->with('success', 'Tipe akun berhasil ditambahkan.');
    }

    public function update(Request $request, AccountType $accountType): RedirectResponse
    {
        $accountType->update($request->validate(['name' => 'required|string|max:255']));

        return back()->with('success', 'Tipe akun berhasil diperbarui.');
    }

    public function destroy(AccountType $accountType): RedirectResponse
    {
        $accountType->delete();

        return back()->with('success', 'Tipe akun berhasil dihapus.');
    }
}
