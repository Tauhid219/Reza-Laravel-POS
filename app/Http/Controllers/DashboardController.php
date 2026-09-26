<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display POS Dashboard with dynamic business metrics.
     */
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Sales Metrics
        $todaySales = Order::whereDate('order_date', $today)->sum('total_amount');
        $todayOrdersCount = Order::whereDate('order_date', $today)->count();
        $monthlyRevenue = Order::whereYear('order_date', $thisYear)
            ->whereMonth('order_date', $thisMonth)
            ->sum('total_amount');

        // Customer Dues
        $totalCustomerDue = Customer::sum('total_due');
        $dueInvoicesCount = Order::where('payment_status', '!=', 'paid')->count();

        // Active Cash Register for Current Logged-in User
        $activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        // Low stock products
        $lowStockProducts = Product::with(['category', 'unit'])
            ->whereRaw('stock_quantity <= alert_quantity')
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

        // Recent Orders
        $recentOrders = Order::with(['customer', 'user'])
            ->latest('order_date')
            ->take(5)
            ->get();

        // Quick Stats
        $totalProductsCount = Product::count();
        $totalCustomersCount = Customer::where('is_walk_in', false)->count();

        return view('dashboard', compact(
            'todaySales',
            'todayOrdersCount',
            'monthlyRevenue',
            'totalCustomerDue',
            'dueInvoicesCount',
            'activeRegister',
            'lowStockProducts',
            'recentOrders',
            'totalProductsCount',
            'totalCustomersCount'
        ));
    }
}
