@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customers Directory')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">People</a></li>
  <li class="breadcrumb-item active">Customers</li>
@endsection

@section('page-actions')
  <div class="d-flex gap-2">
    <a href="{{ route('customers.ledger') }}" class="btn btn-outline-danger d-flex align-items-center gap-1 shadow-sm">
      <i class="bi bi-journal-text"></i>
      <span>Due Ledger (৳{{ number_format($totalDueSum, 2) }})</span>
    </a>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
      <i class="bi bi-person-plus"></i>
      <span>Add Customer</span>
    </button>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Overview Summary Cards -->
  <div class="col-md-6 col-lg-4 mb-3">
    <div class="card info-card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-title">Registered Customers</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
            <i class="bi bi-people"></i>
          </div>
          <div class="ps-3">
            <h6>{{ $totalCustomersCount }}</h6>
            <span class="text-muted small">Active accounts</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-4 mb-3">
    <div class="card info-card shadow-sm h-100">
      <div class="card-body">
        <h5 class="card-title">Total Outstanding Due</h5>
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger">
            <i class="bi bi-exclamation-octagon"></i>
          </div>
          <div class="ps-3">
            <h6 class="text-danger">৳{{ number_format($totalDueSum, 2) }}</h6>
            <span class="text-danger small">Unpaid balance</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('customers.index') }}" class="row g-2 pt-3 pb-3 mb-2 border-bottom align-items-end">
          <div class="col-md-5">
            <label class="form-label small fw-semibold text-muted">Search Customer</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Search by name or phone..." value="{{ request('search') }}">
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted">Due Status</label>
            <select name="has_due" class="form-select">
              <option value="">All Customers</option>
              <option value="yes" {{ request('has_due') === 'yes' ? 'selected' : '' }}>Only Customers with Due (> ৳0)</option>
            </select>
          </div>

          <div class="col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->anyFilled(['search', 'has_due']))
              <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                <i class="bi bi-x-lg"></i>
              </a>
            @endif
          </div>
        </form>

        <!-- Customers Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col">Customer Name</th>
                <th scope="col">Phone</th>
                <th scope="col">Email</th>
                <th scope="col">Address</th>
                <th scope="col" class="text-center">Total Orders</th>
                <th scope="col" class="text-end">Due Balance</th>
                <th scope="col" class="text-end" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($customers as $index => $customer)
                <tr>
                  <td>{{ $customers->firstItem() + $index }}</td>
                  <td>
                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                      {{ $customer->name }}
                      @if($customer->is_walk_in)
                        <span class="badge bg-info-subtle text-info border border-info-subtle small">Default POS</span>
                      @endif
                    </div>
                  </td>
                  <td>
                    @if($customer->phone)
                      <span class="small text-muted"><i class="bi bi-telephone text-primary me-1"></i>{{ $customer->phone }}</span>
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td class="small text-muted">{{ $customer->email ?? '—' }}</td>
                  <td class="small text-muted">{{ Str::limit($customer->address ?? '—', 35) }}</td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary border">
                      {{ $customer->orders_count }} orders
                    </span>
                  </td>
                  <td class="text-end">
                    @if($customer->total_due > 0)
                      <span class="badge bg-danger fs-6">৳{{ number_format($customer->total_due, 2) }}</span>
                    @else
                      <span class="badge bg-success-subtle text-success border border-success-subtle">৳0.00</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if(!$customer->is_walk_in)
                      <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $customer->id }}" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </button>

                      <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    @else
                      <span class="badge bg-light text-muted border">System</span>
                    @endif
                  </td>
                </tr>

                <!-- Edit Customer Modal -->
                @if(!$customer->is_walk_in)
                  <div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <form action="{{ route('customers.update', $customer) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Customer Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-3">
                              <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                              <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label fw-semibold">Phone Number</label>
                              <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
                            </div>
                            <div class="mb-3">
                              <label class="form-label fw-semibold">Email Address</label>
                              <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
                            </div>
                            <div class="mb-3">
                              <label class="form-label fw-semibold">Address</label>
                              <textarea name="address" class="form-control" rows="2">{{ $customer->address }}</textarea>
                            </div>
                            <div class="mb-3">
                              <label class="form-label fw-semibold">Status</label>
                              <select name="status" class="form-select">
                                <option value="1" {{ $customer->status ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$customer->status ? 'selected' : '' }}>Inactive</option>
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div><!-- End Edit Modal -->
                @endif

              @empty
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    No customers found. Click "Add Customer" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $customers->links() }}
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Create Customer Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Customer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Shakil Khan" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Phone Number</label>
            <input type="text" name="phone" class="form-control" placeholder="017xxxxxxxx">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="customer@example.com">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Customer address..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Customer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
