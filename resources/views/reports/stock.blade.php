@extends('layouts.admin')

@section('title', 'Stock & Inventory Valuation Report')
@section('page-title', 'Stock & Inventory Valuation')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Reports</li>
  <li class="breadcrumb-item active">Stock Valuation</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> Print Stock Sheet
    </button>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Purchase Stock
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- KPI Metric Cards -->
  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-primary border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-boxes"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">{{ number_format($totalStockQty) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Total Stock Units</span>
            <div class="text-muted small mt-1">Across {{ $totalProducts }} products</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-secondary border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalCostValuation, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Valuation at Cost</span>
            <div class="text-muted small mt-1">Capital invested in stock</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-info border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-tag"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalRetailValuation, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Valuation at Retail</span>
            <div class="text-muted small mt-1">Expected sales turnover</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-success border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold text-success">৳{{ number_format($projectedGrossProfit, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Unrealized Profit</span>
            <div class="text-success small fw-semibold mt-1">
              Potential: {{ $totalCostValuation > 0 ? number_format(($projectedGrossProfit / $totalCostValuation) * 100, 1) : 0 }}% ROI
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Alerts Quick Bar -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm mb-0">
      <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <form action="{{ route('reports.stock') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
          <select name="category_id" class="form-select form-select-sm" style="width: 180px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>

          <select name="stock_status" class="form-select form-select-sm" style="width: 170px;">
            <option value="">All Stock Levels</option>
            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock Alerts ({{ $lowStockCount }})</option>
            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock ({{ $outOfStockCount }})</option>
            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Healthy Stock</option>
          </select>

          <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>

          @if(request()->hasAny(['category_id', 'stock_status']))
            <a href="{{ route('reports.stock') }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-x-circle me-1"></i> Reset
            </a>
          @endif
        </form>

        <div class="d-flex align-items-center gap-2">
          @if($lowStockCount > 0)
            <a href="{{ route('reports.stock', ['stock_status' => 'low_stock']) }}" class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 text-decoration-none">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $lowStockCount }} Low Stock Alert(s)
            </a>
          @endif
          @if($outOfStockCount > 0)
            <a href="{{ route('reports.stock', ['stock_status' => 'out_of_stock']) }}" class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 text-decoration-none">
              <i class="bi bi-x-octagon-fill me-1"></i> {{ $outOfStockCount }} Out of Stock
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Stock Inventory Table -->
  <div class="col-12">
    <div class="card shadow-sm mb-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Item / Barcode</th>
                <th>Category</th>
                <th class="text-center">Current Stock</th>
                <th class="text-end">Cost Price</th>
                <th class="text-end">Selling Price</th>
                <th class="text-end">Total Cost</th>
                <th class="text-end">Total Retail</th>
                <th class="text-end">Margin</th>
                <th class="text-center pe-3">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
                @php
                  $stockCost = $product->stock_quantity * $product->cost_price;
                  $stockRetail = $product->stock_quantity * $product->selling_price;
                  $margin = $product->selling_price > 0 ? (($product->selling_price - $product->cost_price) / $product->selling_price) * 100 : 0;
                @endphp
                <tr>
                  <td class="ps-3">
                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                    <span class="text-muted small">Code: {{ $product->code }}</span>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                  </td>
                  <td class="text-center">
                    <span class="fw-bold fs-6 {{ $product->stock_quantity <= 0 ? 'text-danger' : ($product->stock_quantity <= $product->alert_quantity ? 'text-warning' : 'text-dark') }}">
                      {{ $product->stock_quantity }} {{ $product->unit?->short_name }}
                    </span>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Alert at: {{ $product->alert_quantity }}</small>
                  </td>
                  <td class="text-end">৳{{ number_format($product->cost_price, 2) }}</td>
                  <td class="text-end fw-semibold">৳{{ number_format($product->selling_price, 2) }}</td>
                  <td class="text-end text-muted">৳{{ number_format($stockCost, 2) }}</td>
                  <td class="text-end fw-bold text-dark">৳{{ number_format($stockRetail, 2) }}</td>
                  <td class="text-end text-success fw-semibold">{{ number_format($margin, 1) }}%</td>
                  <td class="text-center pe-3">
                    @if($product->stock_quantity <= 0)
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        Out of Stock
                      </span>
                    @elseif($product->stock_quantity <= $product->alert_quantity)
                      <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                        Low Stock
                      </span>
                    @else
                      <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        Healthy
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-4 text-muted">
                    No products found matching the criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="p-3 d-flex justify-content-between align-items-center">
          <small class="text-muted">Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</small>
          {{ $products->links() }}
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
