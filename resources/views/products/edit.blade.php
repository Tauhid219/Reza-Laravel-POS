@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product: ' . $product->name)

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/products') }}">Inventory</a></li>
  <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm">
      <div class="card-body pt-3">
        <h5 class="card-title pb-2 border-bottom">Edit Product Details</h5>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="row g-3 mt-1">
          @csrf
          @method('PUT')

          <!-- Product Name -->
          <div class="col-md-8">
            <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Product Code / Barcode -->
          <div class="col-md-4">
            <label for="code" class="form-label fw-semibold">Barcode / SKU <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $product->code) }}" required>
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
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
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
              <input type="number" step="0.01" min="0" name="cost_price" id="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', $product->cost_price) }}" required>
            </div>
            @error('cost_price')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Selling Price -->
          <div class="col-md-3">
            <label for="selling_price" class="form-label fw-semibold">Selling Price (Retail) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', $product->selling_price) }}" required>
            </div>
            @error('selling_price')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Stock Quantity -->
          <div class="col-md-3">
            <label for="stock_quantity" class="form-label fw-semibold">Current Stock <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="stock_quantity" id="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
            @error('stock_quantity')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Alert Quantity -->
          <div class="col-md-3">
            <label for="alert_quantity" class="form-label fw-semibold">Low Stock Alert Qty <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="alert_quantity" id="alert_quantity" class="form-control @error('alert_quantity') is-invalid @enderror" value="{{ old('alert_quantity', $product->alert_quantity) }}" required>
            @error('alert_quantity')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Product Image -->
          <div class="col-md-6">
            <label for="image" class="form-label fw-semibold">Product Image</label>
            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
            <small class="text-muted">Upload to replace existing image (Max 2MB)</small>
            @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div class="mt-2" id="imagePreviewContainer">
              @if($product->image)
                <img id="imagePreview" src="{{ asset($product->image) }}" alt="Current" class="rounded border p-1" style="max-height: 120px;">
              @else
                <img id="imagePreview" src="#" alt="Preview" class="rounded border p-1" style="max-height: 120px; display: none;">
              @endif
            </div>
          </div>

          <!-- Status -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
              <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>Active (Available for sale)</option>
              <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>Inactive (Hide from POS)</option>
            </select>
          </div>

          <!-- Description -->
          <div class="col-12">
            <label for="description" class="form-label fw-semibold">Description / Notes</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
          </div>

          <!-- Buttons -->
          <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-check-circle me-1"></i> Update Product
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
  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
      const output = document.getElementById('imagePreview');
      output.src = reader.result;
      output.style.display = 'block';
    };
    if (event.target.files[0]) {
      reader.readAsDataURL(event.target.files[0]);
    }
  }
</script>
@endpush
