@extends('layouts.admin')

@section('title', 'Profit & Loss Statement')
@section('page-title', 'Profit & Loss Analytics')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item">Reports</li>
  <li class="breadcrumb-item active">Profit & Loss</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> Print Statement
    </button>
    <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary">
      <i class="bi bi-receipt me-1"></i> Sales Report
    </a>
  </div>
@endsection

@section('content')
<div class="row">

  <!-- Preset Filter Bar -->
  <div class="col-12 mb-3">
    <div class="card shadow-sm mb-0">
      <div class="card-body py-3">
        <form action="{{ route('reports.profit_loss') }}" method="GET" class="row g-2 align-items-end" id="filterForm">
          <div class="col-md-4">
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

          <div class="col-md-3" id="customStartCol" style="{{ $dateFilter == 'custom' ? '' : 'display:none;' }}">
            <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
          </div>

          <div class="col-md-3" id="customEndCol" style="{{ $dateFilter == 'custom' ? '' : 'display:none;' }}">
            <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
          </div>

          <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary w-100">
              <i class="bi bi-funnel me-1"></i> Recalculate
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- P&L Metric Cards -->
  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-primary border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-cash-stack"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalSales, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Gross Revenue</span>
            <div class="text-muted small mt-1">From {{ $ordersCount }} orders</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-secondary border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-box-seam"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalCost, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">COGS (Goods Cost)</span>
            <div class="text-muted small mt-1">Direct product costs</div>
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
            <h6 class="fs-4 mb-0 fw-bold text-success">৳{{ number_format($totalGrossProfit, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Gross Profit</span>
            <div class="text-success small fw-semibold mt-1">
              Net Margin: {{ number_format($profitMargin, 1) }}%
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xxl-3 col-md-6 mb-3">
    <div class="card info-card shadow-sm h-100 mb-0 border-start border-warning border-4">
      <div class="card-body py-3">
        <div class="d-flex align-items-center">
          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 48px; height: 48px; font-size: 24px;">
            <i class="bi bi-tag"></i>
          </div>
          <div class="ps-3">
            <h6 class="fs-4 mb-0 fw-bold">৳{{ number_format($totalDiscounts, 2) }}</h6>
            <span class="text-muted small pt-1 fw-semibold">Total Discounts</span>
            <div class="text-muted small mt-1">VAT Collected: ৳{{ number_format($totalTax, 2) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Left: Category-wise Profitability Breakdown -->
  <div class="col-lg-7 mb-4">
    <div class="card shadow-sm h-100 mb-0">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-tags me-2 text-primary"></i> Category-wise Profit Contribution
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Category</th>
                <th class="text-center">Qty Sold</th>
                <th class="text-end">Sales (BDT)</th>
                <th class="text-end">Cost (COGS)</th>
                <th class="text-end text-success">Gross Profit</th>
                <th class="text-end pe-3">Margin %</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categoryBreakdown as $cat)
                @php
                  $margin = $cat->total_sales > 0 ? ($cat->gross_profit / $cat->total_sales) * 100 : 0;
                @endphp
                <tr>
                  <td class="ps-3 fw-bold text-dark">{{ $cat->category_name }}</td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $cat->total_qty }}</span>
                  </td>
                  <td class="text-end fw-semibold">৳{{ number_format($cat->total_sales, 2) }}</td>
                  <td class="text-end text-muted">৳{{ number_format($cat->total_cost, 2) }}</td>
                  <td class="text-end fw-bold text-success">৳{{ number_format($cat->gross_profit, 2) }}</td>
                  <td class="text-end pe-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                      {{ number_format($margin, 1) }}%
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    No sales items found in this timeframe.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Top 10 Most Profitable Items -->
  <div class="col-lg-5 mb-4">
    <div class="card shadow-sm h-100 mb-0">
      <div class="card-header bg-white py-3">
        <h5 class="card-title m-0 fw-bold fs-6">
          <i class="bi bi-trophy me-2 text-primary"></i> Top 10 Profitable Products
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Product</th>
                <th class="text-center">Qty</th>
                <th class="text-end pe-3">Profit Generated</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topProfitableProducts as $prod)
                <tr>
                  <td class="ps-3">
                    <div class="fw-semibold text-dark">{{ $prod->product_name }}</div>
                    <small class="text-muted">Rev: ৳{{ number_format($prod->total_revenue, 1) }}</small>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-light text-dark border">{{ $prod->total_qty }}</span>
                  </td>
                  <td class="text-end pe-3 fw-bold text-success">
                    +৳{{ number_format($prod->profit, 2) }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">
                    No product profit data available.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
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
