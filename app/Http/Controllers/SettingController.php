<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): View
    {
        $settings = Setting::getAll();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update store and POS settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:100',
            'company_address' => 'nullable|string|max:500',
            'vat_number' => 'nullable|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'currency_code' => 'required|string|max:10',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'invoice_prefix' => 'nullable|string|max:20',
            'receipt_footer' => 'nullable|string|max:500',
            'invoice_terms' => 'nullable|string|max:1000',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        // Handle Logo Upload
        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/settings');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Remove old logo if exists
            $oldLogo = Setting::get('company_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }

            $file->move($destinationPath, $filename);
            Setting::set('company_logo', 'uploads/settings/' . $filename, 'company');
        }

        // Store Settings
        $fields = [
            'company_name' => 'company',
            'company_phone' => 'company',
            'company_email' => 'company',
            'company_address' => 'company',
            'vat_number' => 'company',
            'currency_symbol' => 'billing',
            'currency_code' => 'billing',
            'default_tax_rate' => 'billing',
            'invoice_prefix' => 'billing',
            'receipt_footer' => 'pos',
            'invoice_terms' => 'pos',
        ];

        foreach ($fields as $key => $group) {
            if (array_key_exists($key, $validated)) {
                Setting::set($key, $validated[$key] ?? '', $group);
            }
        }

        Setting::clearCache();

        return redirect()->route('settings.index')->with('success', 'Store settings updated successfully!');
    }
}
