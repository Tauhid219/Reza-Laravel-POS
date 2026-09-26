@extends('layouts.admin')

@section('title', 'Purchase Invoice: ' . $purchase->purchase_no)
@section('page-title', 'Purchase Invoice')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/purchases') }}">Procurement</a></li>
  <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
  <li class="breadcrumb-item active">{{ $purchase->purchase_no }}</li>
@endsection

@section('page-actions')
  <div class="d-flex gap-2">
    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Purchases
    </a>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" onclick="window.print()">
      <i class="bi bi-printer"></i>
      <span>Print Invoice</span>
    </button>
  </div>
@endsection

@push('styles')
<style>
  @media print {
    #header, #sidebar, .pagetitle, .btn, .breadcrumb, .footer {
      display: none !important;
    }
    #main {
      margin: 0 !important;
      padding: 0 !important;
    }
    .card {
      border: none !important;
      box-shadow: none !important;
    }
  }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card shadow-sm p-4">
      <div class="card-body">

        <!-- Invoice Top Header -->
        <div class="row pb-4 mb-4 border-bottom">
          <div class="col-sm-6">
            <h3 class="fw-bold text-primary mb-1">Reza POS</h3>
            <p class="text-muted small mb-0">Retail & Inventory Management System</p>
            <p class="text-muted small mb-0">Purchaser: {{ $purchase->user->name ?? 'Admin' }}</p>
          </div>
          <div class="col-sm-6 text-sm-end">
            <h4 class="fw-bold text-dark mb-1">PURCHASE BILL</h4>
            <div class="fw-bold text-secondary">{{ $purchase->purchase_no }}</div>
            <div class="small text-muted">Date: {{ $purchase->purchase_date->format('d M, Y') }}</div>
            <div class="mt-1">
              @if($purchase->payment_status === 'paid')
                <span class="badge bg-success fs-6">Status: Paid</span>
              @elseif($purchase->payment_status === 'partial')
                <span class="badge bg-warning text-dark fs-6">Status: Partial Payment</span>
              @else
                <span class="badge bg-danger fs-6">Status: Due</span>
              @endif
            </div>
          </div>
        </div>

        <!-- Supplier & Store Details -->
        <div class="row mb-4">
          <div class="col-sm-6">
            <h6 class="fw-bold text-uppercase text-muted small">Supplier Details:</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $purchase->supplier->name }}</h5>
            @if($purchase->supplier->company_name)
              <div class="fw-semibold text-secondary small">{{ $purchase->supplier->company_name }}</div>
            @endif
            <div class="small text-muted mt-1"><i class="bi bi-telephone me-1"></i>{{ $purchase->supplier->phone }}</div>
            @if($purchase->supplier->email)
              <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $purchase->supplier->email }}</div>
            @endif
            @if($purchase->supplier->address)
              <div class="small text-muted mt-1">{{ $purchase->supplier->address }}</div>
            @endif
          </div>
        </div>

        <!-- Itemized Table -->
        <div class="table-responsive mb-4">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 50px;">#</th>
                <th scope="col">Product Item</th>
                <th scope="col" class="text-center">Unit</th>
                <th scope="col" class="text-end">Unit Cost (৳)</th>
                <th scope="col" class="text-center">Quantity</th>
                <th scope="col" class="text-end">Subtotal (৳)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($purchase->items as $index => $item)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>
                    <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                    <small class="text-muted">Code: {{ $item->product->code }}</small>
                  </td>
                  <td class="text-center">{{ $item->product->unit->short_name ?? 'pc' }}</td>
                  <td class="text-end">৳{{ number_format($item->cost_price, 2) }}</td>
                  <td class="text-center fw-bold">{{ $item->quantity }}</td>
                  <td class="text-end fw-bold">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Totals & Notes -->
        <div class="row">
          <div class="col-sm-6">
            @if($purchase->notes)
              <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold text-muted small mb-1">Remarks / Notes:</h6>
                <p class="small text-muted mb-0">{{ $purchase->notes }}</p>
              </div>
            @endif
          </div>

          <div class="col-sm-6">
            <table class="table table-borderless text-end">
              <tr>
                <td class="fw-bold text-muted">Grand Total:</td>
                <td class="fw-bold fs-5 text-dark">৳{{ number_format($purchase->total_amount, 2) }}</td>
              </tr>
              <tr>
                <td class="fw-semibold text-success">Paid Amount:</td>
                <td class="fw-bold fs-5 text-success">৳{{ number_format($purchase->paid_amount, 2) }}</td>
              </tr>
              <tr class="border-top">
                <td class="fw-bold text-danger">Balance Due:</td>
                <td class="fw-bold fs-4 text-danger">৳{{ number_format($purchase->due_amount, 2) }}</td>
              </tr>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
