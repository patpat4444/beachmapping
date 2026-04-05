<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create account</title>
    <script src="/js/theme.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/register.css">
    <link rel="icon" type="image/png" href="/storage/locations/logo.png">
  </head>
  <body>
    <div class="auth-container">
      <div class="left-panel">
        <div class="brand-header">
          <a href="/landing" class="brand">Dagat Ta <span class="highlight">bAI</span></a>
        </div>

        <div class="hero-content">
          <h1 class="hero-title">Explore the <span class="shores">shores</span><br>of Binongkalan</h1>

          <div class="features-list">
            <div class="feature-item">
              <div class="feature-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                </svg>
              </div>
              <div class="feature-text">
                <h4>Interactive Beach Maps</h4>
                <p>Explore all beach destinations with real-time geospatial data.</p>
              </div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                  <circle cx="12" cy="5" r="2"></circle>
                  <path d="M12 7v4"></path>
                  <line x1="8" y1="16" x2="8" y2="16"></line>
                  <line x1="16" y1="16" x2="16" y2="16"></line>
                </svg>
              </div>
              <div class="feature-text">
                <h4>AI Inquiry Assistant</h4>
                <p>Get instant answers about facilities, directions, and tips.</p>
              </div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                </svg>
              </div>
              <div class="feature-text">
                <h4>Live Weather Analytics</h4>
                <p>Check real-time forecasts before planning your beach visit.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="right-panel">
        <div class="auth-card">
          <div class="auth-card-header">
            <h2 class="auth-card-title">Create account</h2>
            <p class="auth-card-subtitle">Already have one? <a href="{{ route('login') }}">Log in here</a></p>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger">
              @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
              <label class="form-label" for="email">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
            </div>

            <div class="form-group">
              <label class="form-label" for="password">Password</label>
              <div class="password-wrapper">
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                <button type="button" class="password-toggle" id="togglePassword">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                  </svg>
                </button>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="password_confirmation">Confirm Password</label>
              <div class="password-wrapper">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter password">
                <button type="button" class="password-toggle" id="toggleConfirmPassword">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                  </svg>
                </button>
              </div>
              <span id="password-match" class="password-match"></span>
            </div>

            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
              <label class="form-check-label" for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="submit" class="btn-primary">Sign up</button>
          </form>

          <div class="divider">or continue with</div>

          <div class="social-buttons">
            <button type="button" class="btn-social btn-google">
              <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
            </button>

            <button type="button" class="btn-social btn-facebook">
              <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');

      togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Change icon - closed eye (slashed) when password hidden, open eye when visible
        if (type === 'text') {
          // Password is visible, show open eye
          togglePassword.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        } else {
          // Password is hidden, show closed (slashed) eye
          togglePassword.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        }
      });

      const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
      const confirmPasswordInput = document.getElementById('password_confirmation');

      toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);

        // Change icon - closed eye (slashed) when password hidden, open eye when visible
        if (type === 'text') {
          // Password is visible, show open eye
          toggleConfirmPassword.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        } else {
          // Password is hidden, show closed (slashed) eye
          toggleConfirmPassword.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        }
      });

      // Password match validation
      const passwordMatch = document.getElementById('password-match');
      let isSubmitting = false;

      function checkPasswordMatch() {
        if (isSubmitting || confirmPasswordInput.value === '') {
          passwordMatch.textContent = '';
          passwordMatch.className = 'password-match';
          return;
        }
        if (passwordInput.value === confirmPasswordInput.value) {
          passwordMatch.textContent = 'Passwords match';
          passwordMatch.className = 'password-match match';
        } else {
          passwordMatch.textContent = 'Passwords do not match';
          passwordMatch.className = 'password-match mismatch';
        }
      }

      confirmPasswordInput.addEventListener('blur', checkPasswordMatch);
      confirmPasswordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value === '') {
          passwordMatch.textContent = '';
          passwordMatch.className = 'password-match';
        }
      });

      // Hide message on form submit
      document.querySelector('form').addEventListener('submit', function() {
        isSubmitting = true;
        passwordMatch.textContent = '';
        passwordMatch.className = 'password-match';
      });
    </script>
  </body>
</html>
