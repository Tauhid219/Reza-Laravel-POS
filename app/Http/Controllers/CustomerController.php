<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request): View
    {
        $query = Customer::query()->withCount('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('has_due') && $request->has_due === 'yes') {
            $query->where('total_due', '>', 0);
        }

        $customers = $query->orderBy('is_walk_in', 'desc')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalDueSum = Customer::sum('total_due');
        $totalCustomersCount = Customer::where('is_walk_in', false)->count();

        return view('customers.index', compact('customers', 'totalDueSum', 'totalCustomersCount'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30|unique:customers,phone',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['is_walk_in'] = false;
        $validated['total_due'] = 0.00;

        $customer = Customer::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully!',
                'customer' => $customer,
            ], 201);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'status' => 'required|boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->is_walk_in) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete the default Walk-in customer!');
        }

        if ($customer->orders()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete this customer because they have existing order history! You can set status to Inactive instead.');
        }

        if ($customer->total_due > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete a customer with outstanding due balance!');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Customer Due Ledger page.
     */
    public function ledger(Request $request): View
    {
        $query = Customer::where('total_due', '>', 0)->withCount('orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $dueCustomers = $query->orderBy('total_due', 'desc')->paginate(15)->withQueryString();
        $totalOutstandingDue = Customer::sum('total_due');

        return view('customers.ledger', compact('dueCustomers', 'totalOutstandingDue'));
    }

    /**
     * Collect / Pay down customer due balance.
     */
    public function collectDue(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $customer->total_due,
            'payment_method' => 'required|string',
            'note' => 'nullable|string|max:255',
        ]);

        $customer->decrement('total_due', $validated['amount']);

        return redirect()->back()
            ->with('success', 'Payment of ৳' . number_format($validated['amount'], 2) . ' collected successfully from ' . $customer->name);
    }
}
