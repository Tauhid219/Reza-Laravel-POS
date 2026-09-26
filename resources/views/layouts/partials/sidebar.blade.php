<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard Nav -->
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- POS Terminal -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('pos*') ? '' : 'collapsed' }} text-primary fw-bold" href="{{ url('/pos') }}">
        <i class="bi bi-cart4 text-primary"></i>
        <span>POS Terminal</span>
      </a>
    </li>

    <li class="nav-heading">Operations</li>

    <!-- Sales & Orders -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('orders*') ? '' : 'collapsed' }}" data-bs-target="#sales-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-receipt"></i><span>Sales & Orders</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="sales-nav" class="nav-content collapse {{ request()->is('orders*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/orders') }}" class="{{ request()->is('orders') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>All Sales List</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/orders/due') }}" class="{{ request()->is('orders/due*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Due & Credit Orders</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Products & Inventory -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('products*') || request()->is('categories*') || request()->is('units*') ? '' : 'collapsed' }}" data-bs-target="#inventory-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-box-seam"></i><span>Inventory</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="inventory-nav" class="nav-content collapse {{ request()->is('products*') || request()->is('categories*') || request()->is('units*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/products') }}" class="{{ request()->is('products') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Products Catalog</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/products/create') }}" class="{{ request()->is('products/create') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Add New Product</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/categories') }}" class="{{ request()->is('categories*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Categories</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/units') }}" class="{{ request()->is('units*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Units of Measure</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/products/barcodes') }}" class="{{ request()->is('products/barcodes*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Print Barcodes</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Procurement & Purchases -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('purchases*') || request()->is('suppliers*') ? '' : 'collapsed' }}" data-bs-target="#purchase-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-truck"></i><span>Procurement</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="purchase-nav" class="nav-content collapse {{ request()->is('purchases*') || request()->is('suppliers*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/purchases') }}" class="{{ request()->is('purchases') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Purchase List</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/purchases/create') }}" class="{{ request()->is('purchases/create') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>New Stock Purchase</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/suppliers') }}" class="{{ request()->is('suppliers*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Suppliers</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Customers & CRM -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('customers*') ? '' : 'collapsed' }}" data-bs-target="#customer-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Customers</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="customer-nav" class="nav-content collapse {{ request()->is('customers*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/customers') }}" class="{{ request()->is('customers') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>All Customers</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/customers/ledger') }}" class="{{ request()->is('customers/ledger*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Customer Due Ledger</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Cash Register & Shifts -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('cash-register*') ? '' : 'collapsed' }}" data-bs-target="#register-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-wallet2"></i><span>Cash Register</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="register-nav" class="nav-content collapse {{ request()->is('cash-register*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/cash-register') }}" class="{{ request()->is('cash-register') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Current Shift / Drawer</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/cash-register/history') }}" class="{{ request()->is('cash-register/history*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Shift History Logs</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-heading">Reports & Analytics</li>

    <!-- Reports -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('reports*') ? '' : 'collapsed' }}" data-bs-target="#reports-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-graph-up-arrow"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="reports-nav" class="nav-content collapse {{ request()->is('reports*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/reports/sales') }}" class="{{ request()->is('reports/sales*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Daily / Monthly Sales</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/reports/profit-loss') }}" class="{{ request()->is('reports/profit-loss*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Profit & Loss</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/reports/stock') }}" class="{{ request()->is('reports/stock*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Stock & Inventory Value</span>
          </a>
        </li>
      </ul>
    </li>

    @hasanyrole('Admin|Super Admin')
    <li class="nav-heading">System Administration</li>

    <!-- User Management -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('users*') || request()->is('roles*') ? '' : 'collapsed' }}" data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-shield-lock"></i><span>User & Roles</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="users-nav" class="nav-content collapse {{ request()->is('users*') || request()->is('roles*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
        <li>
          <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Staff Users</span>
          </a>
        </li>
        <li>
          <a href="{{ url('/roles') }}" class="{{ request()->is('roles*') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Roles & Permissions</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Settings -->
    <li class="nav-item">
      <a class="nav-link {{ request()->is('settings*') ? '' : 'collapsed' }}" href="{{ url('/settings') }}">
        <i class="bi bi-gear"></i>
        <span>Store Settings</span>
      </a>
    </li>
    @endhasanyrole

  </ul>

</aside><!-- End Sidebar-->
