@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page-title', 'Role-Based Access Control (RBAC)')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Administration</li>
  <li class="breadcrumb-item active">Roles & Permissions</li>
@endsection

@section('page-actions')
  <a href="{{ route('users.index') }}" class="btn btn-outline-primary">
    <i class="bi bi-people me-1"></i> Staff Directory
  </a>
@endsection

@section('content')
<div class="row">

  <!-- Roles Summary Cards -->
  @foreach($roles as $role)
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm h-100 mb-0 border-top border-primary border-3">
        <div class="card-body pt-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title p-0 m-0 fw-bold">{{ $role->name }}</h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
              {{ $role->users->count() }} user(s)
            </span>
          </div>

          <p class="text-muted small mb-3">
            @if($role->name === 'Admin')
              Full unrestricted access to all POS, procurement, inventory, financials, settings, and staff modules.
            @elseif($role->name === 'Manager')
              Operational supervision: manage products, categories, stock purchases, orders, and view sales reports.
            @elseif($role->name === 'Cashier')
              POS counter operations: open/close cash drawer shifts, live barcode scanning, process checkouts, and print receipts.
            @endif
          </p>

          <h6 class="fw-bold small text-dark mb-2">Assigned Permissions ({{ $role->permissions->count() }}):</h6>
          <div class="d-flex flex-wrap gap-1" style="max-height: 180px; overflow-y: auto;">
            @forelse($role->permissions as $perm)
              <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                {{ $perm->name }}
              </span>
            @empty
              <span class="text-muted small">No specific permissions assigned.</span>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  @endforeach

  <!-- Permissions Master List Grouped by Module -->
  <div class="col-12 mt-2">
    <div class="card shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-shield-check me-2 text-primary"></i> System Permissions Matrix
        </h5>
      </div>
      <div class="card-body pt-3">
        <div class="row g-4">
          @foreach($permissions as $module => $modulePerms)
            <div class="col-md-4 col-sm-6">
              <div class="p-3 border rounded bg-light h-100">
                <h6 class="fw-bold text-primary text-uppercase small mb-2">
                  <i class="bi bi-folder2-open me-1"></i> {{ $module }} Module
                </h6>
                <ul class="list-unstyled mb-0">
                  @foreach($modulePerms as $p)
                    <li class="small text-secondary py-1 border-bottom border-light">
                      <i class="bi bi-check2 text-success me-1"></i> {{ $p->name }}
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
