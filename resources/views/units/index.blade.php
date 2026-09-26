@extends('layouts.admin')

@section('title', 'Units of Measure')
@section('page-title', 'Units of Measure')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/products') }}">Inventory</a></li>
  <li class="breadcrumb-item active">Units</li>
@endsection

@section('page-actions')
  <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUnitModal">
    <i class="bi bi-plus-circle"></i>
    <span>Add Unit</span>
  </button>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Manage Units</h5>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 60px;">#</th>
                <th scope="col">Unit Name</th>
                <th scope="col">Short Name / Symbol</th>
                <th scope="col" class="text-center">Total Products</th>
                <th scope="col" class="text-end" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($units as $index => $unit)
                <tr>
                  <td>{{ $units->firstItem() + $index }}</td>
                  <td class="fw-semibold text-dark">{{ $unit->name }}</td>
                  <td><span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ $unit->short_name }}</span></td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary border">
                      {{ $unit->products_count }} items
                    </span>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $unit->id }}">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this unit?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>

                <!-- Edit Unit Modal -->
                <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="{{ route('units.update', $unit) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Unit</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $unit->name }}" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Short Name / Code <span class="text-danger">*</span></label>
                            <input type="text" name="short_name" class="form-control" value="{{ $unit->short_name }}" required>
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
                  <td colspan="5" class="text-center py-4 text-muted">
                    No units defined yet. Click "Add Unit" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $units->links() }}
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Create Unit Modal -->
<div class="modal fade" id="createUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Unit of Measure</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Kilogram, Piece, Box" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Short Name / Symbol <span class="text-danger">*</span></label>
            <input type="text" name="short_name" class="form-control" placeholder="e.g. kg, pc, box" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Unit</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
