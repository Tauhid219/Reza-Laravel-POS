@extends('layouts.admin')

@section('title', 'Print Product Barcodes')
@section('page-title', 'Print Barcode Labels')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/products') }}">Inventory</a></li>
  <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
  <li class="breadcrumb-item active">Barcodes</li>
@endsection

@section('page-actions')
  <button type="button" class="btn btn-success d-flex align-items-center gap-1 shadow-sm" onclick="window.print()">
    <i class="bi bi-printer"></i>
    <span>Print Labels</span>
  </button>
@endsection

@push('styles')
<style>
  .barcode-sticker {
    width: 200px;
    height: 125px;
    border: 1px dashed #ccc;
    padding: 8px;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #fff;
    margin: 6px;
    border-radius: 4px;
    page-break-inside: avoid;
  }

  .barcode-sticker .brand {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #333;
  }

  .barcode-sticker .item-name {
    font-size: 12px;
    font-weight: 600;
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
  }

  .barcode-sticker .barcode-svg {
    max-width: 170px;
    height: 42px;
  }

  .barcode-sticker .barcode-svg svg {
    width: 100%;
    height: 100%;
  }

  .barcode-sticker .price {
    font-size: 13px;
    font-weight: 700;
    color: #111;
    margin-top: 2px;
  }

  @media print {
    #header, #sidebar, .pagetitle, .card-filter, .footer, .btn, .breadcrumb {
      display: none !important;
    }
    #main {
      margin: 0 !important;
      padding: 0 !important;
    }
    .barcode-sticker {
      border: 1px solid #ddd;
      box-shadow: none;
    }
  }
</style>
@endpush

@section('content')
<div class="row">

  <!-- Control & Filter Box -->
  <div class="col-12 card-filter">
    <div class="card shadow-sm mb-3">
      <div class="card-body pt-3 pb-3">
        <form method="GET" action="{{ route('products.barcode') }}" class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Select Product to Print</label>
            <select name="product" class="form-select" onchange="this.form.submit()">
              @foreach($products as $p)
                <option value="{{ $p->id }}" {{ $selectedProduct && $selectedProduct->id == $p->id ? 'selected' : '' }}>
                  {{ $p->name }} (Code: {{ $p->code }} | ৳{{ number_format($p->selling_price, 2) }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Number of Labels</label>
            <input type="number" name="quantity" class="form-control" min="1" max="100" value="{{ $quantity }}">
          </div>

          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
              <i class="bi bi-arrow-repeat me-1"></i> Update
            </button>
            <button type="button" class="btn btn-success flex-grow-1" onclick="window.print()">
              <i class="bi bi-printer me-1"></i> Print
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Barcode Stickers Grid -->
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body pt-3">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom card-filter">
          <h6 class="fw-bold mb-0 text-dark">Barcode Label Preview ({{ $quantity }} Labels)</h6>
          <small class="text-muted">Designed for standard 50x30mm or 40x25mm thermal stickers / A4 sheets</small>
        </div>

        @if($selectedProduct && $barcodeSvg)
          <div class="d-flex flex-wrap justify-content-center p-2 bg-light rounded border">
            @for($i = 0; $i < $quantity; $i++)
              <div class="barcode-sticker">
                <div class="brand">Reza POS</div>
                <div class="item-name">{{ $selectedProduct->name }}</div>
                <div class="barcode-svg">
                  {!! $barcodeSvg !!}
                </div>
                <div class="price">Price: ৳{{ number_format($selectedProduct->selling_price, 2) }}</div>
              </div>
            @endfor
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="bi bi-upc-scan fs-1 d-block mb-2 text-secondary"></i>
            Please select or add a product to preview barcodes.
          </div>
        @endif

      </div>
    </div>
  </div>

</div>
@endsection
