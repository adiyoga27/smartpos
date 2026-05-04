<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerGroupController extends Controller
{
    public function index(): View
    {
        $customerGroups = CustomerGroup::where('business_id', auth()->user()->business_id)->paginate(20);

        return view('customer_group.index', compact('customerGroups'));
    }

    public function store(Request $request): RedirectResponse
    {
        CustomerGroup::create($request->validate(['name' => 'required|string|max:255', 'amount' => 'required|numeric']) + [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Grup pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, CustomerGroup $customerGroup): RedirectResponse
    {
        $customerGroup->update($request->validate(['name' => 'required|string|max:255', 'amount' => 'required|numeric']));

        return back()->with('success', 'Grup pelanggan berhasil diperbarui.');
    }

    public function destroy(CustomerGroup $customerGroup): RedirectResponse
    {
        $customerGroup->delete();

        return back()->with('success', 'Grup pelanggan berhasil dihapus.');
    }
}
