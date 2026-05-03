<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\InvoiceLayout;
use App\Models\InvoiceScheme;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function business(): View
    {
        $business = Business::where('id', auth()->user()->business_id)->first();

        return view('settings.business', compact('business'));
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tax_number_1' => 'nullable|string',
            'time_zone' => 'nullable|string',
            'date_format' => 'nullable|string',
            'time_format' => 'nullable|in:12,24',
            'theme_color' => 'nullable|string',
            'sell_price_tax' => 'nullable|in:includes,excludes',
        ]);

        Business::where('id', auth()->user()->business_id)->update($data);

        return back()->with('success', 'Pengaturan bisnis berhasil diperbarui.');
    }

    public function locations(): View
    {
        $locations = BusinessLocation::where('business_id', auth()->user()->business_id)->get();

        return view('settings.locations', compact('locations'));
    }

    public function updateLocation(Request $request, BusinessLocation $location): RedirectResponse
    {
        $location->update($request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
        ]));

        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function invoiceLayouts(): View
    {
        $layouts = InvoiceLayout::where('business_id', auth()->user()->business_id)->get();
        $schemes = InvoiceScheme::where('business_id', auth()->user()->business_id)->get();

        return view('settings.invoice_layouts', compact('layouts', 'schemes'));
    }

    public function system(): View
    {
        return view('settings.system');
    }

    public function updateSystem(Request $request): RedirectResponse
    {
        foreach ($request->except('_token') as $key => $value) {
            SystemSetting::setValue($key, $value);
        }

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
