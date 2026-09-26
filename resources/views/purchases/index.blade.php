@extends('layouts.admin')

@section('title', 'Purchases')
@section('page-title', 'Stock Purchases')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/purchases') }}">Procurement</a></li>
  <li class="breadcrumb-item active">Purchases</li>
@endsection

@section('page-actions')
  <a href="{{ route('purchases.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
    <i class="bi bi-plus-circle"></i>
    <span>New Stock Purchase</span>
  </a>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('purchases.index') }}" class="row g-2 pt-3 pb-3 mb-2 border-bottom align-items-end">
          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Supplier</label>
            <select name="supplier_id" class="form-select">
              <option value="">All Suppliers</option>
              @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                  {{ $supplier->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Payment Status</label>
            <select name="payment_status" class="form-select">
              <option value="">All Statuses</option>
              <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
              <option value="due" {{ request('payment_status') === 'due' ? 'selected' : '' }}>Due</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
          </div>

          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->anyFilled(['supplier_id', 'payment_status', 'start_date', 'end_date']))
              <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                <i class="bi bi-x-lg"></i>
              </a>
            @endif
          </div>
        </form>

        <!-- Purchases Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Purchase #</th>
                <th scope="col">Supplier</th>
                <th scope="col">Date</th>
                <th scope="col" class="text-center">Items</th>
                <th scope="col" class="text-end">Total Amount</th>
                <th scope="col" class="text-end">Paid Amount</th>
                <th scope="col" class="text-end">Due Amount</th>
                <th scope="col" class="text-center">Payment</th>
                <th scope="col" class="text-end" style="width: 100px;">Invoice</th>
              </tr>
            </thead>
            <tbody>
              @forelse($purchases as $purchase)
                <tr>
                  <td>
                    <a href="{{ route('purchases.show', $purchase) }}" class="fw-bold text-primary">
                      {{ $purchase->purchase_no }}
                    </a>
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $purchase->supplier->name ?? 'N/A' }}</div>
                    <small class="text-muted">{{ $purchase->supplier->phone ?? '' }}</small>
                  </td>
                  <td>{{ $purchase->purchase_date->format('d M, Y') }}</td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary border">
                      {{ $purchase->items->count() }} items
                    </span>
                  </td>
                  <td class="text-end fw-bold">৳{{ number_format($purchase->total_amount, 2) }}</td>
                  <td class="text-end text-success">৳{{ number_format($purchase->paid_amount, 2) }}</td>
                  <td class="text-end text-danger fw-semibold">৳{{ number_format($purchase->due_amount, 2) }}</td>
                  <td class="text-center">
                    @if($purchase->payment_status === 'paid')
                      <span class="badge bg-success">Paid</span>
                    @elseif($purchase->payment_status === 'partial')
                      <span class="badge bg-warning text-dark">Partial</span>
                    @else
                      <span class="badge bg-danger">Due</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-primary" title="View Bill">
                      <i class="bi bi-eye"></i> View
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="bi bi-truck fs-1 d-block mb-2 text-secondary"></i>
                    No purchase records found. Click "New Stock Purchase" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $purchases->links() }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
