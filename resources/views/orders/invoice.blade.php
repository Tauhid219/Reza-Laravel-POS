<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tax Invoice - {{ $order->invoice_no }}</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #334155;
      padding: 30px 10px;
    }

    .invoice-card {
      max-width: 850px;
      margin: 0 auto;
      background: #ffffff;
      padding: 40px 50px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
      border-radius: 8px;
    }

    .company-logo {
      font-size: 26px;
      font-weight: 800;
      color: #012970;
      letter-spacing: -0.5px;
    }

    .invoice-title {
      font-size: 32px;
      font-weight: 800;
      color: #4154f1;
      letter-spacing: 1px;
    }

    .table th {
      background-color: #f1f5f9;
      color: #1e293b;
      font-weight: 600;
      font-size: 13px;
      border-bottom: 2px solid #cbd5e1;
    }

    .table td {
      font-size: 13.5px;
      vertical-align: middle;
    }

    .totals-box {
      background-color: #f8fafc;
      border-radius: 6px;
      padding: 15px;
      border: 1px solid #e2e8f0;
    }

    .badge-paid {
      background-color: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }
    .badge-partial {
      background-color: #fef9c3;
      color: #854d0e;
      border: 1px solid #fef08a;
    }
    .badge-due {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .signature-line {
      border-top: 1px solid #94a3b8;
      width: 180px;
      margin-top: 50px;
      padding-top: 5px;
      font-size: 12px;
      color: #64748b;
      text-align: center;
    }

    @media print {
      body {
        background: transparent;
        padding: 0;
      }
      .no-print {
        display: none !important;
      }
      .invoice-card {
        box-shadow: none;
        padding: 0;
        max-width: 100%;
        border-radius: 0;
      }
      @page {
        size: A4 portrait;
        margin: 12mm 15mm;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Actions Bar -->
  <div class="max-w-850 mx-auto mb-4 text-center no-print" style="max-width: 850px;">
    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm">
      <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Order
      </a>
      <div class="d-flex gap-2">
        <a href="{{ route('orders.receipt', $order) }}" class="btn btn-outline-info btn-sm">
          <i class="bi bi-receipt me-1"></i> Thermal Receipt
        </a>
        <button class="btn btn-primary btn-sm px-3" onclick="window.print()">
          <i class="bi bi-printer me-1"></i> Print Invoice (A4)
        </button>
      </div>
    </div>
  </div>

  <!-- A4 Invoice Sheet -->
  <div class="invoice-card">

    <!-- Header Section -->
    <div class="row align-items-center border-bottom pb-4 mb-4">
      <div class="col-sm-7">
        <div class="company-logo d-flex align-items-center gap-2">
          <i class="bi bi-cart4 text-primary"></i> REZA POS & SUPER STORE
        </div>
        <p class="text-muted small mb-1 mt-2">
          House #42, Road #27, Dhanmondi, Dhaka - 1209, Bangladesh<br>
          <strong>Hotline:</strong> +880 1700-000000 | <strong>Email:</strong> support@rezapos.com<br>
          <strong>BIN / VAT Reg No:</strong> 001234567-0101
        </p>
      </div>
      <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
        <div class="invoice-title">INVOICE</div>
        <div class="fw-bold fs-5 text-dark">{{ $order->invoice_no }}</div>
        <div class="mt-2">
          {!! $barcodeSvg !!}
        </div>
        <div class="small text-muted mt-1">{{ $order->invoice_no }}</div>
      </div>
    </div>

    <!-- Meta Details / Bill To -->
    <div class="row mb-4">
      <div class="col-sm-6">
        <h6 class="text-uppercase text-muted fw-bold small mb-2">Billed To:</h6>
        <h5 class="fw-bold text-dark mb-1">{{ $order->customer?->name ?? 'Walk-in Customer' }}</h5>
        <div class="text-muted small">
          @if($order->customer?->phone)
            <div><i class="bi bi-telephone me-1"></i> {{ $order->customer->phone }}</div>
          @endif
          @if($order->customer?->email)
            <div><i class="bi bi-envelope me-1"></i> {{ $order->customer->email }}</div>
          @endif
          @if($order->customer?->address)
            <div><i class="bi bi-geo-alt me-1"></i> {{ $order->customer->address }}</div>
          @endif
        </div>
      </div>
      <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
        <h6 class="text-uppercase text-muted fw-bold small mb-2">Invoice Information:</h6>
        <div class="small">
          <div><strong class="text-secondary">Invoice Date:</strong> {{ $order->order_date ? $order->order_date->format('d M, Y h:i A') : $order->created_at->format('d M, Y h:i A') }}</div>
          <div><strong class="text-secondary">Billed By:</strong> {{ $order->user?->name ?? 'Cashier' }}</div>
          <div><strong class="text-secondary">Payment Method:</strong> <span class="text-uppercase">{{ str_replace('_', ' ', $order->payment_type) }}</span></div>
          <div class="mt-2">
            <strong class="text-secondary me-2">Status:</strong>
            @if($order->payment_status === 'paid')
              <span class="badge badge-paid px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> FULLY PAID</span>
            @elseif($order->payment_status === 'partial')
              <span class="badge badge-partial px-2 py-1"><i class="bi bi-clock-fill me-1"></i> PARTIALLY PAID</span>
            @else
              <span class="badge badge-due px-2 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> DUE / UNPAID</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th class="text-center" style="width: 5%;">#</th>
            <th style="width: 45%;">Item Description</th>
            <th class="text-end" style="width: 15%;">Unit Price</th>
            <th class="text-center" style="width: 10%;">Qty</th>
            <th class="text-end" style="width: 10%;">Discount</th>
            <th class="text-end" style="width: 15%;">Total (BDT)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $index => $item)
            <tr>
              <td class="text-center text-muted">{{ $index + 1 }}</td>
              <td>
                <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                @if($item->product?->code)
                  <span class="text-muted small">Code / Barcode: {{ $item->product->code }}</span>
                @endif
              </td>
              <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
              <td class="text-center fw-semibold">{{ $item->quantity }}</td>
              <td class="text-end text-muted">
                {{ $item->discount > 0 ? '৳' . number_format($item->discount, 2) : '-' }}
              </td>
              <td class="text-end fw-bold text-dark">৳{{ number_format($item->subtotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Totals & Payment Summary -->
    <div class="row align-items-start mb-4">
      <div class="col-sm-7">
        <div class="p-3 border rounded bg-light mb-3">
          <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-info-circle me-1"></i> Terms & Notes</h6>
          <p class="small text-muted mb-0">
            {{ $order->note ? $order->note : '1. Thank you for your business. 2. Goods once sold can be replaced within 7 days in undamaged original packaging. 3. Computer-generated invoice; valid without physical stamp.' }}
          </p>
        </div>

        @if($order->payments->count() > 0)
          <div class="small">
            <span class="fw-bold text-secondary">Payments Log:</span>
            <ul class="list-unstyled mt-1">
              @foreach($order->payments as $p)
                <li class="text-muted">
                  <i class="bi bi-check2 text-success me-1"></i>
                  ৳{{ number_format($p->amount, 2) }} via <strong class="text-uppercase">{{ str_replace('_', ' ', $p->payment_method) }}</strong>
                  on {{ $p->payment_date ? $p->payment_date->format('d/m/Y h:i A') : $p->created_at->format('d/m/Y h:i A') }}
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>

      <div class="col-sm-5">
        <div class="totals-box">
          <div class="d-flex justify-content-between mb-2 small">
            <span class="text-muted">Subtotal:</span>
            <span class="fw-semibold">৳{{ number_format($order->subtotal, 2) }}</span>
          </div>
          @if($order->discount_amount > 0)
            <div class="d-flex justify-content-between mb-2 small">
              <span class="text-muted">Discount {{ $order->discount_type === 'percentage' ? '(' . $order->discount_amount . '%)' : '' }}:</span>
              <span class="text-danger fw-semibold">-৳{{ number_format($order->discount_type === 'percentage' ? ($order->subtotal * $order->discount_amount / 100) : $order->discount_amount, 2) }}</span>
            </div>
          @endif
          @if($order->tax_amount > 0)
            <div class="d-flex justify-content-between mb-2 small">
              <span class="text-muted">VAT / Tax ({{ $order->tax_percentage }}%):</span>
              <span class="fw-semibold">+৳{{ number_format($order->tax_amount, 2) }}</span>
            </div>
          @endif
          <div class="d-flex justify-content-between py-2 border-top border-bottom my-2">
            <span class="fw-bold fs-6 text-dark">Grand Total:</span>
            <span class="fw-bold fs-6 text-primary">৳{{ number_format($order->total_amount, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2 small">
            <span class="text-muted">Paid Amount:</span>
            <span class="fw-bold text-success">৳{{ number_format($order->paid_amount, 2) }}</span>
          </div>
          @if($order->change_amount > 0)
            <div class="d-flex justify-content-between mb-2 small">
              <span class="text-muted">Change Returned:</span>
              <span class="fw-semibold">৳{{ number_format($order->change_amount, 2) }}</span>
            </div>
          @endif
          @if($order->due_amount > 0)
            <div class="d-flex justify-content-between p-2 rounded bg-danger-subtle text-danger fw-bold small">
              <span>Outstanding Due:</span>
              <span>৳{{ number_format($order->due_amount, 2) }}</span>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Signatures -->
    <div class="row pt-4 mt-5">
      <div class="col-6">
        <div class="signature-line">
          Customer's Signature
        </div>
      </div>
      <div class="col-6 d-flex justify-content-end">
        <div class="signature-line">
          Authorized Signature
        </div>
      </div>
    </div>

  </div>

</body>
</html>
