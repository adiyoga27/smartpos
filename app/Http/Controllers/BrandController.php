<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::where('business_id', auth()->user()->business_id)->orderBy('name')->paginate(20);

        return view('brand.index', compact('brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);

        Brand::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'Merek berhasil ditambahkan.');
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $brand->update($request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']));

        return back()->with('success', 'Merek berhasil diperbarui.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return back()->with('success', 'Merek berhasil dihapus.');
    }
}
