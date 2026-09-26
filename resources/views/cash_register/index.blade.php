@extends('layouts.admin')

@section('title', 'Cash Register Shift')
@section('page-title', 'Cash Register / Shift Drawer')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item active">Cash Register</li>
@endsection

@section('page-actions')
  <a href="{{ route('cash_register.history') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-sm">
    <i class="bi bi-clock-history"></i>
    <span>Shift History</span>
  </a>
@endsection

@section('content')
<div class="row justify-content-center">

  @if($activeRegister)
    <!-- ================= REGISTER IS CURRENTLY OPEN ================= -->
    <div class="col-lg-8">
      <div class="card shadow-sm border-top border-success border-4 mb-4">
        <div class="card-body pt-3">
          <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
            <div>
              <h5 class="card-title p-0 m-0">Active Shift Drawer</h5>
              <small class="text-muted">Cashier: <strong>{{ Auth::user()->name }}</strong></small>
            </div>
            <span class="badge bg-success fs-6 px-3 py-2">
              <i class="bi bi-unlock-fill me-1"></i> Register is OPEN
            </span>
          </div>

          <div class="row g-3 py-2 text-center">
            <div class="col-md-4">
              <div class="p-3 bg-light rounded border">
                <span class="text-muted small d-block mb-1">Opening Cash Float</span>
                <span class="fs-4 fw-bold text-dark">৳{{ number_format($activeRegister->cash_in_hand, 2) }}</span>
                <div class="small text-muted mt-1">{{ $activeRegister->opened_at->format('h:i A, d M') }}</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="p-3 bg-light rounded border">
                <span class="text-muted small d-block mb-1">Shift Cash Sales</span>
                <span class="fs-4 fw-bold text-success">৳{{ number_format($activeRegister->cash_sales, 2) }}</span>
                <div class="small text-muted mt-1">{{ $shiftOrders->count() }} transactions</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="p-3 bg-primary-subtle text-primary rounded border border-primary-subtle">
                <span class="small d-block mb-1 fw-semibold">Expected Cash in Drawer</span>
                <span class="fs-4 fw-bold">৳{{ number_format($expectedCash, 2) }}</span>
                <div class="small mt-1">(Float + Cash Sales)</div>
              </div>
            </div>
          </div>

          <!-- Close Shift Form -->
          <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold text-danger mb-2"><i class="bi bi-lock-fill me-1"></i> End Shift & Close Register</h6>
            <p class="small text-muted mb-3">Count all physical currency notes and coins in your cash drawer and enter the actual total amount below.</p>

            <form action="{{ route('cash_register.close', $activeRegister) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this shift drawer?');">
              @csrf

              <div class="row g-3">
                <div class="col-md-6">
                  <label for="total_cash_submitted" class="form-label fw-semibold">Counted Physical Cash in Drawer (৳) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text">৳</span>
                    <input type="number" step="0.01" min="0" name="total_cash_submitted" id="total_cash_submitted" class="form-control fs-5 fw-bold" placeholder="0.00" oninput="calculateDrawerDiff({{ $expectedCash }})" required>
                  </div>
                  <div id="diffPreview" class="small mt-1 fw-bold text-muted"></div>
                </div>

                <div class="col-md-6">
                  <label for="close_note" class="form-label fw-semibold">Closing Notes / Discrepancy Reason</label>
                  <input type="text" name="note" id="close_note" class="form-control" placeholder="Optional notes for shift manager...">
                </div>

                <div class="col-12 text-end">
                  <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-right me-1"></i> Submit & Close Register
                  </button>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>

      <!-- Shift Orders Table -->
      <div class="card shadow-sm">
        <div class="card-body pt-3">
          <h5 class="card-title pb-2 border-bottom">Shift Sales Transactions ({{ $shiftOrders->count() }})</h5>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col">Invoice #</th>
                  <th scope="col">Customer</th>
                  <th scope="col">Time</th>
                  <th scope="col" class="text-end">Total</th>
                  <th scope="col" class="text-end">Paid (Cash)</th>
                </tr>
              </thead>
              <tbody>
                @forelse($shiftOrders as $order)
                  <tr>
                    <td><span class="fw-bold">{{ $order->invoice_no }}</span></td>
                    <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                    <td class="small text-muted">{{ $order->order_date->format('h:i A') }}</td>
                    <td class="text-end fw-semibold">৳{{ number_format($order->total_amount, 2) }}</td>
                    <td class="text-end fw-bold text-success">৳{{ number_format($order->paid_amount, 2) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                      No sales transactions recorded in this shift yet.
                      <div class="mt-2">
                        <a href="{{ url('/pos') }}" class="btn btn-sm btn-primary">Go to POS Terminal</a>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  @else
    <!-- ================= REGISTER IS CURRENTLY CLOSED ================= -->
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-sm border-top border-warning border-4 text-center p-4">
        <div class="card-body">

          <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
            <i class="bi bi-wallet2 fs-1"></i>
          </div>

          <h4 class="fw-bold text-dark mb-1">Shift Drawer is Currently Closed</h4>
          <p class="text-muted small mb-4">
            You must open your shift register drawer with an opening cash float to begin processing sales at the POS terminal.
          </p>

          <form action="{{ route('cash_register.open') }}" method="POST" class="text-start">
            @csrf

            <div class="mb-3">
              <label for="cash_in_hand" class="form-label fw-semibold">Opening Cash Float in Drawer (৳) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" step="0.01" min="0" name="cash_in_hand" id="cash_in_hand" class="form-control fs-5 fw-bold text-primary" placeholder="e.g. 1000.00" value="0.00" required autofocus>
              </div>
              <small class="text-muted">Petty cash/change kept in drawer at the start of shift</small>
            </div>

            <div class="mb-4">
              <label for="open_note" class="form-label fw-semibold">Shift Notes</label>
              <textarea name="note" id="open_note" class="form-control" rows="2" placeholder="e.g. Morning Shift - Counter 1"></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-semibold shadow-sm">
              <i class="bi bi-box-arrow-in-right me-1"></i> Open Shift Register Now
            </button>
          </form>

        </div>
      </div>
    </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
  function calculateDrawerDiff(expected) {
    const input = document.getElementById('total_cash_submitted');
    const preview = document.getElementById('diffPreview');
    const val = parseFloat(input.value) || 0;
    const diff = val - expected;

    if (diff === 0) {
      preview.className = 'small mt-1 fw-bold text-success';
      preview.textContent = '✓ Perfectly Balanced with Expected (৳' + expected.toFixed(2) + ')';
    } else if (diff > 0) {
      preview.className = 'small mt-1 fw-bold text-primary';
      preview.textContent = 'Surplus: +৳' + diff.toFixed(2) + ' more than expected (৳' + expected.toFixed(2) + ')';
    } else {
      preview.className = 'small mt-1 fw-bold text-danger';
      preview.textContent = 'Shortage: -৳' + Math.abs(diff).toFixed(2) + ' less than expected (৳' + expected.toFixed(2) + ')';
    }
  }
</script>
@endpush
