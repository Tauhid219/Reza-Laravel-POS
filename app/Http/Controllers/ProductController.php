<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filtering & search.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit']);

        // Search by keyword (Name or Code)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Stock Status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low_stock') {
                $query->whereRaw('stock_quantity <= alert_quantity AND stock_quantity > 0');
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('status', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suggestedCode = $this->generateUniqueBarcode();

        return view('products.create', compact('categories', 'units', 'suggestedCode'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:products,code',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_quantity' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_quantity' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && str_starts_with($product->image, 'storage/')) {
                $oldFile = str_replace('storage/', '', $product->image);
                Storage::disk('public')->delete($oldFile);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Check if product is part of any orders
        if ($product->orderItems()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete this product because it has sales history! You can set its status to Inactive instead.');
        }

        if ($product->image && str_starts_with($product->image, 'storage/')) {
            $oldFile = str_replace('storage/', '', $product->image);
            Storage::disk('public')->delete($oldFile);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Print or Preview Barcode labels for a product.
     */
    public function printBarcode(Request $request, ?Product $product = null): View
    {
        $products = Product::where('status', true)->orderBy('name')->get();
        $selectedProduct = $product ?? $products->first();
        $quantity = (int) $request->get('quantity', 12);

        $barcodeSvg = null;
        if ($selectedProduct) {
            $generator = new BarcodeGeneratorSVG();
            $barcodeSvg = $generator->getBarcode($selectedProduct->code, BarcodeGeneratorSVG::TYPE_CODE_128, 2, 45);
        }

        return view('products.barcode', compact('products', 'selectedProduct', 'quantity', 'barcodeSvg'));
    }

    /**
     * Helper to generate unique numeric barcode.
     */
    private function generateUniqueBarcode(): string
    {
        do {
            $code = '894' . mt_rand(1000000, 9999999);
        } while (Product::where('code', $code)->exists());

        return $code;
    }
}
