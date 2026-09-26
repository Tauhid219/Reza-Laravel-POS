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
                <h6>৳{{ number_format($todaySales, 2) }}</h6>
                <span class="text-success small pt-1 fw-bold">{{ $todayOrdersCount }}</span> <span class="text-muted small pt-2 ps-1">orders today</span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Sales Card -->

      <!-- Revenue / Total Collection Card -->
      <div class="col-xxl-4 col-md-6">
        <div class="card info-card revenue-card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">This Month Sales <span>| {{ date('F Y') }}</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="ps-3">
                <h6>৳{{ number_format($monthlyRevenue, 2) }}</h6>
                <span class="text-muted small pt-2">Total monthly sales</span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Revenue Card -->

      <!-- Customer Due Card -->
      <div class="col-xxl-4 col-xl-12">
        <div class="card info-card customers-card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Customer Due <span>| Outstanding</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger">
                <i class="bi bi-exclamation-diamond"></i>
              </div>
              <div class="ps-3">
                <h6>৳{{ number_format($totalCustomerDue, 2) }}</h6>
                <span class="text-danger small pt-1 fw-bold">{{ $dueInvoicesCount }}</span> <span class="text-muted small pt-2 ps-1">pending invoices</span>
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
                <i class="bi bi-person-plus"></i> Customers ({{ $totalCustomersCount }})
              </a>
              <a href="{{ url('/products') }}" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-box-seam"></i> Products Catalog ({{ $totalProductsCount }})
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
            <h5 class="card-title">Recent Sales <span>| Latest Transactions</span></h5>

            @if($recentOrders->count() > 0)
              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">Invoice #</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentOrders as $order)
                    <tr>
                      <th scope="row"><a href="{{ url('/orders/' . $order->id) }}">{{ $order->invoice_no }}</a></th>
                      <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                      <td>৳{{ number_format($order->total_amount, 2) }}</td>
                      <td>
                        @if($order->payment_status === 'paid')
                          <span class="badge bg-success">Paid</span>
                        @elseif($order->payment_status === 'partial')
                          <span class="badge bg-warning text-dark">Partial</span>
                        @else
                          <span class="badge bg-danger">Due</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <p class="mb-2">No sales transactions recorded yet.</p>
                <a href="{{ url('/pos') }}" class="btn btn-sm btn-primary">Go to POS to make your first sale</a>
              </div>
            @endif

          </div>
        </div>
      </div><!-- End Recent Sales -->

    </div>
  </div><!-- End Left side columns -->

  <!-- Right side columns (4 cols) -->
  <div class="col-lg-4">

    <!-- Active Register / Shift Status Card -->
    <div class="card shadow-sm border-start border-{{ $activeRegister ? 'success' : 'warning' }} border-4">
      <div class="card-body pt-3">
        <h5 class="card-title py-0 mb-2 d-flex justify-content-between align-items-center">
          <span>Cash Register</span>
          @if($activeRegister)
            <span class="badge bg-success"><i class="bi bi-unlock me-1"></i> Open</span>
          @else
            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i> Closed</span>
          @endif
        </h5>

        @if($activeRegister)
          <div class="small text-muted mb-2">
            <div><strong>Opened at:</strong> {{ $activeRegister->opened_at->format('d M, h:i A') }}</div>
            <div><strong>Opening Float:</strong> ৳{{ number_format($activeRegister->cash_in_hand, 2) }}</div>
            <div><strong>Shift Sales:</strong> ৳{{ number_format($activeRegister->cash_sales, 2) }}</div>
          </div>
          <a href="{{ url('/cash-register') }}" class="btn btn-sm btn-outline-danger w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Close Register / Shift
          </a>
        @else
          <p class="small text-muted mb-3">Open your shift drawer with opening cash to start POS transactions.</p>
          <a href="{{ url('/cash-register') }}" class="btn btn-sm btn-outline-primary w-100">
            <i class="bi bi-box-arrow-in-right me-1"></i> Open Register / Shift
          </a>
        @endif
      </div>
    </div>

    <!-- Inventory / Low Stock Alert -->
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title d-flex justify-content-between align-items-center">
          <span>Low Stock Alert</span>
          @if($lowStockProducts->count() > 0)
            <span class="badge bg-danger">{{ $lowStockProducts->count() }} Items</span>
          @endif
        </h5>

        @if($lowStockProducts->count() > 0)
          <div class="list-group list-group-flush">
            @foreach($lowStockProducts as $lowItem)
              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                  <div class="fw-semibold text-truncate" style="max-width: 170px;">{{ $lowItem->name }}</div>
                  <small class="text-muted">Code: {{ $lowItem->code }}</small>
                </div>
                <div class="text-end">
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                    {{ $lowItem->stock_quantity }} {{ $lowItem->unit->short_name ?? 'pcs' }}
                  </span>
                  <div class="small text-muted">Alert: {{ $lowItem->alert_quantity }}</div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="mt-3">
            <a href="{{ url('/purchases/create') }}" class="btn btn-sm btn-outline-success w-100">
              <i class="bi bi-plus-circle me-1"></i> Purchase Stock In
            </a>
          </div>
        @else
          <div class="text-center py-4 text-muted">
            <i class="bi bi-check-circle text-success fs-2 d-block mb-1"></i>
            <span class="small">All inventory stock levels are healthy!</span>
          </div>
        @endif

      </div>
    </div><!-- End Stock Alert -->

  </div><!-- End Right side columns -->

</div>
@endsection
