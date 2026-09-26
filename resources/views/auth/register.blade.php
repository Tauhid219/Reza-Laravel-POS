<x-guest-layout>
  <div class="pt-4 pb-2">
    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
    <p class="text-center small text-muted">Enter your details to create staff account</p>
  </div>

  <form method="POST" action="{{ route('register') }}" class="row g-3 needs-validation" novalidate>
    @csrf

    <!-- Name -->
    <div class="col-12">
      <label for="name" class="form-label">Full Name</label>
      <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        @error('name')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Email Address -->
    <div class="col-12">
      <label for="email" class="form-label">Email Address</label>
      <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" required autocomplete="username">
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Password -->
    <div class="col-12">
      <label for="password" class="form-label">Password</label>
      <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" required autocomplete="new-password">
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Confirm Password -->
    <div class="col-12">
      <label for="password_confirmation" class="form-label">Confirm Password</label>
      <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Submit Button -->
    <div class="col-12">
      <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit">
        <i class="bi bi-person-plus me-1"></i> Register Account
      </button>
    </div>

    <div class="col-12 text-center">
      <p class="small mb-0">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
    </div>
  </form>
</x-guest-layout>
