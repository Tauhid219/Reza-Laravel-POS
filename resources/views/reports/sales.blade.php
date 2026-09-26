@extends('layouts.admin')

@section('title', 'Sales & Revenue Report')
@section('page-title', 'Sales Analytics & Revenue Report')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Reports</li>
  <li class="breadcrumb-item active">Sales Report</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> Print Report
    </button>
    <a href="{{ route('reports.profit_loss') }}" class="btn btn-outline-primary">
      <i class="bi bi-graph-up me-1"></i> Profit & Loss
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Filter & Date Preset Bar -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm mb-0">
      <div class="card-body py-3">
        <form action="{{ route('reports.sales') }}" method="GET" class="row g-2 align-items-end" id="filterForm">
          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Time Period</label>
            <select name="date_range" class="form-select form-select-sm" id="presetSelect" onchange="toggleCustomDates(this.value)">
              <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today ({{ now()->format('d M') }})</option>
              <option value="yesterday" {{ $dateFilter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
              <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
              <option value="last_week" {{ $dateFilter == 'last_week' ? 'selected' : '' }}>Last Week</option>
              <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month ({{ now()->format('F Y') }})</option>
              <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Last Month ({{ now()->subMonth()->format('F Y') }})</option>
              <option value="this_year" {{ $dateFilter == 'this_year' ? 'selected' : '' }}>This Year ({{ now()->format('Y') }})</option>
              <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
            </select>
          </div>

          <div class="col-md-2" id="customStartCol" style="{{ $dateFilter == 'custom' ? '' : 'display:none;' }}">
            <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
          </div>

          <div class="col-md-2" id="customEndCol" style="{{ $dateFilter == 'custom' ? '' : 'display:none;' }}">
            <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Cashier / Staff</label>
            <select name="user_id" class="form-select form-select-sm">
              <option value="">All Cashiers</option>
              @foreach($cashiers as $cashier)
                <option value="{{ $cashier->id }}" {{ request('user_id') == $cashier->id ? 'selected' : '' }}>
                  {{ $cashier->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Payment Method</label>
            <select name="payment_type" class="form-select form-select-sm">
              <option value="">All Methods</option>
              <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="card" {{ request('payment_type') == 'card' ? 'selected' : '' }}>Card</option>
              <option value="mobile_banking" {{ request('payment_type') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
              <option value="credit" {{ request('payment_type') == 'credit' ? 'selected' : '' }}>Credit</option>
              <option value="split" {{ request('payment_type') == 'split' ? 'selected' : '' }}>Split</option>
            </select>
          </div>

          <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter">
              <i class="bi bi-funnel"></i> Filter
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- KPI Metrics Cards -->
  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-primary border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-currency-dollar"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalSales, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Gross Revenue</span>
            <div class="text-primary small fw-semibold mt-1">{{ $totalOrders }} Completed Orders</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-success border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-graph-up-arrow"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalGrossProfit, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Gross Profit</span>
            <div class="text-success small fw-semibold mt-1">
              Margin: {{ $totalSales > 0 ? number_format(($totalGrossProfit / $totalSales) * 100, 1) : 0 }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-info border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalPaid, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Net Collections</span>
            <div class="text-muted small mt-1">VAT: ৳{{ number_format($totalTax, 2) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-danger border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-exclamation-triangle"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalDue, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Unsettled Due</span>
            <div class="text-danger small fw-semibold mt-1">
              <a href="{{ route('reports.customer_due') }}" class="text-danger text-decoration-none">View Due Aging &rarr;</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Left: Daily Breakdown Table -->
  <div class="col-lg-8 mb-4">
    <div class="card shadow-sm h-100 mb-0">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-calendar-event me-2 text-primary"></i> Daily Sales Trend
        </h5>
        <span class="text-muted small">{{ $startDate->format('d M, Y') }} — {{ $endDate->format('d M, Y') }}</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Date</th>
                <th class="text-center">Orders</th>
                <th class="text-end">Sales (BDT)</th>
                <th class="text-end">COGS Cost</th>
                <th class="text-end text-success">Gross Profit</th>
                <th class="text-end pe-3 text-danger">Due</th>
              </tr>
            </thead>
            <tbody>
              @forelse($dailyTrends as $day)
                <tr>
                  <td class="ps-3 fw-semibold text-dark">
                    {{ Carbon\Carbon::parse($day->date)->format('d M, Y (D)') }}
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $day->orders_count }}</span>
                  </td>
                  <td class="text-end fw-bold">৳{{ number_format($day->sales, 2) }}</td>
                  <td class="text-end text-muted">৳{{ number_format($day->cost, 2) }}</td>
                  <td class="text-end fw-bold text-success">৳{{ number_format($day->sales - $day->cost, 2) }}</td>
                  <td class="text-end pe-3 text-danger fw-semibold">
                    {{ $day->due > 0 ? '৳' . number_format($day->due, 2) : '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    No sales recorded for the selected period.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Payment Method Breakdown -->
  <div class="col-lg-4 mb-4">
    <div class="card shadow-sm h-100 mb-0">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-pie-chart me-2 text-primary"></i> Payment Methods Mix
        </h5>
      </div>
      <div class="card-body">
        @if($paymentMethods->count() > 0)
          <div class="list-group list-group-flush mb-3">
            @foreach($paymentMethods as $pm)
              @php
                $pct = $totalSales > 0 ? ($pm->total / $totalSales) * 100 : 0;
              @endphp
              <div class="list-group-item px-0 py-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-semibold text-capitalize">
                    <i class="bi bi-check2-circle text-primary me-1"></i> {{ str_replace('_', ' ', $pm->payment_type) }}
                  </span>
                  <span class="fw-bold text-dark">৳{{ number_format($pm->total, 2) }}</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-1">
                  <span>{{ $pm->count }} transaction(s)</span>
                  <span>{{ number_format($pct, 1) }}% of revenue</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-4 text-muted">No payment records found.</div>
        @endif
      </div>
    </div>
  </div>

  <!-- Orders Transaction Table -->
  <div class="col-12">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-receipt me-2 text-primary"></i> Filtered Sales Invoices ({{ $orders->total() }})
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Invoice #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Billed By</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Due</th>
                <th class="text-center">Method</th>
                <th class="text-center pe-3">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td class="ps-3">
                    <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary">
                      {{ $order->invoice_no }}
                    </a>
                  </td>
                  <td>{{ $order->order_date ? $order->order_date->format('d M Y, h:i A') : $order->created_at->format('d M Y, h:i A') }}</td>
                  <td>{{ $order->customer?->name ?? 'Walk-in Customer' }}</td>
                  <td>{{ $order->user?->name ?? 'Staff' }}</td>
                  <td class="text-end fw-bold">৳{{ number_format($order->total_amount, 2) }}</td>
                  <td class="text-end text-success fw-semibold">৳{{ number_format($order->paid_amount, 2) }}</td>
                  <td class="text-end">
                    @if($order->due_amount > 0)
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                        ৳{{ number_format($order->due_amount, 2) }}
                      </span>
                    @else
                      <span class="text-muted small">৳0.00</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border text-uppercase">
                      {{ str_replace('_', ' ', $order->payment_type) }}
                    </span>
                  </td>
                  <td class="text-center pe-3">
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light border" title="View Details">
                      <i class="bi bi-eye text-primary"></i>
                    </a>
                    <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="btn btn-sm btn-light border" title="Thermal Receipt">
                      <i class="bi bi-printer text-success"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-4 text-muted">
                    No orders matching selected criteria.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="p-3 d-flex justify-content-between align-items-center">
          <small class="text-muted">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</small>
          {{ $orders->links() }}
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function toggleCustomDates(preset) {
    const startCol = document.getElementById('customStartCol');
    const endCol = document.getElementById('customEndCol');
    if (preset === 'custom') {
      startCol.style.display = 'block';
      endCol.style.display = 'block';
    } else {
      startCol.style.display = 'none';
      endCol.style.display = 'none';
      document.getElementById('filterForm').submit();
    }
  }
</script>
@endpush
@endsection
