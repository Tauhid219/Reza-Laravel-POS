@extends('layouts.admin')

@section('title', 'Add New Product')
@section('page-title', 'Add New Product')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/products') }}">Inventory</a></li>
  <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
  <li class="breadcrumb-item active">Add Product</li>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm">
      <div class="card-body pt-3">
        <h5 class="card-title pb-2 border-bottom">Product Information</h5>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 mt-1">
          @csrf

          <!-- Product Name -->
          <div class="col-md-8">
            <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Coca-Cola 250ml Can" required autofocus>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Product Code / Barcode -->
          <div class="col-md-4">
            <label for="code" class="form-label fw-semibold">Barcode / SKU <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $suggestedCode) }}" placeholder="Barcode Number" required>
              <button type="button" class="btn btn-outline-secondary" onclick="generateBarcode()" title="Generate Random Code">
                <i class="bi bi-arrow-repeat"></i>
              </button>
            </div>
            @error('code')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Category -->
          <div class="col-md-6">
            <label for="category_id" class="form-label fw-semibold">Category</label>
            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
              <option value="">Select Category</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            @error('category_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Unit of Measure -->
          <div class="col-md-6">
            <label for="unit_id" class="form-label fw-semibold">Unit of Measure</label>
            <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
              <option value="">Select Unit</option>
              @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                  {{ $unit->name }} ({{ $unit->short_name }})
                </option>
              @endforeach
            </select>
            @error('unit_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Cost / Buying Price -->
          <div class="col-md-3">
            <label for="cost_price" class="form-label fw-semibold">Cost Price (Buying) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" step="0.01" min="0" name="cost_price" id="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', '0.00') }}" required>
            </div>
            <small class="text-muted">Used for profit calculation</small>
            @error('cost_price')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Selling Price -->
          <div class="col-md-3">
            <label for="selling_price" class="form-label fw-semibold">Selling Price (Retail) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price') }}" required>
            </div>
            <small class="text-muted">POS sale price</small>
            @error('selling_price')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Initial Stock Quantity -->
          <div class="col-md-3">
            <label for="stock_quantity" class="form-label fw-semibold">Opening Stock <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="stock_quantity" id="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', '0') }}" required>
            @error('stock_quantity')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Alert Quantity -->
          <div class="col-md-3">
            <label for="alert_quantity" class="form-label fw-semibold">Low Stock Alert Qty <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="alert_quantity" id="alert_quantity" class="form-control @error('alert_quantity') is-invalid @enderror" value="{{ old('alert_quantity', '5') }}" required>
            @error('alert_quantity')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Product Image -->
          <div class="col-md-6">
            <label for="image" class="form-label fw-semibold">Product Image</label>
            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
            <small class="text-muted">Allowed: JPG, PNG, WEBP (Max 2MB)</small>
            @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div class="mt-2" id="imagePreviewContainer" style="display: none;">
              <img id="imagePreview" src="#" alt="Preview" class="rounded border p-1" style="max-height: 120px;">
            </div>
          </div>

          <!-- Status -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
              <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active (Available for sale)</option>
              <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive (Hide from POS)</option>
            </select>
          </div>

          <!-- Description -->
          <div class="col-12">
            <label for="description" class="form-label fw-semibold">Description / Notes</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional product details...">{{ old('description') }}</textarea>
          </div>

          <!-- Buttons -->
          <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-check-circle me-1"></i> Save Product
            </button>
          </div>

        </form>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function generateBarcode() {
    const code = '894' + Math.floor(1000000 + Math.random() * 9000000);
    document.getElementById('code').value = code;
  }

  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
      const output = document.getElementById('imagePreview');
      output.src = reader.result;
      document.getElementById('imagePreviewContainer').style.display = 'block';
    };
    if (event.target.files[0]) {
      reader.readAsDataURL(event.target.files[0]);
    }
  }
</script>
@endpush
