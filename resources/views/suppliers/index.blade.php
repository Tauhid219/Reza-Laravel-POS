@extends('layouts.admin')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers List')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/purchases') }}">Procurement</a></li>
  <li class="breadcrumb-item active">Suppliers</li>
@endsection

@section('page-actions')
  <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
    <i class="bi bi-person-plus"></i>
    <span>Add Supplier</span>
  </button>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Manage Suppliers</h5>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 60px;">#</th>
                <th scope="col">Supplier / Contact Person</th>
                <th scope="col">Company Name</th>
                <th scope="col">Phone & Email</th>
                <th scope="col">Address</th>
                <th scope="col" class="text-center">Purchases</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($suppliers as $index => $supplier)
                <tr>
                  <td>{{ $suppliers->firstItem() + $index }}</td>
                  <td>
                    <div class="fw-bold text-dark">{{ $supplier->name }}</div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      {{ $supplier->company_name ?? 'Individual' }}
                    </span>
                  </td>
                  <td>
                    <div><i class="bi bi-telephone text-primary me-1 small"></i>{{ $supplier->phone }}</div>
                    @if($supplier->email)
                      <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $supplier->email }}</div>
                    @endif
                  </td>
                  <td class="small text-muted">{{ Str::limit($supplier->address ?? '—', 40) }}</td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary border">
                      {{ $supplier->purchases_count }} bills
                    </span>
                  </td>
                  <td class="text-center">
                    @if($supplier->status)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>

                <!-- Edit Supplier Modal -->
                <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Supplier</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Supplier / Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Company / Brand Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ $supplier->company_name }}">
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ $supplier->address }}</textarea>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                              <option value="1" {{ $supplier->status ? 'selected' : '' }}>Active</option>
                              <option value="0" {{ !$supplier->status ? 'selected' : '' }}>Inactive</option>
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

              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    No suppliers found. Click "Add Supplier" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $suppliers->links() }}
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Supplier / Contact Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. PRAN Distribution" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Company / Brand Name</label>
            <input type="text" name="company_name" class="form-control" placeholder="e.g. PRAN-RFL Group">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" placeholder="017xxxxxxxx" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="supplier@example.com">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Full address or warehouse location..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Supplier</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
