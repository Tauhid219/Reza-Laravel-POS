@extends('layouts.admin')

@section('title', 'POS Terminal')
@section('page-title', 'Point of Sale (POS)')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
  <li class="breadcrumb-item active">POS Terminal</li>
@endsection

@section('page-actions')
  <div class="d-flex align-items-center gap-2">
    @if($activeRegister)
      <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
        <i class="bi bi-unlock-fill me-1"></i> Drawer: Open (৳{{ number_format($activeRegister->cash_in_hand + $activeRegister->cash_sales, 2) }})
      </span>
    @else
      <a href="{{ route('cash_register.index') }}" class="btn btn-sm btn-warning text-dark fw-bold">
        <i class="bi bi-exclamation-triangle me-1"></i> Open Register Shift First
      </a>
    @endif
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleHoldListModal()">
      <i class="bi bi-pause-circle me-1"></i> Held Sales (<span id="heldCount">0</span>)
    </button>
  </div>
@endsection

@push('styles')
<style>
  .pos-card-product {
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    border: 1px solid #e2e8f0;
    user-select: none;
  }
  .pos-card-product:hover {
    transform: translateY(-2px);
    border-color: #4154f1;
    box-shadow: 0 4px 12px rgba(65, 84, 241, 0.15);
  }
  .pos-card-product:active {
    transform: scale(0.98);
  }
  .category-pill {
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
  }
  .category-pill.active {
    background-color: #4154f1 !important;
    color: #fff !important;
    border-color: #4154f1 !important;
  }
  .cart-container {
    height: calc(100vh - 280px);
    min-height: 480px;
    display: flex;
    flex-direction: column;
  }
  .cart-items-list {
    flex: 1 1 auto;
    overflow-y: auto;
  }
  .product-grid-container {
    max-height: calc(100vh - 280px);
    overflow-y: auto;
  }
  .quick-cash-btn {
    font-size: 13px;
    font-weight: 600;
  }
</style>
@endpush

