@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Product Categories')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/products') }}">Inventory</a></li>
  <li class="breadcrumb-item active">Categories</li>
@endsection

@section('page-actions')
  <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
    <i class="bi bi-plus-circle"></i>
    <span>Add Category</span>
  </button>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Manage Categories</h5>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 60px;">#</th>
                <th scope="col">Category Name</th>
                <th scope="col">Slug</th>
                <th scope="col">Description</th>
                <th scope="col" class="text-center">Total Products</th>
                <th scope="col" class="text-center">Status</th>
                <th scope="col" class="text-end" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $index => $category)
                <tr>
                  <td>{{ $categories->firstItem() + $index }}</td>
                  <td class="fw-semibold text-dark">{{ $category->name }}</td>
                  <td><code>{{ $category->slug }}</code></td>
                  <td class="text-muted small">{{ Str::limit($category->description ?? '—', 50) }}</td>
                  <td class="text-center">
                    <span class="badge bg-secondary-subtle text-secondary border">
                      {{ $category->products_count }} items
                    </span>
                  </td>
                  <td class="text-center">
                    @if($category->status)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>

                <!-- Edit Category Modal -->
                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Category</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                          </div>
                          <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                              <option value="1" {{ $category->status ? 'selected' : '' }}>Active</option>
                              <option value="0" {{ !$category->status ? 'selected' : '' }}>Inactive</option>
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
                  <td colspan="7" class="text-center py-4 text-muted">
                    No categories found. Click "Add Category" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $categories->links() }}
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Beverages" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Brief note about this category..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Category</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
