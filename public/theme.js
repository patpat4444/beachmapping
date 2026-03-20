  (function() {
  'use strict';

  // Apply theme immediately to prevent flash
  const html = document.documentElement;
  const savedTheme = localStorage.getItem('theme') || 'dark';
  html.setAttribute('data-theme', savedTheme);

  // Initialize theme when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    initTheme();
  });

  function initTheme() {
    const themeToggle = document.getElementById('theme-toggle');
    const themeLabel = document.getElementById('theme-label');
    const html = document.documentElement;

    if (!themeToggle) return;

    // Update UI to match current theme
    updateThemeUI(html.getAttribute('data-theme') || 'dark');

    // Toggle handler
    themeToggle.addEventListener('click', function() {
      const currentTheme = html.getAttribute('data-theme') || 'dark';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

      html.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateThemeUI(newTheme);

      // Dispatch custom event for other scripts
      window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: newTheme } }));
    });

    // Keyboard accessibility
    themeToggle.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        themeToggle.click();
      }
    });

    function updateThemeUI(theme) {
      // Update label if exists
      if (themeLabel) {
        themeLabel.textContent = theme === 'dark' ? 'Dark' : 'Light';
        themeLabel.style.color = theme === 'dark' ? '#ffd700' : '#f4a460';
      }

      // Update landing page icons (sun/moon)
      const sunIcon = themeToggle.querySelector('.sun');
      const moonIcon = themeToggle.querySelector('.moon');
      if (sunIcon && moonIcon) {
        if (theme === 'dark') {
          sunIcon.style.opacity = '1';
          moonIcon.style.opacity = '0';
        } else {
          sunIcon.style.opacity = '0';
          moonIcon.style.opacity = '1';
        }
      }

      // Update icon text content (for other pages)
      const themeIcon = themeToggle.querySelector('.theme-icon');
      if (themeIcon && !sunIcon && !moonIcon) {
        themeIcon.textContent = theme === 'dark' ? '☀' : '☾';
      }
    }
  }
})();
