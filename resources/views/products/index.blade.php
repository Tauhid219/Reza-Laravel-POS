@extends('layouts.admin')

@section('title', 'Products Catalog')
@section('page-title', 'Products Catalog')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/products') }}">Inventory</a></li>
  <li class="breadcrumb-item active">Products</li>
@endsection

@section('page-actions')
  <div class="d-flex gap-2">
    <a href="{{ route('products.barcode') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
      <i class="bi bi-upc-scan"></i>
      <span>Print Barcodes</span>
    </a>
    <a href="{{ route('products.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
      <i class="bi bi-plus-circle"></i>
      <span>Add Product</span>
    </a>
  </div>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 pt-3 pb-3 mb-2 border-bottom align-items-end">
          <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted">Search Product</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Search by name or barcode..." value="{{ request('search') }}">
            </div>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Category</label>
            <select name="category_id" class="form-select">
              <option value="">All Categories</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Stock Status</label>
            <select name="stock_status" class="form-select">
              <option value="">All Stock Levels</option>
              <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock (>0)</option>
              <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock (≤ Alert Qty)</option>
              <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
            </select>
          </div>

          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
              <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                <i class="bi bi-x-lg"></i>
              </a>
            @endif
          </div>
        </form>

        <!-- Product Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 50px;">Image</th>
                <th scope="col">Product & Code</th>
                <th scope="col">Category</th>
                <th scope="col" class="text-end">Cost Price</th>
                <th scope="col" class="text-end">Selling Price</th>
                <th scope="col" class="text-center">Stock</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
                <tr>
                  <td>
                    @if($product->image)
                      <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                    @else
                      <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width: 44px; height: 44px;">
                        <i class="bi bi-image fs-5"></i>
                      </div>
                    @endif
                  </td>

                  <td>
                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                      <i class="bi bi-upc"></i>
                      <code>{{ $product->code }}</code>
                    </div>
                  </td>

                  <td>
                    <span class="badge bg-light text-dark border">
                      {{ $product->category->name ?? 'Uncategorized' }}
                    </span>
                  </td>

                  <td class="text-end text-muted">
                    ৳{{ number_format($product->cost_price, 2) }}
                  </td>

                  <td class="text-end fw-bold text-success">
                    ৳{{ number_format($product->selling_price, 2) }}
                  </td>

                  <td class="text-center">
                    @if($product->stock_quantity <= 0)
                      <span class="badge bg-danger">Out of Stock</span>
                    @elseif($product->isLowStock())
                      <span class="badge bg-warning text-dark border border-warning">
                        {{ $product->stock_quantity }} {{ $product->unit->short_name ?? 'pcs' }} (Low)
                      </span>
                    @else
                      <span class="badge bg-success-subtle text-success border border-success-subtle">
                        {{ $product->stock_quantity }} {{ $product->unit->short_name ?? 'pcs' }}
                      </span>
                    @endif
                  </td>

                  <td class="text-center">
                    @if($product->status)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>

                  <td class="text-end">
                    <a href="{{ route('products.barcode', ['product' => $product->id]) }}" class="btn btn-sm btn-outline-secondary me-1" title="Print Barcode">
                      <i class="bi bi-upc-scan"></i>
                    </a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                    No products found matching your filter criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $products->links() }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
