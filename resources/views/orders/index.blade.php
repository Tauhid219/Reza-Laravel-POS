@extends('layouts.admin')

@section('title', 'Sales Orders List')
@section('page-title', 'Sales History')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Sales</li>
  <li class="breadcrumb-item active">Orders List</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
      <i class="bi bi-cart-plus me-1"></i> Open POS Terminal
    </a>
    <a href="{{ route('orders.due') }}" class="btn btn-outline-danger">
      <i class="bi bi-clock-history me-1"></i> Due Orders
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Summary KPI Cards -->
  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card sales-card shadow-sm h-100 mb-0">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-receipt"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">{{ number_format($orders->total()) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Total Orders</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card revenue-card shadow-sm h-100 mb-0">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-currency-dollar"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalSalesSum, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Total Sales Value</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card customers-card shadow-sm h-100 mb-0">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalDueSum, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Outstanding Due</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-graph-up"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">
              ৳{{ $orders->total() > 0 ? number_format($totalSalesSum / $orders->total(), 2) : '0.00' }}
            </h6>
            <span class="text-muted small pt-1 fw-semibold">Average Order Value</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm mb-0">
      <div class="card-body py-3">
        <form action="{{ route('orders.index') }}" method="GET" class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Search</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Invoice # or customer..." value="{{ request('search') }}">
            </div>
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Payment Status</label>
            <select name="payment_status" class="form-select form-select-sm">
              <option value="">All Statuses</option>
              <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
              <option value="due" {{ request('payment_status') == 'due' ? 'selected' : '' }}>Due / Unpaid</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Payment Type</label>
            <select name="payment_type" class="form-select form-select-sm">
              <option value="">All Types</option>
              <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="card" {{ request('payment_type') == 'card' ? 'selected' : '' }}>Card</option>
              <option value="mobile_banking" {{ request('payment_type') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
              <option value="credit" {{ request('payment_type') == 'credit' ? 'selected' : '' }}>Credit</option>
              <option value="split" {{ request('payment_type') == 'split' ? 'selected' : '' }}>Split</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
          </div>

          <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter">
              <i class="bi bi-funnel"></i>
            </button>
            @if(request()->hasAny(['search', 'payment_status', 'payment_type', 'start_date', 'end_date']))
              <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                <i class="bi bi-x-circle"></i>
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Orders Data Table -->
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body pt-3">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Invoice #</th>
                <th>Date & Time</th>
                <th>Customer</th>
                <th>Billed By</th>
                <th class="text-center">Items</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Due</th>
                <th class="text-center">Payment</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td>
                    <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary">
                      {{ $order->invoice_no }}
                    </a>
                  </td>
                  <td>
                    <span class="d-block small fw-semibold">{{ $order->order_date ? $order->order_date->format('d M, Y') : $order->created_at->format('d M, Y') }}</span>
                    <span class="text-muted small">{{ $order->order_date ? $order->order_date->format('h:i A') : $order->created_at->format('h:i A') }}</span>
                  </td>
                  <td>
                    <div class="fw-semibold">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div>
                    @if($order->customer?->phone)
                      <span class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $order->customer->phone }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="small fw-semibold">{{ $order->user?->name ?? 'Cashier' }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $order->total_items }}</span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    ৳{{ number_format($order->total_amount, 2) }}
                  </td>
                  <td class="text-end text-success fw-semibold">
                    ৳{{ number_format($order->paid_amount, 2) }}
                  </td>
                  <td class="text-end">
                    @if($order->due_amount > 0)
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                        ৳{{ number_format($order->due_amount, 2) }}
                      </span>
                    @else
                      <span class="text-muted small">৳0.00</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 0.75rem;">
                      {{ str_replace('_', ' ', $order->payment_type) }}
                    </span>
                  </td>
                  <td class="text-center">
                    @if($order->payment_status === 'paid')
                      <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-check-circle-fill me-1"></i> Paid
                      </span>
                    @elseif($order->payment_status === 'partial')
                      <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                        <i class="bi bi-clock-fill me-1"></i> Partial
                      </span>
                    @else
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Due
                      </span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                          <a class="dropdown-item" href="{{ route('orders.show', $order) }}">
                            <i class="bi bi-eye text-primary me-2"></i> View Order
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{ route('orders.receipt', $order) }}" target="_blank">
                            <i class="bi bi-printer text-success me-2"></i> Thermal Receipt (80mm)
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{ route('orders.invoice', $order) }}" target="_blank">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i> A4 Standard Invoice
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="11" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    No sales orders found matching your search criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center">
          <div class="text-muted small">
            Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
          </div>
          <div>
            {{ $orders->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
