<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt - {{ $order->invoice_no }}</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Courier New', Courier, monospace, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f1f5f9;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 10px;
    }

    /* Screen action bar */
    .no-print-bar {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .btn {
      padding: 8px 18px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid transparent;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }

    .btn-print {
      background-color: #0d6efd;
      color: #fff;
    }
    .btn-print:hover {
      background-color: #0b5ed7;
    }

    .btn-close {
      background-color: #e2e8f0;
      color: #334155;
    }
    .btn-close:hover {
      background-color: #cbd5e1;
    }

    /* 80mm Thermal Receipt Canvas */
    .receipt-container {
      width: 80mm;
      max-width: 80mm;
      background: #ffffff;
      padding: 15px 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      font-size: 12px;
      line-height: 1.35;
      color: #000;
    }

    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .text-start { text-align: left; }
    .fw-bold { font-weight: bold; }

    .store-header {
      text-align: center;
      margin-bottom: 12px;
    }

    .store-title {
      font-size: 16px;
      font-weight: 900;
      letter-spacing: 0.5px;
      margin-bottom: 3px;
    }

    .store-sub {
      font-size: 10.5px;
      color: #222;
      line-height: 1.25;
    }

    .dashed-line {
      border-top: 1px dashed #444;
      margin: 8px 0;
    }

    .meta-table {
      width: 100%;
      font-size: 11px;
      margin-bottom: 5px;
    }

    .meta-table td {
      padding: 1.5px 0;
      vertical-align: top;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      margin: 4px 0;
    }

    .items-table th {
      border-top: 1px dashed #444;
      border-bottom: 1px dashed #444;
      padding: 4px 0;
      font-weight: bold;
    }

    .items-table td {
      padding: 3px 0;
      vertical-align: top;
    }

    .totals-table {
      width: 100%;
      font-size: 11.5px;
      margin-top: 4px;
    }

    .totals-table td {
      padding: 2px 0;
    }

    .grand-total-row {
      font-size: 14px;
      font-weight: bold;
    }

    .barcode-section {
      text-align: center;
      margin: 12px 0 6px 0;
    }

    .barcode-section svg {
      max-width: 100%;
      height: 38px;
    }

    .barcode-text {
      font-size: 10px;
      letter-spacing: 1px;
      margin-top: 2px;
    }

    .footer-note {
      text-align: center;
      font-size: 10px;
      margin-top: 10px;
      line-height: 1.3;
    }

    @media print {
      body {
        background: transparent;
        padding: 0;
        margin: 0;
      }
      .no-print-bar {
        display: none !important;
      }
      .receipt-container {
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
        padding: 5px 0 !important;
      }
      @page {
        margin: 0;
        size: auto;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Buttons (Hidden on Print) -->
  <div class="no-print-bar">
    <button class="btn btn-print" onclick="window.print()">
      🖨️ Print Receipt
    </button>
    <a href="{{ route('orders.show', $order) }}" class="btn btn-close">
      ✕ Back to Order
    </a>
  </div>

  <!-- Thermal Paper Area -->
  <div class="receipt-container">

    <div class="store-header">
      <div class="store-title">{{ \App\Models\Setting::get('company_name', 'REZA POS & SUPER STORE') }}</div>
      <div class="store-sub">{{ \App\Models\Setting::get('company_address', 'Dhanmondi 27, Dhaka - 1209, Bangladesh') }}</div>
      <div class="store-sub">Phone: {{ \App\Models\Setting::get('company_phone', '+880 1700-000000') }} | {{ \App\Models\Setting::get('company_email', 'info@rezapos.com') }}</div>
      @if(\App\Models\Setting::get('vat_number'))
        <div class="store-sub">BIN/VAT Reg: {{ \App\Models\Setting::get('vat_number') }}</div>
      @endif
    </div>

    <div class="dashed-line"></div>

    <table class="meta-table">
      <tr>
        <td class="fw-bold" style="width: 45%;">Invoice:</td>
        <td class="text-end fw-bold">{{ $order->invoice_no }}</td>
      </tr>
      <tr>
        <td>Date & Time:</td>
        <td class="text-end">{{ $order->order_date ? $order->order_date->format('d/m/Y h:i A') : $order->created_at->format('d/m/Y h:i A') }}</td>
      </tr>
      <tr>
        <td>Cashier:</td>
        <td class="text-end">{{ $order->user?->name ?? 'Staff' }}</td>
      </tr>
      <tr>
        <td>Customer:</td>
        <td class="text-end fw-bold">{{ $order->customer?->name ?? 'Walk-in Customer' }}</td>
      </tr>
      @if($order->customer?->phone)
      <tr>
        <td>Phone:</td>
        <td class="text-end">{{ $order->customer->phone }}</td>
      </tr>
      @endif
    </table>

    <table class="items-table">
      <thead>
        <tr>
          <th class="text-start" style="width: 48%;">Item</th>
          <th class="text-center" style="width: 14%;">Qty</th>
          <th class="text-end" style="width: 18%;">Price</th>
          <th class="text-end" style="width: 20%;">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
          <tr>
            <td class="text-start">
              <div>{{ $item->product_name }}</div>
            </td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-end">{{ number_format($item->unit_price, 1) }}</td>
            <td class="text-end fw-bold">{{ number_format($item->subtotal, 1) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="dashed-line"></div>

    <table class="totals-table">
      <tr>
        <td>Subtotal:</td>
        <td class="text-end">৳{{ number_format($order->subtotal, 2) }}</td>
      </tr>
      @if($order->discount_amount > 0)
        <tr>
          <td>Discount {{ $order->discount_type === 'percentage' ? '(' . $order->discount_amount . '%)' : '' }}:</td>
          <td class="text-end">-৳{{ number_format($order->discount_type === 'percentage' ? ($order->subtotal * $order->discount_amount / 100) : $order->discount_amount, 2) }}</td>
        </tr>
      @endif
      @if($order->tax_amount > 0)
        <tr>
          <td>Tax / VAT ({{ $order->tax_percentage }}%):</td>
          <td class="text-end">+৳{{ number_format($order->tax_amount, 2) }}</td>
        </tr>
      @endif
      <tr class="grand-total-row">
        <td style="padding-top: 4px;">TOTAL:</td>
        <td class="text-end" style="padding-top: 4px;">৳{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="fw-bold">Paid ({{ strtoupper(str_replace('_', ' ', $order->payment_type)) }}):</td>
        <td class="text-end fw-bold">৳{{ number_format($order->paid_amount, 2) }}</td>
      </tr>
      @if($order->change_amount > 0)
        <tr>
          <td>Change:</td>
          <td class="text-end">৳{{ number_format($order->change_amount, 2) }}</td>
        </tr>
      @endif
      @if($order->due_amount > 0)
        <tr class="fw-bold">
          <td style="color: #000;">DUE BALANCE:</td>
          <td class="text-end">৳{{ number_format($order->due_amount, 2) }}</td>
        </tr>
      @endif
    </table>

    <div class="dashed-line"></div>

    <div class="barcode-section">
      {!! $barcodeSvg !!}
      <div class="barcode-text">{{ $order->invoice_no }}</div>
    </div>

    <div class="footer-note">
      <p class="fw-bold">{{ \App\Models\Setting::get('receipt_footer', 'Thank you for your business! Please come again.') }}</p>
      <p style="margin-top: 4px; font-size: 9px; color: #555;">Powered by {{ \App\Models\Setting::get('company_name', 'Reza Laravel POS') }}</p>
    </div>

  </div>

  <script>
    // Auto-trigger print dialog if requested
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('print') === '1') {
      window.onload = function() {
        window.print();
      };
    }
  </script>
</body>
</html>
