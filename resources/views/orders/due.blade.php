@extends('layouts.admin')

@section('title', 'Due & Credit Orders')
@section('page-title', 'Outstanding Due Sales')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
  <li class="breadcrumb-item active">Due Orders</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-list-ul me-1"></i> All Orders
    </a>
    <a href="{{ route('customers.ledger') }}" class="btn btn-outline-primary">
      <i class="bi bi-journal-text me-1"></i> Customer Ledgers
    </a>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
      <i class="bi bi-cart4 me-1"></i> New Sale
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Due Alert Banner -->
  <div class="col-12 mb-3">
    <div class="card bg-danger-subtle border-danger-subtle shadow-sm mb-0">
      <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-danger text-white rounded p-3 text-center">
            <i class="bi bi-exclamation-octagon fs-3"></i>
          </div>
          <div>
            <h5 class="mb-0 fw-bold text-danger">Total Outstanding Receivables</h5>
            <span class="text-danger-emphasis small">Unpaid balance on credit and partial POS transactions</span>
          </div>
        </div>
        <div class="text-end">
          <span class="fs-3 fw-bold text-danger">৳{{ number_format($totalOutstandingDue, 2) }}</span>
          <span class="d-block small text-muted">{{ $orders->total() }} unpaid / partial invoices</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Filter -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm mb-0">
      <div class="card-body py-3">
        <form action="{{ route('orders.due') }}" method="GET" class="row g-2 align-items-center">
          <div class="col-md-6">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Search by customer name, phone, or invoice #..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">
              <i class="bi bi-filter me-1"></i> Filter
            </button>
          </div>
          @if(request('search'))
            <div class="col-md-2">
              <a href="{{ route('orders.due') }}" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-1"></i> Clear
              </a>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>

  <!-- Due Orders Table -->
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body pt-3">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Invoice #</th>
                <th>Sale Date</th>
                <th>Customer</th>
                <th>Contact</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Paid Amount</th>
                <th class="text-end text-danger">Due Amount</th>
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
                    <span class="small fw-semibold">{{ $order->order_date ? $order->order_date->format('d M, Y') : $order->created_at->format('d M, Y') }}</span>
                    <span class="d-block text-muted small">{{ $order->order_date ? $order->order_date->format('h:i A') : $order->created_at->format('h:i A') }}</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark">{{ $order->customer?->name ?? 'Walk-in Customer' }}</div>
                    @if($order->customer?->address)
                      <span class="text-muted small">{{ Str::limit($order->customer->address, 30) }}</span>
                    @endif
                  </td>
                  <td>
                    @if($order->customer?->phone)
                      <a href="tel:{{ $order->customer->phone }}" class="text-secondary small fw-semibold text-decoration-none">
                        <i class="bi bi-telephone-fill text-success me-1"></i>{{ $order->customer->phone }}
                      </a>
                    @else
                      <span class="text-muted small">N/A</span>
                    @endif
                  </td>
                  <td class="text-end fw-semibold">
                    ৳{{ number_format($order->total_amount, 2) }}
                  </td>
                  <td class="text-end text-success fw-semibold">
                    ৳{{ number_format($order->paid_amount, 2) }}
                  </td>
                  <td class="text-end">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6 fw-bold">
                      ৳{{ number_format($order->due_amount, 2) }}
                    </span>
                  </td>
                  <td class="text-center">
                    @if($order->payment_status === 'partial')
                      <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                        Partial
                      </span>
                    @else
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        Unpaid Due
                      </span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                      <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light border" title="View Order">
                        <i class="bi bi-eye text-primary"></i>
                      </a>
                      <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="btn btn-sm btn-light border" title="Receipt">
                        <i class="bi bi-printer text-success"></i>
                      </a>
                      @if($order->customer && !$order->customer->is_walk_in)
                        <a href="{{ route('customers.ledger', ['customer_id' => $order->customer_id]) }}" class="btn btn-sm btn-outline-danger" title="Customer Ledger / Collect Due">
                          <i class="bi bi-cash-coin me-1"></i> Settle
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                    Great news! There are no outstanding due orders matching your criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center">
          <div class="text-muted small">
            Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} due orders
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
