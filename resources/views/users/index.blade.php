@extends('layouts.admin')

@section('title', 'Staff & Users')
@section('page-title', 'Staff & User Management')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Administration</li>
  <li class="breadcrumb-item active">Staff Users</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-shield-check me-1"></i> View Permissions
    </a>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
      <i class="bi bi-person-plus me-1"></i> Add New Staff
    </a>
  </div>
@endsection

@section('content')
<div class="row">
  <div class="col-12">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card shadow-sm mb-3">
      <div class="card-body py-3">
        <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-center">
          <div class="col-md-5">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
              <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-3">
            <select name="role" class="form-select form-select-sm">
              <option value="">All Roles</option>
              @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                  {{ $role->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
              <option value="">All Statuses</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary w-100">
              <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
              <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle"></i>
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
      <div class="card-body pt-3">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>User / Name</th>
                <th>Username</th>
                <th>Email Address</th>
                <th>Phone</th>
                <th class="text-center">Role</th>
                <th class="text-center">Status</th>
                <th class="text-center">Joined</th>
                <th class="text-center pe-3">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                      </div>
                      <div>
                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                        @if($user->id === auth()->id())
                          <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">You (Current)</span>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>{{ $user->username ?? '-' }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->phone ?? 'N/A' }}</td>
                  <td class="text-center">
                    @foreach($user->roles as $role)
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                        {{ $role->name }}
                      </span>
                    @endforeach
                  </td>
                  <td class="text-center">
                    @if($user->status === 'active')
                      <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        Active
                      </span>
                    @else
                      <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                        Inactive
                      </span>
                    @endif
                  </td>
                  <td class="text-center text-muted small">
                    {{ $user->created_at->format('d M, Y') }}
                  </td>
                  <td class="text-center pe-3">
                    <div class="d-flex justify-content-center gap-1">
                      <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-light border" title="Edit Staff">
                        <i class="bi bi-pencil-square text-primary"></i>
                      </a>
                      @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff user?');" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-light border" title="Delete Staff">
                            <i class="bi bi-trash text-danger"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    No staff members found matching criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="p-3 d-flex justify-content-between align-items-center">
          <small class="text-muted">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</small>
          {{ $users->links() }}
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
