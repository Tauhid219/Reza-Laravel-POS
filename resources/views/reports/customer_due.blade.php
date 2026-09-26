@extends('layouts.admin')

@section('title', 'Customer Outstanding Due & Receivables')
@section('page-title', 'Customer Due & Receivables Report')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Reports</li>
  <li class="breadcrumb-item active">Customer Due</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> Print Due Sheet
    </button>
    <a href="{{ route('customers.ledger') }}" class="btn btn-primary">
      <i class="bi bi-journal-text me-1"></i> Customer Due Ledger
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Summary Cards -->
  <div class="col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-danger border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 52px; height: 52px; font-size: 26px;">
            <i class="bi bi-exclamation-octagon"></i>
          </div>
          <div class="ps-3">
            <h5 class="fs-3 mb-0 fw-bold text-danger">৳{{ number_format($totalOutstandingDue, 2) }}</h5>
            <span class="text-muted small pt-1 fw-semibold">Total Market Receivables (Due)</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-warning border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 52px; height: 52px; font-size: 26px;">
            <i class="bi bi-people"></i>
          </div>
          <div class="ps-3">
            <h5 class="fs-3 mb-0 fw-bold">{{ number_format($totalDueCustomers) }}</h5>
            <span class="text-muted small pt-1 fw-semibold">Customers with Outstanding Balances</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Filter -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm mb-0">
      <div class="card-body py-3">
        <form action="{{ route('reports.customer_due') }}" method="GET" class="row g-2 align-items-center">
          <div class="col-md-8">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Search customer by name or phone number..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">
              <i class="bi bi-filter me-1"></i> Filter
            </button>
          </div>
          @if(request('search'))
            <div class="col-md-2">
              <a href="{{ route('reports.customer_due') }}" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-1"></i> Reset
              </a>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>

  <!-- Customers Due Table -->
  <div class="col-12">
    <div class="card shadow-sm mb-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Customer Name</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th class="text-center">Total Orders</th>
                <th class="text-end text-danger fw-bold">Outstanding Due</th>
                <th class="text-center pe-3">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($customers as $customer)
                <tr>
                  <td class="ps-3">
                    <div class="fw-bold text-dark">{{ $customer->name }}</div>
                    @if($customer->email)
                      <small class="text-muted">{{ $customer->email }}</small>
                    @endif
                  </td>
                  <td>
                    @if($customer->phone)
                      <a href="tel:{{ $customer->phone }}" class="text-secondary small fw-semibold text-decoration-none">
                        <i class="bi bi-telephone text-success me-1"></i>{{ $customer->phone }}
                      </a>
                    @else
                      <span class="text-muted small">N/A</span>
                    @endif
                  </td>
                  <td>
                    <span class="text-muted small">{{ Str::limit($customer->address ?? 'N/A', 40) }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $customer->orders()->count() }}</span>
                  </td>
                  <td class="text-end">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fs-6 fw-bold">
                      ৳{{ number_format($customer->total_due, 2) }}
                    </span>
                  </td>
                  <td class="text-center pe-3">
                    <a href="{{ route('customers.ledger', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-journal-text me-1"></i> Ledger & Settle
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                    Excellent! No customer has outstanding due at this moment.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="p-3 d-flex justify-content-between align-items-center">
          <small class="text-muted">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} due customers</small>
          {{ $customers->links() }}
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
