<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Explore Binongkalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/landing.css">
    <link rel="icon" type="image/png" href="/storage/locations/logo.png">
  </head>
  <body>
    <!-- Header -->
    <header class="landing-header">
      <div class="header-container">
        <a href="/" class="brand">Dagat Ta <span class="highlight">bAI</span></a>
        
        <nav class="main-nav">
          <a href="#features">FEATURES</a>
          <a href="#beaches">BEACHES</a>
          <a href="#weather">WEATHER</a>
          <a href="#ai-guide">AI GUIDE</a>
        </nav>
        
        <div class="header-actions">
          <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
          <a href="{{ route('register') }}" class="btn btn-primary">Sign up</a>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
      <video class="hero-video" autoplay muted loop playsinline>
        <source src="/storage/locations/bg.mp4" type="video/mp4">
      </video>
      <div class="hero-overlay"></div>
      <div class="hero-container">
        <div class="hero-logo">
          <img src="/storage/locations/logo.png" alt="Dagat Ta bAI Logo">
        </div>
        
        <h1 class="hero-title">Dagat Ta <span class="highlight">bAI</span></h1>
        
        <p class="hero-subtitle">
        </p>
        
        <div class="hero-cta">
          <a href="{{ route('explore') }}" class="btn btn-cta-primary">Explore the Map</a>
          <a href="#features" class="btn btn-cta-secondary">Learn More</a>
        </div>
      </div>
      
      <div class="scroll-indicator">
        <span>SCROLL TO EXPLORE</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M19 12l-7 7-7-7"/>
        </svg>
      </div>
    </section>

    <!-- Floating Theme Toggle -->
    <button class="theme-toggle-floating" id="theme-toggle" aria-label="Toggle theme">
      <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="5"/>
        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
      </svg>
      <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
      </svg>
    </button>

    <!-- What We Offer Section -->
    <section class="features-section" id="features">
      <div class="section-container">
        <span class="section-label">WHAT WE OFFER</span>
        <h2 class="section-title">Everything you need for your <span class="highlight">beach trip</span></h2>
        <p class="section-subtitle">
          Dagat Ta bAI combines three powerful technologies into one easy-to-use platform for tourists and visitors in Binongkalan, Catmon, Cebu.
        </p>
        
        <div class="features-grid">
          <div class="feature-card">
            <div class="card-accent blue"></div>
            <div class="feature-icon">🗺️</div>
            <h3>Geospatial Mapping</h3>
            <p>Explore an interactive digital map showing all 8 commercial beach destinations in Barangay Binongkalan with precise locations, routes, and facilities.</p>
          </div>
          
          <div class="feature-card">
            <div class="card-accent green"></div>
            <div class="feature-icon">🤖</div>
            <h3>AI Inquiry Assistant</h3>
            <p>Ask anything about beach conditions, facilities, directions, and travel tips. Our AI chatbot gives instant, accurate answers anytime.</p>
          </div>
          
          <div class="feature-card">
            <div class="card-accent yellow"></div>
            <div class="feature-icon">☀️</div>
            <h3>Weather Analytics</h3>
            <p>Access real-time weather data including temperature, wind speed, wave height, and UV index to plan the perfect beach day.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Beach Destinations Section -->
    <section class="beaches-section" id="beaches">
      <div class="section-container">
        <span class="section-label">DISCOVER</span>
        <h2 class="section-title">Commercial <span class="highlight">Beach Destinations</span></h2>
        <p class="section-subtitle">
          Browse all mapped beach resorts in Barangay Binongkalan, Catmon, Cebu — with real-time status and weather info.
        </p>
        
        <div class="beaches-grid">
          <div class="beach-card">
            <div class="beach-image">
              <span class="placeholder">🖼️ Beach photo</span>
            </div>
            <div class="beach-info">
              <h4>Sta. Rosa Beach</h4>
              <div class="beach-meta">
                <span>0.8 km</span>
                <span class="status open">● Open</span>
              </div>
            </div>
          </div>
          
          <div class="beach-card">
            <div class="beach-image">
              <span class="placeholder">🖼️ Beach photo</span>
            </div>
            <div class="beach-info">
              <h4>Catmon White Sand</h4>
              <div class="beach-meta">
                <span>1.4 km</span>
                <span class="status open">● Open</span>
              </div>
            </div>
          </div>
          
          <div class="beach-card">
            <div class="beach-image">
              <span class="placeholder">🖼️ Beach photo</span>
            </div>
            <div class="beach-info">
              <h4>Binongkalan Cove</h4>
              <div class="beach-meta">
                <span>2.1 km</span>
                <span class="status open">● Open</span>
              </div>
            </div>
          </div>
          
          <div class="beach-card">
            <div class="beach-image">
              <span class="placeholder">🖼️ Beach photo</span>
            </div>
            <div class="beach-info">
              <h4>Coral Bay Resort</h4>
              <div class="beach-meta">
                <span>3.7 km</span>
                <span class="status open">● Open</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-container">
        <h2 class="cta-title">Ready to explore<br><span class="highlight">Binongkalan's</span> shores?</h2>
        <p class="cta-subtitle">
          Create a free account and start discovering the best beach destinations in Catmon, Cebu today.
        </p>
        <a href="{{ route('register') }}" class="btn btn-cta-main">Get Started — It's Free</a>
      </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
      <div class="footer-container">
        <div class="footer-brand">
          <span class="brand">Dagat Ta <span class="highlight">bAI</span></span>
        </div>
        
        <div class="footer-copy">
          © 2025 Dagat Ta bAI · Binongkalan, Catmon, Cebu
        </div>
        
        <div class="footer-links">
          <a href="#">Privacy Policy</a>
          <a href="#">Terms</a>
          <a href="#">Contact</a>
        </div>
      </div>
    </footer>

    <script>
      const themeToggle = document.getElementById('theme-toggle');
      const html = document.documentElement;

      const savedTheme = localStorage.getItem('theme') || 'dark';
      html.setAttribute('data-theme', savedTheme);

      themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
      });

      themeToggle.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          themeToggle.click();
        }
      });
    </script>
  </body>
</html>
