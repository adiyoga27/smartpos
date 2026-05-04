<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::where('business_id', auth()->user()->business_id)->orderBy('actual_name')->paginate(20);

        return view('unit.index', compact('units'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'actual_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'allow_decimal' => 'boolean',
        ]);

        Unit::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validate([
            'actual_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'allow_decimal' => 'boolean',
        ]));

        return back()->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('success', 'Satuan berhasil dihapus.');
    }
}
