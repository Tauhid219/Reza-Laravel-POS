<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
      <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
      <span class="d-none d-lg-block">{{ \App\Models\Setting::get('company_name', 'Reza POS') }}</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <div class="search-bar">
    <form class="search-form d-flex align-items-center" method="GET" action="#">
      <input type="text" name="query" placeholder="Search orders, products, customers..." title="Enter search keyword">
      <button type="submit" title="Search"><i class="bi bi-search"></i></button>
    </form>
  </div><!-- End Search Bar -->

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle " href="#">
          <i class="bi bi-search"></i>
        </a>
      </li><!-- End Search Icon-->

      <!-- POS Shortcut Button -->
      <li class="nav-item pe-2">
        <a href="{{ url('/pos') }}" class="btn btn-sm btn-primary rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm">
          <i class="bi bi-cart4 fs-6"></i>
          <span class="fw-semibold">POS Terminal</span>
        </a>
      </li>

      <!-- Notification Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span class="badge bg-danger badge-number">1</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
          <li class="dropdown-header">
            System Notifications
            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li class="notification-item">
            <i class="bi bi-exclamation-triangle text-warning"></i>
            <div>
              <h4>Low Stock Alert</h4>
              <p>Check low stock items in inventory</p>
              <p class="text-muted small">Just now</p>
            </div>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li class="dropdown-footer">
            <a href="#">Show all notifications</a>
          </li>
        </ul>
      </li><!-- End Notification Nav -->

      <!-- User Profile Dropdown -->
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="{{ Auth::user()->photo ? asset(Auth::user()->photo) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
          <span class="d-none d-md-block dropdown-toggle ps-2 fw-semibold">{{ Auth::user()->name }}</span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header text-start">
            <h6 class="fw-bold mb-0">{{ Auth::user()->name }}</h6>
            <span class="badge bg-info-subtle text-info border border-info-subtle text-capitalize">
              {{ Auth::user()->roles->pluck('name')->first() ?? 'Staff' }}
            </span>
          </li>
          <li><hr class="dropdown-divider"></li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>

          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center text-danger border-0 bg-transparent w-100">
                <i class="bi bi-box-arrow-right"></i>
                <span>Log Out</span>
              </button>
            </form>
          </li>

        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->

</header><!-- End Header -->
