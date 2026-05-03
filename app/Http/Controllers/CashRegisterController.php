<?php

namespace App\Http\Controllers;

use App\Models\BusinessLocation;
use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function index(): View
    {
        $register = CashRegister::where('business_id', auth()->user()->business_id)
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        $locations = BusinessLocation::where('business_id', auth()->user()->business_id)->get();
        $history = CashRegister::where('business_id', auth()->user()->business_id)
            ->latest()
            ->take(10)
            ->get();

        return view('cash_register.index', compact('register', 'locations', 'history'));
    }

    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'location_id' => 'required|exists:business_locations,id',
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $existing = CashRegister::where('business_id', auth()->user()->business_id)
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda masih memiliki kas register yang terbuka.');
        }

        $register = CashRegister::create([
            'business_id' => auth()->user()->business_id,
            'location_id' => $request->location_id,
            'user_id' => auth()->id(),
            'status' => 'open',
            'opening_amount' => $request->opening_amount,
        ]);

        CashRegisterTransaction::create([
            'cash_register_id' => $register->id,
            'amount' => $request->opening_amount,
            'type' => 'credit',
            'transaction_type' => 'initial',
        ]);

        return back()->with('success', 'Kas register berhasil dibuka.');
    }

    public function close(Request $request): RedirectResponse
    {
        $register = CashRegister::where('business_id', auth()->user()->business_id)
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->firstOrFail();

        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'closing_note' => 'nullable|string',
            'denom_100000' => 'nullable|integer',
            'denom_50000' => 'nullable|integer',
            'denom_20000' => 'nullable|integer',
            'denom_10000' => 'nullable|integer',
            'denom_5000' => 'nullable|integer',
            'denom_2000' => 'nullable|integer',
            'denom_1000' => 'nullable|integer',
            'denom_500' => 'nullable|integer',
            'denom_100' => 'nullable|integer',
        ]);

        $salesTotal = CashRegisterTransaction::where('cash_register_id', $register->id)
            ->where('transaction_type', 'sell')
            ->sum('amount');

        $register->update([
            'status' => 'close',
            'closed_at' => now(),
            'closing_amount' => $request->closing_amount,
            'closing_note' => $request->closing_note,
            'total_cash' => $request->closing_amount,
            'denom_100000' => $request->denom_100000 ?? 0,
            'denom_50000' => $request->denom_50000 ?? 0,
            'denom_20000' => $request->denom_20000 ?? 0,
            'denom_10000' => $request->denom_10000 ?? 0,
            'denom_5000' => $request->denom_5000 ?? 0,
            'denom_2000' => $request->denom_2000 ?? 0,
            'denom_1000' => $request->denom_1000 ?? 0,
            'denom_500' => $request->denom_500 ?? 0,
            'denom_100' => $request->denom_100 ?? 0,
        ]);

        return redirect()->route('cash-register.index')->with('success', 'Kas register berhasil ditutup.');
    }
}
