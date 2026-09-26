@extends('layouts.admin')

@section('title', 'Add New Staff')
@section('page-title', 'Create Staff User')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Staff</a></li>
  <li class="breadcrumb-item active">New Staff</li>
@endsection

@section('page-actions')
  <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Back to Staff List
  </a>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">

    <div class="card shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-person-plus me-2 text-primary"></i> New Staff Member Information
        </h5>
      </div>
      <div class="card-body pt-4">

        <form action="{{ route('users.store') }}" method="POST">
          @csrf

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Tanvir Ahmed">
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Username</label>
              <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="e.g. tanvir">
              @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="e.g. tanvir@rezapos.com">
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Phone Number</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. 01711223344">
              @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">Select Role</option>
                @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                    {{ $role->name }}
                  </option>
                @endforeach
              </select>
              @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
              <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
              @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimum 8 characters">
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" class="form-control" required placeholder="Re-type password">
            </div>
          </div>

          <div class="mt-4 pt-3 border-top d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-check-circle me-1"></i> Create Staff Member
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
@endsection
