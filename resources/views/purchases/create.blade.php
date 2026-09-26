@extends('layouts.admin')

@section('title', 'New Stock Purchase')
@section('page-title', 'New Stock Purchase')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/purchases') }}">Procurement</a></li>
  <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
  <li class="breadcrumb-item active">New Purchase</li>
@endsection

@section('content')
<form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
  @csrf

  <div class="row">

    <!-- Left Column: Details & Items (8 cols) -->
    <div class="col-lg-8">
      <div class="card shadow-sm mb-3">
        <div class="card-body pt-3">
          <h5 class="card-title pb-2 border-bottom">Purchase Info</h5>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="supplier_id" class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
              <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                <option value="">Select Supplier</option>
                @foreach($suppliers as $supplier)
                  <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->name }} ({{ $supplier->company_name ?? 'Individual' }})
                  </option>
                @endforeach
              </select>
              @error('supplier_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="purchase_date" class="form-label fw-semibold">Purchase Date <span class="text-danger">*</span></label>
              <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
              @error('purchase_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      <!-- Purchase Items Table -->
      <div class="card shadow-sm mb-3">
        <div class="card-body pt-3">
          <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
            <h5 class="card-title p-0 m-0">Purchase Products</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
              <i class="bi bi-plus-lg me-1"></i> Add Item Row
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="itemsTable">
              <thead class="table-light">
                <tr>
                  <th style="min-width: 260px;">Product <span class="text-danger">*</span></th>
                  <th style="width: 140px;">Unit Cost (৳) <span class="text-danger">*</span></th>
                  <th style="width: 120px;">Qty <span class="text-danger">*</span></th>
                  <th style="width: 140px;" class="text-end">Subtotal (৳)</th>
                  <th style="width: 50px;" class="text-center"></th>
                </tr>
              </thead>
              <tbody id="itemsBody">
                <tr class="item-row">
                  <td>
                    <select name="items[0][product_id]" class="form-select product-select" onchange="onProductChange(this)" required>
                      <option value="">Select Product</option>
                      @foreach($products as $product)
                        <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}" data-unit="{{ $product->unit->short_name ?? 'pcs' }}">
                          {{ $product->name }} (Code: {{ $product->code }})
                        </option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <input type="number" step="0.01" min="0" name="items[0][cost_price]" class="form-control cost-input" placeholder="0.00" oninput="calculateSubtotal(this)" required>
                  </td>
                  <td>
                    <input type="number" step="0.01" min="0.01" name="items[0][quantity]" class="form-control qty-input" value="1" oninput="calculateSubtotal(this)" required>
                  </td>
                  <td class="text-end fw-bold">
                    <span class="row-subtotal">৳0.00</span>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label fw-semibold">Purchase Notes / Remarks</label>
            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="e.g. Invoice #1234 from supplier warehouse...">{{ old('notes') }}</textarea>
          </div>

        </div>
      </div>
    </div>

    <!-- Right Column: Summary & Payment (4 cols) -->
    <div class="col-lg-4">
      <div class="card shadow-sm sticky-top" style="top: 80px;">
        <div class="card-body pt-3">
          <h5 class="card-title pb-2 border-bottom">Payment Summary</h5>

          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <span class="text-muted">Total Products:</span>
            <span class="fw-bold fs-6" id="totalItemsCount">1</span>
          </div>

          <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
            <span class="fs-5 fw-bold text-dark">Grand Total:</span>
            <span class="fs-4 fw-bold text-primary" id="grandTotalDisplay">৳0.00</span>
          </div>

          <div class="mt-3">
            <label for="paid_amount" class="form-label fw-semibold">Paid Amount (৳) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">৳</span>
              <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="form-control fs-5 fw-bold text-success" value="{{ old('paid_amount', '0.00') }}" oninput="calculateDue()" required>
            </div>
            <div class="d-flex gap-1 mt-2">
              <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small" onclick="setPaidFull()">Paid in Full</button>
              <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small" onclick="setPaidZero()">Due (৳0)</button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center py-3 mt-3 border-top border-bottom">
            <span class="fw-semibold text-danger">Balance Due:</span>
            <span class="fw-bold fs-5 text-danger" id="dueAmountDisplay">৳0.00</span>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-semibold shadow-sm">
              <i class="bi bi-check-circle me-1"></i> Confirm & Save Purchase
            </button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary w-100 mt-2">
              Cancel
            </a>
          </div>

        </div>
      </div>
    </div>

  </div>
</form>
@endsection

@push('scripts')
<script>
  let rowCount = 1;

  const productsData = @json($products);

  function getProductOptions() {
    let html = '<option value="">Select Product</option>';
    productsData.forEach(p => {
      html += `<option value="${p.id}" data-cost="${p.cost_price}" data-unit="${p.unit ? p.unit.short_name : 'pcs'}">${p.name} (Code: ${p.code})</option>`;
    });
    return html;
  }

  function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
      <td>
        <select name="items[${rowCount}][product_id]" class="form-select product-select" onchange="onProductChange(this)" required>
          ${getProductOptions()}
        </select>
      </td>
      <td>
        <input type="number" step="0.01" min="0" name="items[${rowCount}][cost_price]" class="form-control cost-input" placeholder="0.00" oninput="calculateSubtotal(this)" required>
      </td>
      <td>
        <input type="number" step="0.01" min="0.01" name="items[${rowCount}][quantity]" class="form-control qty-input" value="1" oninput="calculateSubtotal(this)" required>
      </td>
      <td class="text-end fw-bold">
        <span class="row-subtotal">৳0.00</span>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)" title="Remove">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    rowCount++;
    updateTotals();
  }

  function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
      btn.closest('tr').remove();
      updateTotals();
    } else {
      alert('At least one item is required in a purchase order!');
    }
  }

  function onProductChange(select) {
    const option = select.options[select.selectedIndex];
    const cost = option.getAttribute('data-cost') || 0;
    const row = select.closest('tr');
    const costInput = row.querySelector('.cost-input');
    costInput.value = parseFloat(cost).toFixed(2);
    calculateSubtotal(costInput);
  }

  function calculateSubtotal(input) {
    const row = input.closest('tr');
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const subtotal = cost * qty;
    row.querySelector('.row-subtotal').textContent = '৳' + subtotal.toFixed(2);
    updateTotals();
  }

  function updateTotals() {
    let grandTotal = 0;
    const rows = document.querySelectorAll('.item-row');
    rows.forEach(r => {
      const cost = parseFloat(r.querySelector('.cost-input').value) || 0;
      const qty = parseFloat(r.querySelector('.qty-input').value) || 0;
      grandTotal += (cost * qty);
    });

    document.getElementById('totalItemsCount').textContent = rows.length;
    document.getElementById('grandTotalDisplay').textContent = '৳' + grandTotal.toFixed(2);
    calculateDue();
  }

  function calculateDue() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(r => {
      const cost = parseFloat(r.querySelector('.cost-input').value) || 0;
      const qty = parseFloat(r.querySelector('.qty-input').value) || 0;
      grandTotal += (cost * qty);
    });

    const paidInput = document.getElementById('paid_amount');
    const paid = parseFloat(paidInput.value) || 0;
    const due = Math.max(0, grandTotal - paid);

    document.getElementById('dueAmountDisplay').textContent = '৳' + due.toFixed(2);
  }

  function setPaidFull() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(r => {
      const cost = parseFloat(r.querySelector('.cost-input').value) || 0;
      const qty = parseFloat(r.querySelector('.qty-input').value) || 0;
      grandTotal += (cost * qty);
    });
    document.getElementById('paid_amount').value = grandTotal.toFixed(2);
    calculateDue();
  }

  function setPaidZero() {
    document.getElementById('paid_amount').value = '0.00';
    calculateDue();
  }
</script>
@endpush
