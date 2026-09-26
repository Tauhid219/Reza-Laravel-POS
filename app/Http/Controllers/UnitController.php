<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index(): View
    {
        $units = Unit::withCount('products')
            ->latest()
            ->paginate(15);

        return view('units.index', compact('units'));
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name',
            'short_name' => 'required|string|max:20|unique:units,short_name',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully!');
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,' . $unit->id,
            'short_name' => 'required|string|max:20|unique:units,short_name,' . $unit->id,
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully!');
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'Cannot delete unit that is associated with products!');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully!');
    }
}
