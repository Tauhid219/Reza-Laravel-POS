@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'POS Dashboard')

@section('breadcrumb')
  <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
  <a href="{{ url('/pos') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
    <i class="bi bi-cart-check fs-5"></i>
    <span>Open POS Terminal</span>
  </a>
@endsection

@section('content')
<div class="row">

  <!-- Left side columns (8 cols) -->
  <div class="col-lg-8">
    <div class="row">

      <!-- Today's Sales Card -->
      <div class="col-xxl-4 col-md-6">
        <div class="card info-card sales-card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Today's Sales <span>| Today</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary">
                <i class="bi bi-cart"></i>
              </div>
              <div class="ps-3">
                <h6>৳0.00</h6>
                <span class="text-success small pt-1 fw-bold">0</span> <span class="text-muted small pt-2 ps-1">orders today</span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Sales Card -->

      <!-- Revenue / Total Collection Card -->
      <div class="col-xxl-4 col-md-6">
        <div class="card info-card revenue-card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Revenue <span>| This Month</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="ps-3">
                <h6>৳0.00</h6>
                <span class="text-success small pt-1 fw-bold">0%</span> <span class="text-muted small pt-2 ps-1">increase</span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Revenue Card -->

      <!-- Customer Due Card -->
      <div class="col-xxl-4 col-xl-12">
        <div class="card info-card customers-card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Customer Due <span>| Outstanding</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger">
                <i class="bi bi-exclamation-diamond"></i>
              </div>
              <div class="ps-3">
                <h6>৳0.00</h6>
                <span class="text-danger small pt-1 fw-bold">0</span> <span class="text-muted small pt-2 ps-1">pending invoices</span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Customers Card -->

      <!-- Quick Action Shortcuts -->
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body py-3">
            <h5 class="card-title py-1 mb-2">Quick Shortcuts</h5>
            <div class="d-flex flex-wrap gap-2">
              <a href="{{ url('/pos') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-cart4"></i> Start New Sale (POS)
              </a>
              <a href="{{ url('/products/create') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i> Add Product
              </a>
              <a href="{{ url('/purchases/create') }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-truck"></i> New Stock Purchase
              </a>
              <a href="{{ url('/customers') }}" class="btn btn-outline-info btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-person-plus"></i> Add Customer
              </a>
              <a href="{{ url('/cash-register') }}" class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-cash-stack"></i> Shift Register
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Sales / Orders -->
      <div class="col-12">
        <div class="card recent-sales overflow-auto shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Recent Sales <span>| Latest 5 Transactions</span></h5>

            <div class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
              <p class="mb-2">No sales transactions recorded yet.</p>
              <a href="{{ url('/pos') }}" class="btn btn-sm btn-primary">Go to POS to make your first sale</a>
            </div>

          </div>
        </div>
      </div><!-- End Recent Sales -->

    </div>
  </div><!-- End Left side columns -->

  <!-- Right side columns (4 cols) -->
  <div class="col-lg-4">

    <!-- Active Register / Shift Status Card -->
    <div class="card shadow-sm border-start border-primary border-4">
      <div class="card-body pt-3">
        <h5 class="card-title py-0 mb-2 d-flex justify-content-between align-items-center">
          <span>Cash Register</span>
          <span class="badge bg-secondary">Closed</span>
        </h5>
        <p class="small text-muted mb-3">Open your shift drawer to start accepting payments and track cash flow.</p>
        <a href="{{ url('/cash-register') }}" class="btn btn-sm btn-outline-primary w-100">
          <i class="bi bi-box-arrow-in-right me-1"></i> Open Register / Shift
        </a>
      </div>
    </div>

    <!-- Inventory / Low Stock Alert -->
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Low Stock Alert <span>| Items &lt; 5 qty</span></h5>

        <div class="text-center py-4 text-muted">
          <i class="bi bi-check-circle text-success fs-2 d-block mb-1"></i>
          <span class="small">All inventory stock levels are healthy!</span>
        </div>

      </div>
    </div><!-- End Stock Alert -->

    <!-- Top Selling Products -->
    <div class="card top-selling overflow-auto shadow-sm">
      <div class="card-body pb-0">
        <h5 class="card-title">Top Selling Products <span>| This Month</span></h5>

        <div class="text-center py-4 text-muted">
          <span class="small">No sales data available yet.</span>
        </div>

      </div>
    </div><!-- End Top Selling -->

  </div><!-- End Right side columns -->

</div>
@endsection