@section('content')
<div class="row g-3">

  <!-- ================= LEFT COLUMN: PRODUCTS & BARCODE SCANNER (7 COLS) ================= -->
  <div class="col-lg-7">
    <div class="card shadow-sm mb-3">
      <div class="card-body pt-3">

        <!-- Barcode & Search Controls -->
        <div class="row g-2 mb-3">
          <div class="col-md-7">
            <div class="input-group">
              <span class="input-group-text bg-primary text-white"><i class="bi bi-upc-scan"></i></span>
              <input type="text" id="barcodeScannerInput" class="form-control form-control-lg fs-6" placeholder="Scan Barcode or type code and press Enter..." autofocus autocomplete="off">
            </div>
          </div>
          <div class="col-md-5">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
              <input type="text" id="productSearchInput" class="form-control" placeholder="Search product name..." oninput="filterProducts()">
            </div>
          </div>
        </div>

        <!-- Category Pills Bar -->
        <div class="d-flex gap-2 overflow-auto pb-2 mb-3 border-bottom" id="categoryPills">
          <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 category-pill active" onclick="filterByCategory('', this)">
            All Categories
          </button>
          @foreach($categories as $category)
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 category-pill" onclick="filterByCategory('{{ $category->id }}', this)">
              {{ $category->name }}
            </button>
          @endforeach
        </div>

        <!-- Product Cards Grid -->
        <div class="product-grid-container pe-1">
          <div class="row g-2" id="productsGrid">
            @forelse($products as $product)
              <div class="col-6 col-sm-4 col-md-3 col-xl-3 product-item" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}" data-code="{{ strtolower($product->code) }}">
                <div class="card h-100 mb-0 pos-card-product p-2" onclick="addToCart({{ $product->id }})">
                  <div class="position-relative text-center">
                    @if($product->image)
                      <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="rounded mb-2" style="width: 100%; height: 75px; object-fit: cover;">
                    @else
                      <div class="rounded mb-2 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 100%; height: 75px;">
                        <i class="bi bi-box-seam fs-2"></i>
                      </div>
                    @endif
                    <span class="position-absolute top-0 end-0 badge {{ $product->stock_quantity <= $product->alert_quantity ? 'bg-danger' : 'bg-secondary' }}" style="font-size: 10px;">
                      {{ (int) $product->stock_quantity }}
                    </span>
                  </div>

                  <div class="fw-semibold text-dark text-truncate small" title="{{ $product->name }}">
                    {{ $product->name }}
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="fw-bold text-primary small">৳{{ number_format($product->selling_price, 2) }}</span>
                    <small class="text-muted" style="font-size: 11px;">{{ $product->unit->short_name ?? 'pc' }}</small>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No active products found in inventory.
              </div>
            @endforelse
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ================= RIGHT COLUMN: CART & CHECKOUT PANEL (5 COLS) ================= -->
  <div class="col-lg-5">
    <div class="card shadow-sm cart-container mb-3">
      <div class="card-body p-3 d-flex flex-column h-100">

        <!-- Customer Selection Bar -->
        <div class="d-flex gap-2 align-items-center pb-2 mb-2 border-bottom">
          <div class="flex-grow-1">
            <select id="customerSelect" class="form-select form-select-sm">
              @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ $c->id == $defaultCustomer?->id ? 'selected' : '' }}>
                  {{ $c->name }} ({{ $c->phone ?? 'Walk-in' }}) {{ $c->total_due > 0 ? '- Due: ৳' . number_format($c->total_due, 2) : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickCustomerModal" title="Quick Add Customer">
            <i class="bi bi-person-plus"></i>
          </button>
        </div>

        <!-- Cart Items List Header -->
        <div class="d-flex justify-content-between align-items-center text-muted small fw-bold px-1 pb-1 border-bottom">
          <span style="width: 45%;">Item</span>
          <span style="width: 25%;" class="text-center">Qty</span>
          <span style="width: 20%;" class="text-end">Total</span>
          <span style="width: 10%;" class="text-end"></span>
        </div>

        <!-- Cart Items Scrollable List -->
        <div class="cart-items-list py-1" id="cartItemsList">
          <!-- Dynamically populated via JS -->
          <div class="text-center py-5 text-muted" id="emptyCartMessage">
            <i class="bi bi-cart3 fs-1 text-secondary d-block mb-2"></i>
            <span>Cart is empty. Click on items or scan barcode to add.</span>
          </div>
        </div>

        <!-- Cart Bottom Summary -->
        <div class="pt-2 border-top mt-auto">
          <div class="d-flex justify-content-between py-1 small">
            <span class="text-muted">Subtotal:</span>
            <span class="fw-semibold" id="cartSubtotalDisplay">৳0.00</span>
          </div>

          <div class="row g-2 py-1 align-items-center small">
            <div class="col-6 d-flex align-items-center gap-1">
              <span class="text-muted">Discount:</span>
              <input type="number" id="cartDiscountInput" class="form-control form-control-sm text-end" style="width: 75px;" value="0" min="0" oninput="recalculateCart()">
              <select id="discountTypeSelect" class="form-select form-select-sm" style="width: 55px;" onchange="recalculateCart()">
                <option value="fixed">৳</option>
                <option value="percentage">%</option>
              </select>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end gap-1">
              <span class="text-muted">VAT/Tax:</span>
              <input type="number" id="cartTaxInput" class="form-control form-control-sm text-end" style="width: 60px;" value="0" min="0" oninput="recalculateCart()">
              <span>%</span>
            </div>
          </div>

          <!-- Total Payable Banner -->
          <div class="d-flex justify-content-between align-items-center p-2 rounded bg-primary-subtle text-primary my-2">
            <span class="fw-bold fs-6">Payable Total:</span>
            <span class="fw-bold fs-4" id="cartTotalDisplay">৳0.00</span>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCart()" title="Empty Cart">
              <i class="bi bi-trash"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="holdCurrentSale()" title="Hold this sale">
              <i class="bi bi-pause"></i> Hold
            </button>
            <button type="button" class="btn btn-primary flex-grow-1 fw-bold fs-6 shadow-sm" onclick="openPaymentModal()">
              <i class="bi bi-credit-card me-1"></i> Pay Now (৳<span id="payBtnAmount">0.00</span>)
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>

<!-- ================= QUICK ADD CUSTOMER MODAL ================= -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form id="quickCustomerForm" onsubmit="submitQuickCustomer(event)">
        <div class="modal-header py-2">
          <h6 class="modal-title fw-bold">Add Quick Customer</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-2">
          <div class="mb-2">
            <label class="form-label small fw-semibold">Customer Name <span class="text-danger">*</span></label>
            <input type="text" id="quickCustomerName" class="form-control form-control-sm" required>
          </div>
          <div class="mb-2">
            <label class="form-label small fw-semibold">Phone Number</label>
            <input type="text" id="quickCustomerPhone" class="form-control form-control-sm" placeholder="017xxxxxxxx">
          </div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary">Save & Select</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ================= PAYMENT CHECKOUT MODAL ================= -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white py-2">
        <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-credit-card-2-front me-1"></i> Checkout & Payment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Amount Banner -->
        <div class="p-3 bg-light rounded border text-center mb-3">
          <span class="text-muted small d-block">TOTAL PAYABLE AMOUNT</span>
          <span class="fs-2 fw-bold text-primary" id="modalPayableTotalDisplay">৳0.00</span>
        </div>

        <!-- Payment Method Tabs -->
        <div class="mb-3">
          <label class="form-label fw-semibold small">Payment Method</label>
          <div class="btn-group w-100" role="group">
            <input type="radio" class="btn-check" name="payment_type_radio" id="pay_cash" value="cash" checked onchange="onPaymentTypeChange('cash')">
            <label class="btn btn-outline-primary btn-sm" for="pay_cash"><i class="bi bi-cash me-1"></i>Cash</label>

            <input type="radio" class="btn-check" name="payment_type_radio" id="pay_card" value="card" onchange="onPaymentTypeChange('card')">
            <label class="btn btn-outline-primary btn-sm" for="pay_card"><i class="bi bi-credit-card me-1"></i>Card</label>

            <input type="radio" class="btn-check" name="payment_type_radio" id="pay_mobile" value="mobile_banking" onchange="onPaymentTypeChange('mobile_banking')">
            <label class="btn btn-outline-primary btn-sm" for="pay_mobile"><i class="bi bi-phone me-1"></i>bKash/Nagad</label>

            <input type="radio" class="btn-check" name="payment_type_radio" id="pay_credit" value="credit" onchange="onPaymentTypeChange('credit')">
            <label class="btn btn-outline-danger btn-sm" for="pay_credit"><i class="bi bi-clock-history me-1"></i>Credit (Due)</label>
          </div>
        </div>

        <!-- Amount Tendered Input -->
        <div class="mb-3">
          <label for="modalReceivedAmountInput" class="form-label fw-semibold small">Amount Paid by Customer (৳)</label>
          <div class="input-group">
            <span class="input-group-text">৳</span>
            <input type="number" step="0.01" min="0" id="modalReceivedAmountInput" class="form-control form-control-lg fs-4 fw-bold text-success" oninput="calculateChangeAndDue()">
          </div>
          <!-- Quick Cash Buttons -->
          <div class="d-flex flex-wrap gap-1 mt-2">
            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="setExactCash()">Exact</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="addCash(50)">+50</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="addCash(100)">+100</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="addCash(500)">+500</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="addCash(1000)">+1000</button>
          </div>
        </div>

        <!-- Change & Due Status Cards -->
        <div class="row g-2 mb-3">
          <div class="col-6">
            <div class="p-2 rounded bg-success-subtle border border-success-subtle text-center">
              <span class="small text-muted d-block">Change Return:</span>
              <span class="fs-5 fw-bold text-success" id="modalChangeDisplay">৳0.00</span>
            </div>
          </div>
          <div class="col-6">
            <div class="p-2 rounded bg-danger-subtle border border-danger-subtle text-center">
              <span class="small text-muted d-block">Customer Due:</span>
              <span class="fs-5 fw-bold text-danger" id="modalDueDisplay">৳0.00</span>
            </div>
          </div>
        </div>

        <!-- Note -->
        <div class="mb-2">
          <label for="modalOrderNote" class="form-label small text-muted">Order Note / Transaction ID</label>
          <input type="text" id="modalOrderNote" class="form-control form-control-sm" placeholder="Optional reference notes...">
        </div>

      </div>

      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success fw-bold px-4" id="confirmSaleBtn" onclick="submitSaleCheckout()">
          <i class="bi bi-check-circle me-1"></i> Complete Sale (Print Receipt)
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ================= HELD SALES MODAL ================= -->
<div class="modal fade" id="heldSalesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title fw-bold">Held / Parked Sales</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="list-group list-group-flush" id="heldSalesList">
          <!-- Populated via JS -->
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ================= SUCCESS RECEIPT POPUP MODAL ================= -->
<div class="modal fade" id="successReceiptModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-body">
        <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
        <h5 class="fw-bold mb-1">Sale Completed!</h5>
        <p class="small text-muted mb-3" id="successInvoiceNo">Invoice: INV-XXXX</p>

        <div class="d-grid gap-2">
          <button type="button" class="btn btn-primary" onclick="printReceiptPopup()">
            <i class="bi bi-printer me-1"></i> Print POS Receipt
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printInvoicePopup()">
            <i class="bi bi-file-earmark-pdf me-1"></i> View A4 Invoice
          </button>
          <button type="button" class="btn btn-light btn-sm mt-1" data-bs-dismiss="modal" onclick="resetPosForNextCustomer()">
            Next Customer (New Sale)
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // All Products In-Memory Cache
  const allProducts = @json($products);
  let cart = [];
  let heldSales = JSON.parse(localStorage.getItem('reza_pos_held_sales') || '[]');
  let lastCompletedSale = null;

  // Initialize
  document.addEventListener('DOMContentLoaded', function() {
    updateHeldCount();
    document.getElementById('barcodeScannerInput').focus();

    // Barcode scanner Enter key listener
    document.getElementById('barcodeScannerInput').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const code = this.value.trim().toLowerCase();
        if (code) {
          scanBarcode(code);
          this.value = '';
        }
      }
    });
  });

  // Filter Products by Category Pill
  function filterByCategory(categoryId, btn) {
    document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');

    const search = document.getElementById('productSearchInput').value.trim().toLowerCase();

    document.querySelectorAll('.product-item').forEach(item => {
      const itemCat = item.getAttribute('data-category');
      const itemName = item.getAttribute('data-name');
      const itemCode = item.getAttribute('data-code');

      const matchesCat = !categoryId || itemCat === categoryId;
      const matchesSearch = !search || itemName.includes(search) || itemCode.includes(search);

      if (matchesCat && matchesSearch) {
        item.style.display = '';
      } else {
        item.style.display = 'none';
      }
    });
  }

  // Filter Products by Search Input
  function filterProducts() {
    const search = document.getElementById('productSearchInput').value.trim().toLowerCase();
    const activeCatBtn = document.querySelector('.category-pill.active');
    const categoryId = activeCatBtn ? activeCatBtn.getAttribute('onclick').match(/'([^']*)'/)[1] : '';

    document.querySelectorAll('.product-item').forEach(item => {
      const itemCat = item.getAttribute('data-category');
      const itemName = item.getAttribute('data-name');
      const itemCode = item.getAttribute('data-code');

      const matchesCat = !categoryId || itemCat === categoryId;
      const matchesSearch = !search || itemName.includes(search) || itemCode.includes(search);

      if (matchesCat && matchesSearch) {
        item.style.display = '';
      } else {
        item.style.display = 'none';
      }
    });
  }

  // Scan Barcode
  function scanBarcode(code) {
    const product = allProducts.find(p => p.code.toLowerCase() === code);
    if (product) {
      addToCart(product.id);
    } else {
      // Sound or toast alert
      alert('Product with barcode "' + code + '" not found in active inventory.');
    }
  }

  // Add Product to Cart
  function addToCart(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (!product) return;

    if (product.stock_quantity <= 0) {
      alert('Cannot add "' + product.name + '" because it is out of stock!');
      return;
    }

    const existingIndex = cart.findIndex(item => item.product_id === productId);

    if (existingIndex > -1) {
      if (cart[existingIndex].quantity + 1 > product.stock_quantity) {
        alert('Stock limit reached for ' + product.name + '! Available stock: ' + product.stock_quantity);
        return;
      }
      cart[existingIndex].quantity += 1;
    } else {
      cart.push({
        product_id: product.id,
        name: product.name,
        code: product.code,
        price: parseFloat(product.selling_price),
        cost_price: parseFloat(product.cost_price),
        stock: parseFloat(product.stock_quantity),
        unit: product.unit ? product.unit.short_name : 'pc',
        quantity: 1,
        discount: 0
      });
    }

    renderCart();
  }

  // Update Item Quantity
  function updateCartQty(index, delta) {
    const item = cart[index];
    const newQty = item.quantity + delta;

    if (newQty <= 0) {
      removeCartItem(index);
      return;
    }

    if (newQty > item.stock) {
      alert('Stock limit reached! Available stock: ' + item.stock);
      return;
    }

    item.quantity = newQty;
    renderCart();
  }

  // Remove Item from Cart
  function removeCartItem(index) {
    cart.splice(index, 1);
    renderCart();
  }

  // Clear Entire Cart
  function clearCart() {
    if (cart.length === 0) return;
    if (confirm('Are you sure you want to clear the entire cart?')) {
      cart = [];
      renderCart();
    }
  }

  // Render Cart DOM
  function renderCart() {
    const container = document.getElementById('cartItemsList');

    if (cart.length === 0) {
      container.innerHTML = `
        <div class="text-center py-5 text-muted" id="emptyCartMessage">
          <i class="bi bi-cart3 fs-1 text-secondary d-block mb-2"></i>
          <span>Cart is empty. Click on items or scan barcode to add.</span>
        </div>
      `;
    } else {
      let html = '';
      cart.forEach((item, index) => {
        const itemTotal = (item.price * item.quantity) - item.discount;
        html += `
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <div style="width: 45%;">
              <div class="fw-semibold text-dark text-truncate small">${item.name}</div>
              <small class="text-muted">৳${item.price.toFixed(2)} / ${item.unit}</small>
            </div>
            <div style="width: 25%;" class="d-flex align-items-center justify-content-center gap-1">
              <button type="button" class="btn btn-xs btn-outline-secondary p-0 px-1" onclick="updateCartQty(${index}, -1)">-</button>
              <span class="fw-bold small px-1">${item.quantity}</span>
              <button type="button" class="btn btn-xs btn-outline-secondary p-0 px-1" onclick="updateCartQty(${index}, 1)">+</button>
            </div>
            <div style="width: 20%;" class="text-end fw-bold small text-dark">
              ৳${itemTotal.toFixed(2)}
            </div>
            <div style="width: 10%;" class="text-end">
              <button type="button" class="btn btn-link text-danger p-0" onclick="removeCartItem(${index})">
                <i class="bi bi-x-circle fs-6"></i>
              </button>
            </div>
          </div>
        `;
      });
      container.innerHTML = html;
    }

    recalculateCart();
  }

  // Recalculate Subtotal, Discount, Tax, Total
  function recalculateCart() {
    let subtotal = 0;
    cart.forEach(item => {
      subtotal += (item.price * item.quantity) - item.discount;
    });

    const discountVal = parseFloat(document.getElementById('cartDiscountInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    let discountAmount = 0;

    if (discountType === 'percentage') {
      discountAmount = (subtotal * discountVal) / 100;
    } else {
      discountAmount = discountVal;
    }

    const afterDiscount = Math.max(0, subtotal - discountAmount);
    const taxPercentage = parseFloat(document.getElementById('cartTaxInput').value) || 0;
    const taxAmount = (afterDiscount * taxPercentage) / 100;
    const grandTotal = afterDiscount + taxAmount;

    document.getElementById('cartSubtotalDisplay').textContent = '৳' + subtotal.toFixed(2);
    document.getElementById('cartTotalDisplay').textContent = '৳' + grandTotal.toFixed(2);
    document.getElementById('payBtnAmount').textContent = grandTotal.toFixed(2);
  }

  // Open Checkout Modal
  function openPaymentModal() {
    if (cart.length === 0) {
      alert('Cart is empty! Add products before checking out.');
      return;
    }

    let subtotal = 0;
    cart.forEach(item => subtotal += (item.price * item.quantity) - item.discount);
    const discountVal = parseFloat(document.getElementById('cartDiscountInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountAmount = discountType === 'percentage' ? (subtotal * discountVal) / 100 : discountVal;
    const taxPercentage = parseFloat(document.getElementById('cartTaxInput').value) || 0;
    const taxAmount = (Math.max(0, subtotal - discountAmount) * taxPercentage) / 100;
    const grandTotal = Math.max(0, subtotal - discountAmount) + taxAmount;

    document.getElementById('modalPayableTotalDisplay').textContent = '৳' + grandTotal.toFixed(2);
    document.getElementById('modalReceivedAmountInput').value = grandTotal.toFixed(2);

    document.getElementById('pay_cash').checked = true;
    calculateChangeAndDue();

    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
  }

  // Payment Type Change
  function onPaymentTypeChange(type) {
    let subtotal = 0;
    cart.forEach(item => subtotal += (item.price * item.quantity) - item.discount);
    const discountVal = parseFloat(document.getElementById('cartDiscountInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountAmount = discountType === 'percentage' ? (subtotal * discountVal) / 100 : discountVal;
    const taxPercentage = parseFloat(document.getElementById('cartTaxInput').value) || 0;
    const grandTotal = Math.max(0, subtotal - discountAmount) + (Math.max(0, subtotal - discountAmount) * taxPercentage) / 100;

    const receivedInput = document.getElementById('modalReceivedAmountInput');

    if (type === 'credit') {
      receivedInput.value = '0.00';
    } else {
      receivedInput.value = grandTotal.toFixed(2);
    }
    calculateChangeAndDue();
  }

  // Calculate Change & Due in Modal
  function calculateChangeAndDue() {
    let subtotal = 0;
    cart.forEach(item => subtotal += (item.price * item.quantity) - item.discount);
    const discountVal = parseFloat(document.getElementById('cartDiscountInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountAmount = discountType === 'percentage' ? (subtotal * discountVal) / 100 : discountVal;
    const taxPercentage = parseFloat(document.getElementById('cartTaxInput').value) || 0;
    const grandTotal = Math.max(0, subtotal - discountAmount) + (Math.max(0, subtotal - discountAmount) * taxPercentage) / 100;

    const received = parseFloat(document.getElementById('modalReceivedAmountInput').value) || 0;

    const change = Math.max(0, received - grandTotal);
    const due = Math.max(0, grandTotal - received);

    document.getElementById('modalChangeDisplay').textContent = '৳' + change.toFixed(2);
    document.getElementById('modalDueDisplay').textContent = '৳' + due.toFixed(2);
  }

  function setExactCash() {
    let subtotal = 0;
    cart.forEach(item => subtotal += (item.price * item.quantity) - item.discount);
    const discountVal = parseFloat(document.getElementById('cartDiscountInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountAmount = discountType === 'percentage' ? (subtotal * discountVal) / 100 : discountVal;
    const taxPercentage = parseFloat(document.getElementById('cartTaxInput').value) || 0;
    const grandTotal = Math.max(0, subtotal - discountAmount) + (Math.max(0, subtotal - discountAmount) * taxPercentage) / 100;

    document.getElementById('modalReceivedAmountInput').value = grandTotal.toFixed(2);
    calculateChangeAndDue();
  }

  function addCash(amount) {
    const cur = parseFloat(document.getElementById('modalReceivedAmountInput').value) || 0;
    document.getElementById('modalReceivedAmountInput').value = (cur + amount).toFixed(2);
    calculateChangeAndDue();
  }

  // Submit Sale Checkout
  function submitSaleCheckout() {
    if (cart.length === 0) return;

    const btn = document.getElementById('confirmSaleBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

    let subtotal = 0;
    cart.forEach(item => subtotal += (item.price * item.quantity) - item.discount);
    const discountVal = parseFloat(document.getElementById('cartDiscountInput').value) || 0;
    const discountType = document.getElementById('discountTypeSelect').value;
    const discountAmount = discountType === 'percentage' ? (subtotal * discountVal) / 100 : discountVal;
    const taxPercentage = parseFloat(document.getElementById('cartTaxInput').value) || 0;
    const taxAmount = (Math.max(0, subtotal - discountAmount) * taxPercentage) / 100;
    const grandTotal = Math.max(0, subtotal - discountAmount) + taxAmount;
    const paidAmount = parseFloat(document.getElementById('modalReceivedAmountInput').value) || 0;
    const paymentType = document.querySelector('input[name="payment_type_radio"]:checked').value;
    const customerId = document.getElementById('customerSelect').value;
    const note = document.getElementById('modalOrderNote').value;

    const payload = {
      customer_id: customerId,
      items: cart.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price,
        discount: item.discount
      })),
      subtotal: subtotal,
      discount_type: discountType,
      discount_amount: discountAmount,
      tax_percentage: taxPercentage,
      tax_amount: taxAmount,
      total_amount: grandTotal,
      paid_amount: paidAmount,
      payment_type: paymentType,
      note: note,
      payments: [
        {
          method: paymentType,
          amount: Math.min(paidAmount, grandTotal)
        }
      ]
    };

    fetch('{{ route("pos.checkout") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Complete Sale (Print Receipt)';

      if (data.success) {
        lastCompletedSale = data;
        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();

        document.getElementById('successInvoiceNo').textContent = 'Invoice: ' + data.invoice_no;
        const successModal = new bootstrap.Modal(document.getElementById('successReceiptModal'));
        successModal.show();
      } else {
        alert(data.message || 'Error occurred while saving sale.');
      }
    })
    .catch(err => {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Complete Sale (Print Receipt)';
      console.error(err);
      alert('Checkout failed! Please check stock availability and try again.');
    });
  }

  // Print Receipt in Popup Window
  function printReceiptPopup() {
    if (!lastCompletedSale) return;
    const w = window.open(lastCompletedSale.receipt_url, '_blank', 'width=420,height=600');
    if (w) w.focus();
  }

  function printInvoicePopup() {
    if (!lastCompletedSale) return;
    window.open(lastCompletedSale.invoice_url, '_blank');
  }

  function resetPosForNextCustomer() {
    cart = [];
    renderCart();
    document.getElementById('cartDiscountInput').value = '0';
    document.getElementById('cartTaxInput').value = '0';
    document.getElementById('modalOrderNote').value = '';
    document.getElementById('barcodeScannerInput').focus();
  }

  // Quick Customer Creation via AJAX
  function submitQuickCustomer(e) {
    e.preventDefault();
    const name = document.getElementById('quickCustomerName').value.trim();
    const phone = document.getElementById('quickCustomerPhone').value.trim();

    fetch('{{ route("customers.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({ name: name, phone: phone })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const select = document.getElementById('customerSelect');
        const opt = document.createElement('option');
        opt.value = data.customer.id;
        opt.textContent = `${data.customer.name} (${data.customer.phone || 'N/A'})`;
        opt.selected = true;
        select.appendChild(opt);

        bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal')).hide();
        document.getElementById('quickCustomerForm').reset();
      } else {
        alert('Failed to add customer: ' + (data.message || ''));
      }
    })
    .catch(err => alert('Error adding customer.'));
  }

  // Hold / Park Current Sale
  function holdCurrentSale() {
    if (cart.length === 0) {
      alert('Cart is empty. Nothing to hold.');
      return;
    }

    const customerSelect = document.getElementById('customerSelect');
    const customerName = customerSelect.options[customerSelect.selectedIndex].text;

    const heldItem = {
      id: Date.now(),
      time: new Date().toLocaleTimeString(),
      customerName: customerName,
      customerId: customerSelect.value,
      cart: [...cart]
    };

    heldSales.push(heldItem);
    localStorage.setItem('reza_pos_held_sales', JSON.stringify(heldSales));

    cart = [];
    renderCart();
    updateHeldCount();
    alert('Sale has been parked on hold! You can now serve the next customer.');
  }

  function updateHeldCount() {
    document.getElementById('heldCount').textContent = heldSales.length;
  }

  function toggleHoldListModal() {
    const list = document.getElementById('heldSalesList');
    if (heldSales.length === 0) {
      list.innerHTML = '<div class="text-center py-4 text-muted">No sales currently on hold.</div>';
    } else {
      let html = '';
      heldSales.forEach((h, index) => {
        let total = 0;
        h.cart.forEach(i => total += (i.price * i.quantity));
        html += `
          <div class="list-group-item d-flex justify-content-between align-items-center py-2">
            <div>
              <div class="fw-bold text-dark">${h.customerName}</div>
              <small class="text-muted">Time: ${h.time} | Items: ${h.cart.length}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="fw-bold text-primary">৳${total.toFixed(2)}</span>
              <button type="button" class="btn btn-sm btn-primary" onclick="restoreHeldSale(${index})">Retrieve</button>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteHeldSale(${index})"><i class="bi bi-trash"></i></button>
            </div>
          </div>
        `;
      });
      list.innerHTML = html;
    }

    new bootstrap.Modal(document.getElementById('heldSalesModal')).show();
  }

  function restoreHeldSale(index) {
    if (cart.length > 0 && !confirm('Restoring will replace current cart. Continue?')) {
      return;
    }
    const retrieved = heldSales.splice(index, 1)[0];
    localStorage.setItem('reza_pos_held_sales', JSON.stringify(heldSales));

    cart = retrieved.cart;
    document.getElementById('customerSelect').value = retrieved.customerId;
    renderCart();
    updateHeldCount();
    bootstrap.Modal.getInstance(document.getElementById('heldSalesModal')).hide();
  }

  function deleteHeldSale(index) {
    if (confirm('Delete this held sale?')) {
      heldSales.splice(index, 1);
      localStorage.setItem('reza_pos_held_sales', JSON.stringify(heldSales));
      updateHeldCount();
      toggleHoldListModal();
    }
  }
</script>
@endpush
