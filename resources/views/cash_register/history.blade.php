@extends('layouts.admin')

@section('title', 'Shift History Logs')
@section('page-title', 'Cash Register Shift Logs')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('cash_register.index') }}">Cash Register</a></li>
  <li class="breadcrumb-item active">History</li>
@endsection

@section('page-actions')
  <a href="{{ route('cash_register.index') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
    <i class="bi bi-wallet2"></i>
    <span>Current Shift</span>
  </a>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">

        <!-- Filter Form -->
        <form method="GET" action="{{ route('cash_register.history') }}" class="row g-2 pt-3 pb-3 mb-2 border-bottom align-items-end">
          <div class="col-md-4">
            <label class="form-label small fw-semibold text-muted">Cashier / Staff</label>
            <select name="user_id" class="form-select">
              <option value="">All Cashiers</option>
              @foreach($staffUsers as $staff)
                <option value="{{ $staff->id }}" {{ request('user_id') == $staff->id ? 'selected' : '' }}>
                  {{ $staff->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">From Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">To Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
          </div>

          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->anyFilled(['user_id', 'start_date', 'end_date']))
              <a href="{{ route('cash_register.history') }}" class="btn btn-outline-secondary" title="Clear Filters">
                <i class="bi bi-x-lg"></i>
              </a>
            @endif
          </div>
        </form>

        <!-- Shift Register Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Cashier</th>
                <th scope="col">Shift Duration</th>
                <th scope="col" class="text-end">Opening Float</th>
                <th scope="col" class="text-end">Cash Sales</th>
                <th scope="col" class="text-end">Expected</th>
                <th scope="col" class="text-end">Counted Cash</th>
                <th scope="col" class="text-center">Variance (Diff)</th>
                <th scope="col">Notes</th>
              </tr>
            </thead>
            <tbody>
              @forelse($registers as $reg)
                @php
                  $expected = $reg->cash_in_hand + $reg->cash_sales;
                @endphp
                <tr>
                  <td>
                    <div class="fw-bold text-dark">{{ $reg->user->name ?? 'Staff' }}</div>
                    <small class="text-muted">{{ $reg->user->email ?? '' }}</small>
                  </td>
                  <td>
                    <div class="small fw-semibold">{{ $reg->opened_at->format('d M, h:i A') }}</div>
                    <div class="small text-muted">to {{ $reg->closed_at ? $reg->closed_at->format('h:i A') : 'N/A' }}</div>
                  </td>
                  <td class="text-end">৳{{ number_format($reg->cash_in_hand, 2) }}</td>
                  <td class="text-end text-success fw-semibold">৳{{ number_format($reg->cash_sales, 2) }}</td>
                  <td class="text-end fw-bold">৳{{ number_format($expected, 2) }}</td>
                  <td class="text-end fw-bold text-primary">৳{{ number_format($reg->total_cash_submitted, 2) }}</td>
                  <td class="text-center">
                    @if($reg->difference == 0)
                      <span class="badge bg-secondary-subtle text-secondary border">Balanced</span>
                    @elseif($reg->difference > 0)
                      <span class="badge bg-success">+৳{{ number_format($reg->difference, 2) }} (Surplus)</span>
                    @else
                      <span class="badge bg-danger">-৳{{ number_format(abs($reg->difference), 2) }} (Shortage)</span>
                    @endif
                  </td>
                  <td class="small text-muted">{{ Str::limit($reg->note ?? '—', 30) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                    No closed shift records found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $registers->links() }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
