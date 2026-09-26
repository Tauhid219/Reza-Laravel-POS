@extends('layouts.admin')

@section('title', 'Store & System Settings')
@section('page-title', 'System & Store Settings')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Administration</li>
  <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card shadow-sm">
      <div class="card-body pt-3">

        <!-- Bordered Tabs -->
        <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="store-tab" data-bs-toggle="tab" data-bs-target="#bordered-store" type="button" role="tab">
              <i class="bi bi-shop me-1"></i> Store Profile & Branding
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="billing-tab" data-bs-toggle="tab" data-bs-target="#bordered-billing" type="button" role="tab">
              <i class="bi bi-receipt me-1"></i> POS & Invoicing Config
            </button>
          </li>
        </ul>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="tab-content pt-4" id="borderedTabContent">

            <!-- Tab 1: Store Profile -->
            <div class="tab-pane fade show active" id="bordered-store" role="tabpanel">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Store / Company Name <span class="text-danger">*</span></label>
                  <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                  @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Store Telephone / Hotline</label>
                  <input type="text" name="company_phone" class="form-control @error('company_phone') is-invalid @enderror" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}">
                  @error('company_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Official Email Address</label>
                  <input type="email" name="company_email" class="form-control @error('company_email') is-invalid @enderror" value="{{ old('company_email', $settings['company_email'] ?? '') }}">
                  @error('company_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">BIN / VAT Registration Number</label>
                  <input type="text" name="vat_number" class="form-control @error('vat_number') is-invalid @enderror" value="{{ old('vat_number', $settings['vat_number'] ?? '') }}" placeholder="e.g. 001234567-0101">
                  @error('vat_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold">Store Physical Address</label>
                  <textarea name="company_address" class="form-control @error('company_address') is-invalid @enderror" rows="2">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                  @error('company_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Store Logo (Appears on A4 Invoices)</label>
                  <input type="file" name="company_logo" class="form-control @error('company_logo') is-invalid @enderror" accept="image/*">
                  <small class="text-muted">Recommended: PNG or WEBP with transparent background</small>
                  @error('company_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                  @if(!empty($settings['company_logo']) && file_exists(public_path($settings['company_logo'])))
                    <div class="mt-2 p-2 border rounded bg-light d-inline-block">
                      <img src="{{ asset($settings['company_logo']) }}" alt="Store Logo" style="max-height: 50px;">
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Tab 2: POS & Invoicing Config -->
            <div class="tab-pane fade" id="bordered-billing" role="tabpanel">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label fw-semibold">Currency Symbol <span class="text-danger">*</span></label>
                  <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" required>
                  @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                  <label class="form-label fw-semibold">Currency Code <span class="text-danger">*</span></label>
                  <input type="text" name="currency_code" class="form-control @error('currency_code') is-invalid @enderror" value="{{ old('currency_code', $settings['currency_code'] ?? 'BDT') }}" required>
                  @error('currency_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                  <label class="form-label fw-semibold">Default VAT / Tax (%)</label>
                  <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100" name="default_tax_rate" class="form-control @error('default_tax_rate') is-invalid @enderror" value="{{ old('default_tax_rate', $settings['default_tax_rate'] ?? '0') }}">
                    <span class="input-group-text">%</span>
                  </div>
                  @error('default_tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                  <label class="form-label fw-semibold">Invoice Number Prefix</label>
                  <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}">
                  @error('invoice_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold">Thermal Receipt Footer Message</label>
                  <input type="text" name="receipt_footer" class="form-control @error('receipt_footer') is-invalid @enderror" value="{{ old('receipt_footer', $settings['receipt_footer'] ?? 'Thank you for shopping with us! Please come again.') }}">
                  <small class="text-muted">Printed at the bottom of the 80mm/58mm thermal rolls</small>
                  @error('receipt_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                  <label class="form-label fw-semibold">Invoice Terms & Conditions</label>
                  <textarea name="invoice_terms" class="form-control @error('invoice_terms') is-invalid @enderror" rows="3">{{ old('invoice_terms', $settings['invoice_terms'] ?? '') }}</textarea>
                  <small class="text-muted">Printed at the bottom of official A4 Tax Invoices</small>
                  @error('invoice_terms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

          </div>

          <div class="mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-save me-1"></i> Save Changes
            </button>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
@endsection
