<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    /**
     * Display current shift / drawer status or open register form.
     */
    public function index(): View
    {
        $activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $shiftOrders = collect();
        $expectedCash = 0;

        if ($activeRegister) {
            // Live cash sales calculation from orders linked to this register
            $activeRegister->cash_sales = Order::where('cash_register_id', $activeRegister->id)
                ->where('payment_type', 'cash')
                ->sum('paid_amount');
            $activeRegister->save();

            $shiftOrders = Order::with('customer')
                ->where('cash_register_id', $activeRegister->id)
                ->latest()
                ->get();

            $expectedCash = $activeRegister->cash_in_hand + $activeRegister->cash_sales;
        }

        return view('cash_register.index', compact('activeRegister', 'shiftOrders', 'expectedCash'));
    }

    /**
     * Open a new shift register drawer.
     */
    public function open(Request $request): RedirectResponse
    {
        // Check if current user already has an open register
        $existing = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return redirect()->route('cash_register.index')
                ->with('error', 'You already have an open register! Please close your current shift first.');
        }

        $validated = $request->validate([
            'cash_in_hand' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        CashRegister::create([
            'user_id' => auth()->id(),
            'cash_in_hand' => $validated['cash_in_hand'],
            'cash_sales' => 0.00,
            'status' => 'open',
            'opened_at' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('cash_register.index')
            ->with('success', 'Shift register opened successfully with opening float of ৳' . number_format($validated['cash_in_hand'], 2));
    }

    /**
     * Close an active shift register drawer.
     */
    public function close(Request $request, CashRegister $cashRegister): RedirectResponse
    {
        if ($cashRegister->status !== 'open') {
            return redirect()->route('cash_register.index')
                ->with('error', 'This register is already closed.');
        }

        $validated = $request->validate([
            'total_cash_submitted' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        // Recalculate latest cash sales
        $cashSales = Order::where('cash_register_id', $cashRegister->id)
            ->where('payment_type', 'cash')
            ->sum('paid_amount');

        $expectedTotal = $cashRegister->cash_in_hand + $cashSales;
        $submitted = (float) $validated['total_cash_submitted'];
        $difference = $submitted - $expectedTotal;

        $cashRegister->update([
            'cash_sales' => $cashSales,
            'total_cash_submitted' => $submitted,
            'difference' => $difference,
            'status' => 'closed',
            'closed_at' => now(),
            'note' => $validated['note'] ?? $cashRegister->note,
        ]);

        $diffMessage = $difference == 0
            ? 'Cash balanced perfectly.'
            : ($difference > 0
                ? 'Surplus of +৳' . number_format($difference, 2)
                : 'Shortage of -৳' . number_format(abs($difference), 2));

        return redirect()->route('cash_register.history')
            ->with('success', "Shift register closed successfully! ({$diffMessage})");
    }

    /**
     * View history of closed registers / shifts.
     */
    public function history(Request $request): View
    {
        $query = CashRegister::with('user')->where('status', 'closed');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('opened_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('opened_at', '<=', $request->end_date);
        }

        $registers = $query->latest('closed_at')->paginate(15)->withQueryString();
        $staffUsers = User::orderBy('name')->get();

        return view('cash_register.history', compact('registers', 'staffUsers'));
    }
}
