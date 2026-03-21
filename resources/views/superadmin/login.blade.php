<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Super Admin Login - Dagat Ta bAI</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="stylesheet" href="/css/adminlog.css" />
</head>
<body>
  <!-- Theme Toggle -->
  <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
    <i class="fa-solid fa-sun" id="themeIcon"></i>
  </button>

  <div class="admin-login-container">
    <!-- Left Panel -->
    <div class="left-panel">
      <div class="left-content">
        <div class="brand">Dagat Ta <em>bAI</em></div>
        
        <div class="admin-badge">
          <i class="fa-solid fa-lock"></i>
          Super Admin Portal
        </div>
        
        <h1 class="left-title">Secure <em>Super</em><br>Access</h1>
        
        <p class="left-description">
          Manage beach destinations, system data, and user accounts from one centralized dashboard.
        </p>
        
        <ul class="feature-list">
          <li>Manage beach destinations</li>
          <li>Monitor system activity</li>
          <li>Update weather and AI data</li>
          <li>Manage user accounts</li>
        </ul>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div class="right-content">
        <h2 class="login-title">Super Admin <em>Login</em></h2>
        <p class="login-description">Enter your 6-digit PIN to access the dashboard</p>
        
        @if(session('error'))
          <div class="alert alert-error" style="margin-bottom: 20px; padding: 12px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; color: #ef4444; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        <form id="superAdminLoginForm" method="POST" action="{{ route('superadmin.verify') }}">
          @csrf
          
          <!-- Hidden email input -->
          <input type="hidden" name="email" id="emailInput" value="superadmin@example.com">

          <div class="pin-label">6-Digit PIN</div>
          
          <div class="pin-inputs">
            <input type="password" class="pin-input" maxlength="1" name="pin_1" id="pin1" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
            <input type="password" class="pin-input" maxlength="1" name="pin_2" id="pin2" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
            <input type="password" class="pin-input" maxlength="1" name="pin_3" id="pin3" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
            <input type="password" class="pin-input" maxlength="1" name="pin_4" id="pin4" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
            <input type="password" class="pin-input" maxlength="1" name="pin_5" id="pin5" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
            <input type="password" class="pin-input" maxlength="1" name="pin_6" id="pin6" autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
          </div>
          
          <input type="hidden" name="pin" id="fullPin" />
          
          <div style="margin-top: 24px; max-width: 348px;">
            <button type="submit" class="access-btn" id="accessBtn" disabled style="width: 100%;">Access Dashboard</button>
          </div>
        </form>
        
        <div class="login-footer" style="justify-content: flex-end; max-width: 348px;">
          <a href="#" class="forgot-pin">Forgot PIN? Contact developer</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;
    
    const savedTheme = localStorage.getItem('superadmin-theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    
    themeToggle.addEventListener('click', () => {
      const current = html.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-theme', next);
      localStorage.setItem('superadmin-theme', next);
      updateThemeIcon(next);
    });
    
    function updateThemeIcon(theme) {
      themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
    
    // PIN input handling
    const pinInputs = document.querySelectorAll('.pin-input');
    const fullPin = document.getElementById('fullPin');
    const accessBtn = document.getElementById('accessBtn');
    const form = document.getElementById('superAdminLoginForm');
    
    function updatePinState() {
      let pin = '';
      let filledCount = 0;
      
      pinInputs.forEach((input, index) => {
        const value = input.value;
        pin += value;
        
        if (value) {
          filledCount++;
          input.classList.add('filled');
        } else {
          input.classList.remove('filled');
        }
      });
      
      fullPin.value = pin;
      accessBtn.disabled = filledCount !== 6;
    }
    
    pinInputs.forEach((input, index) => {
      // Handle input
      input.addEventListener('input', (e) => {
        const value = e.target.value;
        
        // Only allow numbers
        if (!/^\d*$/.test(value)) {
          e.target.value = '';
          return;
        }
        
        if (value) {
          updatePinState();
          // Auto-focus next input
          if (index < pinInputs.length - 1) {
            pinInputs[index + 1].focus();
          }
        }
      });
      
      // Handle backspace
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          pinInputs[index - 1].focus();
        }
        
        // Allow only numbers
        if (e.key.length === 1 && !/^\d$/.test(e.key)) {
          e.preventDefault();
        }
      });
      
      // Handle paste
      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
        
        pastedData.split('').forEach((digit, i) => {
          if (pinInputs[i]) {
            pinInputs[i].value = digit;
          }
        });
        
        updatePinState();
        
        // Focus the next empty input or the last one
        const nextEmpty = Array.from(pinInputs).find(inp => !inp.value);
        if (nextEmpty) {
          nextEmpty.focus();
        } else if (pinInputs[pastedData.length - 1]) {
          pinInputs[pastedData.length - 1].focus();
        }
      });
    });
    
    // Form submission
    form.addEventListener('submit', (e) => {
      if (fullPin.value.length !== 6) {
        e.preventDefault();
        alert('Please enter all 6 digits of your PIN.');
      }
    });
    
    // Focus first input on load
    pinInputs[0].focus();
  </script>
</body>
</html>
