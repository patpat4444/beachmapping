<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Log in — Dagat ta bAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/auth.css">
  </head>
  <body class="auth-page">
    <header class="auth-appbar d-flex align-items-center">
      <a href="/landing" class="brand">Dagat ta bAI</a>
      <div class="ms-auto">
        <a href="{{ route('register') }}" class="btn-auth-nav">Sign up</a>
      </div>
    </header>

    <main class="auth-main">
      <div class="auth-card">
        <div class="auth-card-header">
          <h1 class="auth-card-title">Log in</h1>
          <p class="auth-card-subtitle">Welcome back. Sign in to continue.</p>
        </div>
        <div class="auth-card-body">
          @if ($errors->any())
            <div class="alert alert-danger py-2 mb-3">
              @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
              @endforeach
            </div>
          @endif
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ request('redirect', old('redirect', '/landing')) }}">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="remember" name="remember">
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary">Log in</button>
          </form>
          <div class="auth-footer">
            <p>Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
          </div>
        </div>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
