<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CustomerGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $query = Contact::where('business_id', auth()->user()->business_id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('mobile', 'like', '%'.$request->search.'%')
                    ->orWhere('supplier_business_name', 'like', '%'.$request->search.'%');
            });
        }

        $contacts = $query->latest()->paginate(20);
        $customerGroups = CustomerGroup::where('business_id', auth()->user()->business_id)->get();

        return view('contact.index', compact('contacts', 'customerGroups'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:customer,supplier,both',
            'first_name' => 'nullable|string|max:255',
            'supplier_business_name' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'contact_id' => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
        ]);

        $contact = Contact::create(array_merge($data, [
            'business_id' => auth()->user()->business_id,
            'created_by' => auth()->id(),
        ]));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan.',
                'contact' => [
                    'id' => $contact->id,
                    'full_name' => $contact->full_name,
                    'contact_id' => $contact->contact_id,
                ],
            ]);
        }

        return back()->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $contacts = Contact::where('business_id', auth()->user()->business_id)
            ->customers()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('mobile', 'like', "%{$query}%")
                    ->orWhere('contact_id', 'like', "%{$query}%");
            })
            ->orderBy('first_name')
            ->limit(15)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'mobile' => $c->mobile,
                'contact_id' => $c->contact_id,
            ]);

        return response()->json($contacts);
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $contact->update($request->validate([
            'type' => 'required|in:customer,supplier,both',
            'first_name' => 'nullable|string|max:255',
            'supplier_business_name' => 'nullable|string|max:255',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'address_line_1' => 'nullable|string',
            'city' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
        ]));

        return back()->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return back()->with('success', 'Kontak berhasil dihapus.');
    }
}
