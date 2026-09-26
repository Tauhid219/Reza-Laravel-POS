<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Sales Report with date filters, KPI aggregates, daily breakdown & payment breakdown.
     */
    public function sales(Request $request): View
    {
        $dateFilter = $request->get('date_range', 'this_month');
        [$startDate, $endDate] = $this->resolveDateRange($dateFilter, $request->start_date, $request->end_date);

        $query = Order::whereBetween('order_date', [$startDate, $endDate])
            ->where('order_status', 'completed');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Summary Aggregates
        $totalOrders = (clone $query)->count();
        $totalSales = (clone $query)->sum('total_amount');
        $totalCost = (clone $query)->sum('total_cost');
        $totalPaid = (clone $query)->sum('paid_amount');
        $totalDue = (clone $query)->sum('due_amount');
        $totalTax = (clone $query)->sum('tax_amount');
        $totalGrossProfit = $totalSales - $totalCost;

        // Daily Breakdown
        $dailyTrends = (clone $query)
            ->selectRaw('DATE(order_date) as date, COUNT(*) as orders_count, SUM(total_amount) as sales, SUM(total_cost) as cost, SUM(paid_amount) as paid, SUM(due_amount) as due')
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date', 'desc')
            ->get();

        // Payment Method Breakdown
        $paymentMethods = (clone $query)
            ->selectRaw('payment_type, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_type')
            ->get();

        // Recent Orders in the filter range
        $orders = (clone $query)
            ->with(['customer', 'user'])
            ->latest('order_date')
            ->paginate(20)
            ->withQueryString();

        $cashiers = User::orderBy('name')->get();

        return view('reports.sales', compact(
            'orders',
            'totalOrders',
            'totalSales',
            'totalCost',
            'totalGrossProfit',
            'totalPaid',
            'totalDue',
            'totalTax',
            'dailyTrends',
            'paymentMethods',
            'cashiers',
            'startDate',
            'endDate',
            'dateFilter'
        ));
    }

    /**
     * Profit & Loss Report with COGS, gross margins, and category profitability.
     */
    public function profitLoss(Request $request): View
    {
        $dateFilter = $request->get('date_range', 'this_month');
        [$startDate, $endDate] = $this->resolveDateRange($dateFilter, $request->start_date, $request->end_date);

        $query = Order::whereBetween('order_date', [$startDate, $endDate])
            ->where('order_status', 'completed');

        $totalSales = (clone $query)->sum('total_amount');
        $totalCost = (clone $query)->sum('total_cost');
        $totalGrossProfit = $totalSales - $totalCost;
        $profitMargin = $totalSales > 0 ? ($totalGrossProfit / $totalSales) * 100 : 0;
        $totalDiscounts = (clone $query)->sum(DB::raw("CASE WHEN discount_type = 'percentage' THEN (subtotal * discount_amount / 100) ELSE discount_amount END"));
        $totalTax = (clone $query)->sum('tax_amount');
        $ordersCount = (clone $query)->count();

        // Category-wise Profitability Breakdown
        $categoryBreakdown = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->where('orders.order_status', 'completed')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_sales'),
                DB::raw('SUM(order_items.unit_cost * order_items.quantity) as total_cost'),
                DB::raw('SUM(order_items.subtotal - (order_items.unit_cost * order_items.quantity)) as gross_profit')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('gross_profit')
            ->get();

        // Top 10 Most Profitable Products
        $topProfitableProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->where('orders.order_status', 'completed')
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(order_items.unit_cost * order_items.quantity) as total_cost'),
                DB::raw('SUM(order_items.subtotal - (order_items.unit_cost * order_items.quantity)) as profit')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('profit')
            ->limit(10)
            ->get();

        return view('reports.profit_loss', compact(
            'totalSales',
            'totalCost',
            'totalGrossProfit',
            'profitMargin',
            'totalDiscounts',
            'totalTax',
            'ordersCount',
            'categoryBreakdown',
            'topProfitableProducts',
            'startDate',
            'endDate',
            'dateFilter'
        ));
    }

    /**
     * Stock & Inventory Valuation Report with Low Stock Alerts.
     */
    public function stock(Request $request): View
    {
        $query = Product::with(['category', 'unit'])->where('status', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low_stock') {
                $query->whereColumn('stock_quantity', '<=', 'alert_quantity')
                      ->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereColumn('stock_quantity', '>', 'alert_quantity');
            }
        }

        // Inventory Valuation Aggregates
        $totalProducts = Product::where('status', true)->count();
        $totalStockQty = Product::where('status', true)->sum('stock_quantity');
        $totalCostValuation = Product::where('status', true)->sum(DB::raw('stock_quantity * cost_price'));
        $totalRetailValuation = Product::where('status', true)->sum(DB::raw('stock_quantity * selling_price'));
        $projectedGrossProfit = $totalRetailValuation - $totalCostValuation;
        $lowStockCount = Product::where('status', true)
            ->whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->where('stock_quantity', '>', 0)
            ->count();
        $outOfStockCount = Product::where('status', true)
            ->where('stock_quantity', '<=', 0)
            ->count();

        $products = $query->orderBy('stock_quantity', 'asc')->paginate(25)->withQueryString();
        $categories = Category::where('status', true)->orderBy('name')->get();

        return view('reports.stock', compact(
            'products',
            'categories',
            'totalProducts',
            'totalStockQty',
            'totalCostValuation',
            'totalRetailValuation',
            'projectedGrossProfit',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Customer Outstanding Due & Receivables Report.
     */
    public function customerDue(Request $request): View
    {
        $query = Customer::where('total_due', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $totalOutstandingDue = Customer::sum('total_due');
        $totalDueCustomers = Customer::where('total_due', '>', 0)->count();

        $customers = $query->orderByDesc('total_due')->paginate(20)->withQueryString();

        return view('reports.customer_due', compact('customers', 'totalOutstandingDue', 'totalDueCustomers'));
    }

    /**
     * Helper to resolve Date Range into [Carbon $startDate, Carbon $endDate].
     */
    private function resolveDateRange(string $preset, ?string $customStart, ?string $customEnd): array
    {
        if ($preset === 'custom' && $customStart && $customEnd) {
            return [
                Carbon::parse($customStart)->startOfDay(),
                Carbon::parse($customEnd)->endOfDay(),
            ];
        }

        return match ($preset) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()], // 'this_month'
        };
    }
}
