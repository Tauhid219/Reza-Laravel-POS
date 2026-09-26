<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index(Request $request): View
    {
        $query = Purchase::with(['supplier', 'user', 'items.product']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        $purchases = $query->latest('purchase_date')->paginate(15)->withQueryString();
        $suppliers = Supplier::where('status', true)->orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create(): View
    {
        $suppliers = Supplier::where('status', true)->orderBy('name')->get();
        $products = Product::where('status', true)->with('unit')->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $purchase = DB::transaction(function () use ($validated, $request) {
            // Generate Unique Purchase Number
            $purchaseNo = 'PUR-' . date('Ymd') . '-' . mt_rand(1000, 9999);

            // Calculate Subtotals
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += ($item['cost_price'] * $item['quantity']);
            }

            $paidAmount = min((float) $validated['paid_amount'], $totalAmount);
            $dueAmount = max(0, $totalAmount - $paidAmount);

            if ($dueAmount == 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'due';
            }

            // Create Purchase Header
            $purchase = Purchase::create([
                'purchase_no' => $purchaseNo,
                'supplier_id' => $validated['supplier_id'],
                'user_id' => auth()->id(),
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'status' => 'received',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create Purchase Items and Update Stock & Cost Price
            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['cost_price'] * $itemData['quantity'];

                $purchase->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'cost_price' => $itemData['cost_price'],
                    'subtotal' => $subtotal,
                ]);

                // Increment Product Stock and update latest Cost Price
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->increment('stock_quantity', $itemData['quantity']);
                    $product->cost_price = $itemData['cost_price'];
                    $product->save();
                }
            }

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase recorded successfully! Product stock levels have been updated.');
    }

    /**
     * Display the specified purchase invoice.
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product.unit']);

        return view('purchases.show', compact('purchase'));
    }
}
