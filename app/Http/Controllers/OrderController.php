<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;

class OrderController extends Controller
{
    /**
     * Display a listing of sales orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        $orders = $query->latest('order_date')->paginate(15)->withQueryString();
        $totalSalesSum = Order::sum('total_amount');
        $totalDueSum = Order::sum('due_amount');

        return view('orders.index', compact('orders', 'totalSalesSum', 'totalDueSum'));
    }

    /**
     * Display the specified sales order details.
     */
    public function show(Order $order): View
    {
        $order->load(['customer', 'user', 'cashRegister', 'items.product', 'payments']);

        return view('orders.show', compact('order'));
    }

    /**
     * 80mm / 58mm Thermal POS Receipt View.
     */
    public function receipt(Order $order): View
    {
        $order->load(['customer', 'user', 'items', 'payments']);

        $generator = new BarcodeGeneratorSVG();
        $barcodeSvg = $generator->getBarcode($order->invoice_no, BarcodeGeneratorSVG::TYPE_CODE_128, 1.8, 38);

        return view('orders.receipt', compact('order', 'barcodeSvg'));
    }

    /**
     * A4 Standard Printable Invoice View.
     */
    public function invoice(Order $order): View
    {
        $order->load(['customer', 'user', 'items', 'payments']);

        $generator = new BarcodeGeneratorSVG();
        $barcodeSvg = $generator->getBarcode($order->invoice_no, BarcodeGeneratorSVG::TYPE_CODE_128, 2, 45);

        return view('orders.invoice', compact('order', 'barcodeSvg'));
    }

    /**
     * Display listing of due / credit sales orders.
     */
    public function dueOrders(Request $request): View
    {
        $query = Order::where('due_amount', '>', 0)->with(['customer', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest('order_date')->paginate(15)->withQueryString();
        $totalOutstandingDue = Order::sum('due_amount');

        return view('orders.due', compact('orders', 'totalOutstandingDue'));
    }
}
