<x-guest-layout>
  <div class="pt-4 pb-2">
    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
    <p class="text-center small text-muted">Enter your email and password to access Reza POS</p>
  </div>

  <!-- Session Status -->
  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="row g-3 needs-validation" novalidate>
    @csrf

    <!-- Email Address -->
    <div class="col-12">
      <label for="email" class="form-label">Email Address</label>
      <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Password -->
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <label for="password" class="form-label mb-0">Password</label>
        @if (Route::has('password.request'))
          <a class="small text-muted" href="{{ route('password.request') }}">
            Forgot password?
          </a>
        @endif
      </div>
      <div class="input-group has-validation mt-1">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" required autocomplete="current-password">
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Remember Me -->
    <div class="col-12">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
        <label class="form-check-label text-muted small" for="rememberMe">Remember me</label>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="col-12">
      <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
      </button>
    </div>

    <!-- Demo Credentials helper -->
    <div class="col-12 mt-3 pt-3 border-top text-center">
      <div class="badge bg-light text-dark p-2 w-100 text-start">
        <div class="fw-bold mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Demo Login:</div>
        <div class="small">Admin: <code>admin@gmail.com</code> | <code>password</code></div>
        <div class="small">Cashier: <code>cashier@gmail.com</code> | <code>password</code></div>
      </div>
    </div>

    @if (Route::has('register'))
      <div class="col-12 text-center">
        <p class="small mb-0">Don't have an account? <a href="{{ route('register') }}">Create an account</a></p>
      </div>
    @endif
  </form>
</x-guest-layout>
