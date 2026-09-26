<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(): View
    {
        $suppliers = Supplier::withCount('purchases')
            ->latest()
            ->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:30|unique:suppliers,phone',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:30|unique:suppliers,phone,' . $supplier->id,
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'status' => 'required|boolean',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete this supplier because there are purchase records linked to it!');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
}
