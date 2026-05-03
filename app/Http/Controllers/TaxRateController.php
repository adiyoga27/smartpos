<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxRateController extends Controller
{
    public function index(): View
    {
        $taxRates = TaxRate::where('business_id', auth()->user()->business_id)->orderBy('name')->get();

        return view('tax_rate.index', compact('taxRates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'amount' => 'required|numeric']);

        TaxRate::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Tarif pajak berhasil ditambahkan.');
    }

    public function update(Request $request, TaxRate $taxRate): RedirectResponse
    {
        $taxRate->update($request->validate(['name' => 'required|string|max:255', 'amount' => 'required|numeric']));

        return back()->with('success', 'Tarif pajak berhasil diperbarui.');
    }

    public function destroy(TaxRate $taxRate): RedirectResponse
    {
        $taxRate->delete();

        return back()->with('success', 'Tarif pajak berhasil dihapus.');
    }
}
