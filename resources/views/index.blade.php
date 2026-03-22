<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Explore Binongkalan</title>
    <script src="/theme.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/landing.css">
    <link rel="icon" type="image/png" href="/storage/locations/logo.png">
  </head>
  <body>
    <!-- Header -->
    <header class="landing-header">
      <div class="header-container">
        <a href="/" class="brand">Dagat Ta <span class="highlight"><i>bAI</i></span></a>
        
        <nav class="main-nav">
          
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
      
      <a class="scroll-indicator" href="#features" aria-label="Scroll to explore">
        <span>SCROLL TO EXPLORE</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
          <path d="M12 5v14M19 12l-7 7-7-7"/>
        </svg>
      </a>
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
        <h2 class="cta-title">Own a beach in <span class="highlight">Binongkalan</span>?</h2>
        <p class="cta-subtitle">
          List your beach resort on Dagat Ta bAI and reach more tourists. Create an account to get started with your application.
        </p>
        <a href="{{ route('register') }}" class="btn btn-cta-main">Apply as Beach Owner — It's Free</a>
      </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer" id="contact">
      <div class="footer-container">
        <div class="footer-main">
          <div class="footer-brand-section">
            <span class="brand">Dagat Ta <span class="highlight"><i>bAI</i></span></span>
            <p class="footer-desc">Your ultimate guide to beach destinations in Binongkalan, Catmon, Cebu.</p>
          </div>
          <div class="footer-links-group">
            <h4>Quick Links</h4>
            <a href="#features">Features</a>
            <a href="#beaches">Beaches</a>
            <a href="#weather">Weather</a>
            <a href="#ai-guide">AI Guide</a>
          </div>
          <div class="footer-links-group">
            <h4>Contact</h4>
            <a href="mailto:info@dagattabai.com"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>info@dagattabai.com</a>
            <a href="tel:+63123456789"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>+63 123 456 789</a>
            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Binongkalan, Catmon, Cebu</span>
          </div>
          <div class="footer-newsletter">
            <h4>Stay Updated</h4>
            <p>Subscribe to our newsletter for the latest beach updates.</p>
            <div class="newsletter-form">
              <input type="email" placeholder="Enter email address">
              <button type="submit">Subscribe</button>
            </div>
          </div>
        </div>
        
        <div class="footer-bottom">
          <div class="footer-legal">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
          </div>
          <div class="footer-copy">
            © 2026 Dagat Ta bAI · All rights reserved
          </div>
        </div>
      </div>
    </footer>
  </body>
</html>
