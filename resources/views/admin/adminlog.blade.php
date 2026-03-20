<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login - Dagat Ta bAI</title>
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
          Admin Portal
        </div>
        
        <h1 class="left-title">Secure <em>Admin</em><br>Access</h1>
        
        <p class="left-description">
          Manage beach destinations, system data, and user accounts from one centralized dashboard.
        </p>
        
        <ul class="feature-list">
          <li>Manage beach destinations</li>
          <li>Monitor system activity</li>
          <li>Update weather and AI data</li>
          <li>Manage user accounts</li>
        </ul>
        
        <div class="location-tags">
          <span class="location-tag">BINONGKALAN</span>
          <span class="location-tag">CATMON</span>
          <span class="location-tag">CEBU</span>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div class="right-content">
        <h2 class="login-title">Admin <em>Login</em></h2>
        <p class="login-description">Enter your 6-digit PIN to access the admin dashboard</p>
        
        <form id="adminLoginForm" method="POST" action="{{ route('admin.verify') }}">
          @csrf
          
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
          
          <div class="pin-counter" id="pinCounter">0 of 6 digits entered</div>
          
          <button type="submit" class="access-btn" id="accessBtn" disabled>Access Dashboard</button>
        </form>
        
        <div class="login-footer">
          <button type="button" class="clear-pin" id="clearPin">
            <i class="fa-regular fa-square-check"></i>
            Clear PIN
          </button>
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
    
    const savedTheme = localStorage.getItem('admin-theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
    
    themeToggle.addEventListener('click', () => {
      const current = html.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-theme', next);
      localStorage.setItem('admin-theme', next);
      updateThemeIcon(next);
    });
    
    function updateThemeIcon(theme) {
      themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
    
    // PIN input handling
    const pinInputs = document.querySelectorAll('.pin-input');
    const pinCounter = document.getElementById('pinCounter');
    const fullPin = document.getElementById('fullPin');
    const accessBtn = document.getElementById('accessBtn');
    const clearPinBtn = document.getElementById('clearPin');
    const form = document.getElementById('adminLoginForm');
    
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
      pinCounter.textContent = `${filledCount} of 6 digits entered`;
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
    
    // Clear PIN button
    clearPinBtn.addEventListener('click', () => {
      pinInputs.forEach(input => {
        input.value = '';
        input.classList.remove('filled');
      });
      fullPin.value = '';
      pinCounter.textContent = '0 of 6 digits entered';
      accessBtn.disabled = true;
      pinInputs[0].focus();
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
