<x-guest-layout>
  <div class="pt-4 pb-2">
    <h5 class="card-title text-center pb-0 fs-4">Forgot Password?</h5>
    <p class="text-center small text-muted">Enter your registered email to receive a password reset link.</p>
  </div>

  <!-- Session Status -->
  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form method="POST" action="{{ route('password.email') }}" class="row g-3 needs-validation" novalidate>
    @csrf

    <!-- Email Address -->
    <div class="col-12">
      <label for="email" class="form-label">Email Address</label>
      <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" required autofocus>
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <!-- Submit Button -->
    <div class="col-12">
      <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit">
        <i class="bi bi-send me-1"></i> Send Password Reset Link
      </button>
    </div>

    <div class="col-12 text-center">
      <p class="small mb-0">Back to <a href="{{ route('login') }}">Login</a></p>
    </div>
  </form>
</x-guest-layout>
