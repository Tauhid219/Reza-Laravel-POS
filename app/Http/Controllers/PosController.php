<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Display POS terminal interface.
     */
    public function index(): View
    {
        $activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $categories = Category::where('status', true)->orderBy('name')->get();
        $products = Product::where('status', true)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get();

        $customers = Customer::where('status', true)
            ->orderBy('is_walk_in', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        $defaultCustomer = $customers->firstWhere('is_walk_in', true) ?? $customers->first();

        return view('pos.index', compact('activeRegister', 'categories', 'products', 'customers', 'defaultCustomer'));
    }

    /**
     * Live search products by name or barcode (JSON for POS).
     */
    public function search(Request $request): JsonResponse
    {
        $term = $request->get('query', '');

        $products = Product::where('status', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('code', 'like', "%{$term}%");
            })
            ->with('unit')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    /**
     * Process POS sale checkout.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,card,mobile_banking,credit,split',
            'note' => 'nullable|string|max:500',
            'payments' => 'nullable|array',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        // Active Cash Register
        $activeRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $order = DB::transaction(function () use ($validated, $activeRegister) {
            // 1. Validate Stock Availability & Calculate Cost (COGS)
            $totalCost = 0;
            $itemsToProcess = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => ['Selected product not found.'],
                    ]);
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for '{$product->name}'. Available: {$product->stock_quantity}, Requested: {$item['quantity']}."],
                    ]);
                }

                $totalCost += ($product->cost_price * $item['quantity']);
                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit_cost' => $product->cost_price,
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0),
                ];
            }

            // 2. Financial Calculations
            $totalAmount = (float) $validated['total_amount'];
            $paidAmount = (float) $validated['paid_amount'];
            $changeAmount = max(0, $paidAmount - $totalAmount);
            $dueAmount = max(0, $totalAmount - $paidAmount);

            if ($dueAmount == 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'due';
            }

            $invoiceNo = 'INV-' . date('Ymd') . '-' . mt_rand(1000, 9999);

            // 3. Create Order
            $order = Order::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'cash_register_id' => $activeRegister?->id,
                'order_date' => now(),
                'total_items' => count($itemsToProcess),
                'subtotal' => $validated['subtotal'],
                'discount_type' => $validated['discount_type'] ?? 'fixed',
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_percentage' => $validated['tax_percentage'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'paid_amount' => min($paidAmount, $totalAmount),
                'due_amount' => $dueAmount,
                'change_amount' => $changeAmount,
                'payment_type' => $validated['payment_type'],
                'payment_status' => $paymentStatus,
                'order_status' => 'completed',
                'note' => $validated['note'] ?? null,
            ]);

            // 4. Create Order Items & Decrement Inventory Stock
            foreach ($itemsToProcess as $itemData) {
                $order->items()->create([
                    'product_id' => $itemData['product']->id,
                    'product_name' => $itemData['product']->name,
                    'unit_cost' => $itemData['unit_cost'],
                    'unit_price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'discount' => $itemData['discount'],
                    'subtotal' => $itemData['subtotal'],
                ]);

                $itemData['product']->decrement('stock_quantity', $itemData['quantity']);
            }

            // 5. Update Customer Due Balance if credit/partial
            if ($dueAmount > 0) {
                $customer = Customer::find($validated['customer_id']);
                if ($customer && !$customer->is_walk_in) {
                    $customer->increment('total_due', $dueAmount);
                }
            }

            // 6. Record Payments
            if (!empty($validated['payments'])) {
                foreach ($validated['payments'] as $p) {
                    if ($p['amount'] > 0) {
                        $order->payments()->create([
                            'user_id' => auth()->id(),
                            'amount' => $p['amount'],
                            'payment_method' => $p['method'],
                            'payment_date' => now(),
                        ]);

                        // If cash and register active, increment register cash sales
                        if (strtolower($p['method']) === 'cash' && $activeRegister) {
                            $activeRegister->increment('cash_sales', $p['amount']);
                        }
                    }
                }
            } else {
                // Single payment fallback
                $actualPaid = min($paidAmount, $totalAmount);
                if ($actualPaid > 0) {
                    $order->payments()->create([
                        'user_id' => auth()->id(),
                        'amount' => $actualPaid,
                        'payment_method' => $validated['payment_type'],
                        'payment_date' => now(),
                    ]);

                    if ($validated['payment_type'] === 'cash' && $activeRegister) {
                        $activeRegister->increment('cash_sales', $actualPaid);
                    }
                }
            }

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sale completed successfully!',
            'order_id' => $order->id,
            'invoice_no' => $order->invoice_no,
            'total_amount' => $order->total_amount,
            'paid_amount' => $order->paid_amount,
            'change_amount' => $order->change_amount,
            'due_amount' => $order->due_amount,
            'receipt_url' => route('orders.receipt', $order),
            'invoice_url' => route('orders.invoice', $order),
        ], 201);
    }
}
