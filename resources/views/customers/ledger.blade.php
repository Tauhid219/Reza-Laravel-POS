@extends('layouts.admin')

@section('title', 'Customer Due Ledger')
@section('page-title', 'Customer Due Ledger')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
  <li class="breadcrumb-item active">Due Ledger</li>
@endsection

@section('page-actions')
  <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back to Customers
  </a>
@endsection

@section('content')
<div class="row">

  <!-- Overview Stat Banner -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm border-start border-danger border-4">
      <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="fw-bold mb-0 text-danger">Total Outstanding Customer Due</h5>
          <small class="text-muted">Total unpaid credit sales amount pending collection from customers</small>
        </div>
        <div>
          <span class="fs-3 fw-bold text-danger">৳{{ number_format($totalOutstandingDue, 2) }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Customers with Due Balance</h5>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col">Customer</th>
                <th scope="col">Phone</th>
                <th scope="col">Address</th>
                <th scope="col" class="text-center">Total Orders</th>
                <th scope="col" class="text-end">Due Balance</th>
                <th scope="col" class="text-end" style="width: 160px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dueCustomers as $index => $customer)
                <tr>
                  <td>{{ $dueCustomers->firstItem() + $index }}</td>
                  <td class="fw-bold text-dark">{{ $customer->name }}</td>
                  <td>
                    @if($customer->phone)
                      <span class="small text-muted"><i class="bi bi-telephone text-primary me-1"></i>{{ $customer->phone }}</span>
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td class="small text-muted">{{ Str::limit($customer->address ?? '—', 40) }}</td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary border">
                      {{ $customer->orders_count }} orders
                    </span>
                  </td>
                  <td class="text-end">
                    <span class="badge bg-danger fs-6">৳{{ number_format($customer->total_due, 2) }}</span>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#collectDueModal{{ $customer->id }}">
                      <i class="bi bi-cash-coin"></i>
                      <span>Collect Payment</span>
                    </button>
                  </td>
                </tr>

                <!-- Collect Payment Modal -->
                <div class="modal fade" id="collectDueModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="{{ route('customers.collect_due', $customer) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                          <h5 class="modal-title">Collect Due Payment</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="p-3 bg-light rounded border mb-3">
                            <div class="d-flex justify-content-between mb-1">
                              <span class="text-muted small">Customer Name:</span>
                              <span class="fw-bold">{{ $customer->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                              <span class="text-muted small">Phone:</span>
                              <span>{{ $customer->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                              <span class="fw-bold text-danger">Current Due:</span>
                              <span class="fw-bold text-danger fs-5">৳{{ number_format($customer->total_due, 2) }}</span>
                            </div>
                          </div>

                          <div class="mb-3">
                            <label class="form-label fw-semibold">Collected Amount (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" max="{{ $customer->total_due }}" name="amount" class="form-control fs-5 fw-bold text-success" value="{{ $customer->total_due }}" required>
                            <small class="text-muted">Maximum collectible: ৳{{ number_format($customer->total_due, 2) }}</small>
                          </div>

                          <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                              <option value="Cash">Cash</option>
                              <option value="bKash">bKash</option>
                              <option value="Nagad">Nagad</option>
                              <option value="Card">Card</option>
                              <option value="Bank">Bank Transfer</option>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label class="form-label fw-semibold">Note / Transaction ID</label>
                            <input type="text" name="note" class="form-control" placeholder="e.g. Received cash at counter">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Confirm Collection
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div><!-- End Collect Due Modal -->

              @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                    No outstanding customer dues! All customer balances are clear.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $dueCustomers->links() }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
