@extends('layouts.admin')

@section('title', 'Order Details - ' . $order->invoice_no)
@section('page-title', 'Order ' . $order->invoice_no)

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
  <li class="breadcrumb-item active">{{ $order->invoice_no }}</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Orders
    </a>
    <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="btn btn-outline-success">
      <i class="bi bi-printer me-1"></i> Thermal Receipt (80mm)
    </a>
    <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="btn btn-primary">
      <i class="bi bi-file-earmark-text me-1"></i> A4 Invoice
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Status Bar / Key Meta -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm border-0 mb-0">
      <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary text-white rounded p-3 text-center">
            <i class="bi bi-receipt fs-3"></i>
          </div>
          <div>
            <h4 class="mb-0 fw-bold">{{ $order->invoice_no }}</h4>
            <span class="text-muted small">
              Placed on {{ $order->order_date ? $order->order_date->format('l, d F Y - h:i A') : $order->created_at->format('l, d F Y - h:i A') }}
            </span>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="text-end">
            <span class="text-muted small d-block">Payment Status</span>
            @if($order->payment_status === 'paid')
              <span class="badge bg-success fs-6 px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Fully Paid</span>
            @elseif($order->payment_status === 'partial')
              <span class="badge bg-warning text-dark fs-6 px-3 py-1"><i class="bi bi-clock-fill me-1"></i> Partially Paid</span>
            @else
              <span class="badge bg-danger fs-6 px-3 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Unpaid / Due</span>
            @endif
          </div>

          <div class="text-end ps-3 border-start">
            <span class="text-muted small d-block">Order Status</span>
            <span class="badge bg-primary fs-6 px-3 py-1 text-capitalize">{{ $order->order_status }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Left: Order Items & Payment Details -->
  <div class="col-lg-8">
    <!-- Items Card -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-basket me-2 text-primary"></i> Purchased Items ({{ $order->items->count() }})
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">#</th>
                <th>Item Description</th>
                <th class="text-end">Unit Price</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Discount</th>
                <th class="text-end pe-3">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $index => $item)
                <tr>
                  <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                  <td>
                    <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                    @if($item->product?->code)
                      <span class="text-muted small">Code: {{ $item->product->code }}</span>
                    @endif
                  </td>
                  <td class="text-end fw-semibold">৳{{ number_format($item->unit_price, 2) }}</td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ $item->quantity }}</span>
                  </td>
                  <td class="text-end text-muted">
                    {{ $item->discount > 0 ? '৳' . number_format($item->discount, 2) : '-' }}
                  </td>
                  <td class="text-end pe-3 fw-bold text-dark">
                    ৳{{ number_format($item->subtotal, 2) }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @if($order->note)
        <div class="card-footer bg-light py-2 px-3">
          <small class="text-muted fw-bold">Note:</small>
          <small class="text-secondary">{{ $order->note }}</small>
        </div>
      @endif
    </div>

    <!-- Payments Card -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-credit-card me-2 text-primary"></i> Payment Transactions
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Date</th>
                <th>Method</th>
                <th>Processed By</th>
                <th class="text-end pe-3">Amount Paid</th>
              </tr>
            </thead>
            <tbody>
              @forelse($order->payments as $payment)
                <tr>
                  <td class="ps-3 small">
                    {{ $payment->payment_date ? $payment->payment_date->format('d M Y, h:i A') : $payment->created_at->format('d M Y, h:i A') }}
                  </td>
                  <td>
                    <span class="badge bg-secondary-subtle text-secondary text-uppercase">
                      {{ str_replace('_', ' ', $payment->payment_method) }}
                    </span>
                  </td>
                  <td class="small">{{ $payment->user?->name ?? 'System' }}</td>
                  <td class="text-end pe-3 fw-bold text-success">
                    ৳{{ number_format($payment->amount, 2) }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-3 text-muted">
                    No individual payment records found (recorded on checkout).
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Summary & Customer Details -->
  <div class="col-lg-4">
    <!-- Financial Breakdown Card -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-calculator me-2 text-primary"></i> Financial Summary
        </h5>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush mb-0">
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Subtotal:</span>
            <span class="fw-semibold">৳{{ number_format($order->subtotal, 2) }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Discount {{ $order->discount_type === 'percentage' ? '(' . $order->discount_amount . '%)' : '' }}:</span>
            <span class="text-danger fw-semibold">-৳{{ number_format($order->discount_type === 'percentage' ? ($order->subtotal * $order->discount_amount / 100) : $order->discount_amount, 2) }}</span>
          </li>
          @if($order->tax_amount > 0)
            <li class="list-group-item d-flex justify-content-between px-0 py-2">
              <span class="text-muted">Tax / VAT ({{ $order->tax_percentage }}%):</span>
              <span class="fw-semibold">+৳{{ number_format($order->tax_amount, 2) }}</span>
            </li>
          @endif
          <li class="list-group-item d-flex justify-content-between px-0 py-2 fs-5 border-top border-2">
            <span class="fw-bold">Total Amount:</span>
            <span class="fw-bold text-primary">৳{{ number_format($order->total_amount, 2) }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between px-0 py-2">
            <span class="text-muted">Paid Amount:</span>
            <span class="fw-bold text-success">৳{{ number_format($order->paid_amount, 2) }}</span>
          </li>
          @if($order->due_amount > 0)
            <li class="list-group-item d-flex justify-content-between px-0 py-2 bg-danger-subtle rounded px-2 mt-1">
              <span class="fw-bold text-danger">Due / Balance:</span>
              <span class="fw-bold text-danger fs-6">৳{{ number_format($order->due_amount, 2) }}</span>
            </li>
          @endif
          @if($order->change_amount > 0)
            <li class="list-group-item d-flex justify-content-between px-0 py-2">
              <span class="text-muted">Change Returned:</span>
              <span class="fw-bold text-dark">৳{{ number_format($order->change_amount, 2) }}</span>
            </li>
          @endif

          @hasrole('Admin')
            <!-- Profit Margin & COGS for Admin -->
            <li class="list-group-item px-0 py-2 mt-2 border-top">
              <div class="d-flex justify-content-between text-muted small">
                <span>Cost of Goods (COGS):</span>
                <span>৳{{ number_format($order->total_cost, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between text-success fw-bold small mt-1">
                <span>Gross Profit:</span>
                <span>৳{{ number_format($order->total_amount - $order->total_cost, 2) }}</span>
              </div>
            </li>
          @endhasrole
        </ul>
      </div>
    </div>

    <!-- Customer Card -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-person me-2 text-primary"></i> Customer Details
        </h5>
      </div>
      <div class="card-body">
        <h6 class="fw-bold mb-1">{{ $order->customer?->name ?? 'Walk-in Customer' }}</h6>
        @if($order->customer?->phone)
          <div class="text-muted small mb-1"><i class="bi bi-telephone me-2"></i>{{ $order->customer->phone }}</div>
        @endif
        @if($order->customer?->email)
          <div class="text-muted small mb-1"><i class="bi bi-envelope me-2"></i>{{ $order->customer->email }}</div>
        @endif
        @if($order->customer?->address)
          <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-2"></i>{{ $order->customer->address }}</div>
        @endif

        @if($order->customer && !$order->customer->is_walk_in)
          <div class="border-top pt-2 mt-2">
            <div class="d-flex justify-content-between align-items-center small">
              <span class="text-muted">Total Outstanding Due:</span>
              <span class="badge bg-danger-subtle text-danger fw-bold">৳{{ number_format($order->customer->total_due, 2) }}</span>
            </div>
            <a href="{{ route('customers.ledger', ['customer_id' => $order->customer_id]) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
              <i class="bi bi-journal-text me-1"></i> Customer Ledger
            </a>
          </div>
        @endif
      </div>
    </div>

    <!-- Register & Cashier Card -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-cash-stack me-2 text-primary"></i> Shift & Cashier
        </h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Cashier:</span>
          <span class="fw-semibold small">{{ $order->user?->name ?? 'Cashier' }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Register Shift:</span>
          <span class="small">{{ $order->cashRegister ? 'Shift #' . $order->cashRegister->id : 'Direct' }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted small">Payment Method:</span>
          <span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $order->payment_type) }}</span>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
