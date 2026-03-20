<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dagat Ta bAI — Explore beaches & places</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="/landing.css">
  </head>
  <body>
    <header class="landing-appbar d-flex align-items-center">
      <a href="/landing" class="brand">Dagat Ta <em>bAI</em></a>
      <div class="nav-links d-none d-md-flex">
        <a href="#map-section" class="nav-link active">MAP</a>
        <a href="#weather" class="nav-link">WEATHER</a>
        <a href="#ai-guide" class="nav-link">AI GUIDE</a>
      </div>
      <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        <div class="search-box">
          <i class="fa-solid fa-location-dot search-icon"></i>
          <input type="text" placeholder="Search place or location..." aria-label="Search" />
        </div>
        <button class="theme-toggle-btn" id="theme-toggle" type="button" aria-label="Toggle theme">
          <i class="fa-solid fa-sun theme-icon sun"></i>
          <i class="fa-solid fa-moon theme-icon moon"></i>
        </button>
        @auth
          <span class="user-welcome me-2">Welcome, {{ auth()->user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-appbar btn-outline-appbar">Log out</button>
          </form>
        @else
          <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn btn-appbar btn-outline-appbar">Log in</a>
          <a href="{{ route('register') }}" class="btn btn-appbar btn-solid">Sign up</a>
        @endauth
      </div>
    </header>

    <main class="landing-main">
      <div class="landing-sidebar">
        <div class="sidebar-header">
          <h2 class="sidebar-title">Beach <em>Destinations</em></h2>
        </div>
        <div class="landing-filters">
          <div class="filter-group">
            <select class="form-select" aria-label="Filter by type">
              <option>All Types</option>
              <option>Beaches</option>
              <option>Resort</option>
              <option>Public</option>
              <option>Snorkel</option>
            </select>
            <i class="fa-solid fa-chevron-down filter-arrow"></i>
          </div>
          <div class="filter-group">
            <select class="form-select" aria-label="Filter by rating">
              <option>Any Rating</option>
              <option>5 stars</option>
              <option>4+ stars</option>
              <option>3+ stars</option>
            </select>
            <i class="fa-solid fa-chevron-down filter-arrow"></i>
          </div>
        </div>
        <button type="button" id="locate-me" class="btn btn-locate">
          <i class="fa-solid fa-location-crosshairs"></i> Locate me
        </button>
        <div class="results-count">Showing 5 beaches in Binongkalan</div>
        <div id="place-cards" class="landing-cards-wrap">
          <!-- Beach cards will be populated by JS -->
        </div>
      </div>

      <div class="landing-map-wrap" id="map-section">
        <div id="map" class="h-100 w-100 rounded shadow-sm"></div>
      </div>

      <aside class="landing-right" aria-label="Widgets">
        <div class="widget weather-widget" id="weather">
          <div class="weather-header">
            <div class="widget-title">Current Weather</div>
            <div class="live-badge">LIVE</div>
          </div>
          <div class="weather-main">
            <div class="weather-icon-large">
              <i class="fa-solid fa-cloud-sun"></i>
            </div>
            <div class="weather-info">
              <div class="weather-temp-large" id="weather-temp">—°C</div>
              <div class="weather-desc" id="weather-desc">—</div>
            </div>
          </div>
          <div class="weather-metrics">
            <div class="metric">
              <div class="metric-value" id="weather-waves">—</div>
              <div class="metric-label">WAVES</div>
            </div>
            <div class="metric">
              <div class="metric-value" id="weather-wind">—</div>
              <div class="metric-label">WIND</div>
            </div>
            <div class="metric">
              <div class="metric-value" id="weather-humidity">—</div>
              <div class="metric-label">HUMIDITY</div>
            </div>
            <div class="metric">
              <div class="metric-value" id="weather-uv">—</div>
              <div class="metric-label">UV INDEX</div>
            </div>
          </div>
          <button class="btn-view-more" data-bs-toggle="modal" data-bs-target="#weatherModal">
            View more <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

        <div class="widget forecast-widget">
          <div class="widget-title">7-Day Forecast</div>
          <div class="forecast-list">
            <div class="forecast-item">
              <span class="day">Today</span>
              <i class="fa-solid fa-sun forecast-icon"></i>
              <span class="temps">31° / 26°</span>
            </div>
            <div class="forecast-item">
              <span class="day">Fri</span>
              <i class="fa-solid fa-cloud-sun forecast-icon"></i>
              <span class="temps">33° / 27°</span>
            </div>
            <div class="forecast-item">
              <span class="day">Sat</span>
              <i class="fa-solid fa-cloud-rain forecast-icon"></i>
              <span class="temps">28° / 24°</span>
            </div>
            <div class="forecast-item">
              <span class="day">Sun</span>
              <i class="fa-solid fa-sun forecast-icon"></i>
              <span class="temps">30° / 25°</span>
            </div>
            <div class="forecast-item">
              <span class="day">Mon</span>
              <i class="fa-solid fa-sun forecast-icon"></i>
              <span class="temps">34° / 27°</span>
            </div>
            <div class="forecast-item">
              <span class="day">Tue</span>
              <i class="fa-solid fa-sun forecast-icon"></i>
              <span class="temps">33° / 26°</span>
            </div>
            <div class="forecast-item">
              <span class="day">Wed</span>
              <i class="fa-solid fa-cloud-sun forecast-icon"></i>
              <span class="temps">29° / 25°</span>
            </div>
          </div>
        </div>
      </aside>
    </main>

    <!-- Floating AI Bot Button -->
    <button class="ai-bot-float" id="ai-bot-btn" type="button" data-bs-toggle="modal" data-bs-target="#aiModal" aria-label="Open AI Assistant">
      <i class="fa-solid fa-robot"></i>
      <span class="ai-bot-pulse"></span>
    </button>

    <!-- AI Chat Modal -->
    <div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ai-modal-content">
          <div class="modal-header ai-modal-header">
            <div class="ai-header-info">
              <i class="fa-solid fa-robot ai-bot-icon"></i>
              <div>
                <h5 class="modal-title" id="aiModalLabel">AI Assistant</h5>
                <span class="ai-status">Online</span>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body ai-modal-body">
            <div class="ai-chat-messages" id="ai-chat-messages">
              <div class="ai-message-bubble ai-bot">
                <i class="fa-solid fa-quote-left quote-icon"></i>
                "Best time to visit today is 7–10am. Waves are calm — great for swimming and snorkeling!"
              </div>
            </div>
            <div class="ai-suggestions">
              <button class="suggestion-chip" onclick="sendAiQuestion('Best beach for swimming?')">
                <i class="fa-solid fa-umbrella-beach"></i>
                Best beach for swimming?
              </button>
              <button class="suggestion-chip" onclick="sendAiQuestion('Will it rain this weekend?')">
                <i class="fa-solid fa-cloud-rain"></i>
                Will it rain this weekend?
              </button>
              <button class="suggestion-chip" onclick="sendAiQuestion('Where to snorkel nearby?')">
                <i class="fa-solid fa-mask"></i>
                Where to snorkel nearby?
              </button>
            </div>
          </div>
          <div class="modal-footer ai-modal-footer">
            <div class="ai-input-row">
              <input type="text" class="ai-input" id="ai-chat-input" placeholder="Ask about beaches..." aria-label="Ask about beaches" />
              <button type="button" class="ai-send" id="ai-chat-send" aria-label="Send">
                <i class="fa-solid fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="weatherModal" tabindex="-1" aria-labelledby="weatherModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content landing-modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="weatherModalLabel">Weather details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="weather-icon-large" id="weather-emoji-more">
                <i class="fa-solid fa-cloud-sun"></i>
              </div>
              <div>
                <div class="weather-temp-large" id="weather-temp-more">—°C</div>
                <div class="weather-desc" id="weather-desc-more">—</div>
                <div class="small" id="weather-location-more" style="opacity:0.75">—</div>
              </div>
            </div>
            <div class="weather-grid">
              <div class="weather-metric">
                <div class="metric-value" id="weather-feels-more">—</div>
                <div class="metric-label">Feels like</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-pressure-more">—</div>
                <div class="metric-label">Pressure</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-humidity-more">—</div>
                <div class="metric-label">Humidity</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-wind-more">—</div>
                <div class="metric-label">Wind</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-clouds-more">—</div>
                <div class="metric-label">Clouds</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-visibility-more">—</div>
                <div class="metric-label">Visibility</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-uv-more">—</div>
                <div class="metric-label">UV Index</div>
              </div>
              <div class="weather-metric">
                <div class="metric-value" id="weather-updated-more">—</div>
                <div class="metric-label">Updated</div>
              </div>
            </div>
            <div class="weather-loading" id="weather-loading-more">Loading weather…</div>
            <div class="weather-error" id="weather-error-more" style="display:none;">Could not load weather</div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade landing-modal" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel">Place details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <img id="detail-image" src="" alt="" style="width:100%;height:240px;object-fit:cover;border-radius:12px" />
              </div>
              <div class="col-md-6">
                <h4 id="detail-title" class="mb-2" style="font-weight:700;color:#1a1a2e"></h4>
                <div id="detail-rating" class="mb-2"></div>
                <p id="detail-description" class="text-muted small mb-2"></p>
                <p id="detail-distance-wrap" class="small mb-1" style="display:none"><strong>Distance from you:</strong> <span id="detail-distance"></span></p>
                <p id="detail-location-wrap" class="small mb-1"><strong>Location:</strong> <span id="detail-location"></span></p>
                <p id="detail-fees-wrap" class="small mb-1"><strong>Fees:</strong> <span id="detail-fees"></span></p>
                <p id="detail-facilities-wrap" class="small mb-1"><strong>Facilities:</strong> <span id="detail-facilities"></span></p>
                <p id="detail-cottage-wrap" class="small mb-1"><strong>Cottage:</strong> <span id="detail-cottage"></span></p>
                <div class="detail-weather mt-3 p-3 rounded-3" id="detail-weather-wrap">
                  <div class="small fw-bold text-secondary mb-2">Weather at this beach</div>
                  <div class="d-flex align-items-center gap-3">
                    <img id="detail-weather-icon" src="" alt="" class="d-none" style="width:48px;height:48px" />
                    <span id="detail-weather-emoji" class="detail-weather-emoji d-none">—</span>
                    <div>
                      <span id="detail-weather-temp" class="fw-bold">—</span>
                      <span id="detail-weather-desc" class="small text-muted ms-1">—</span>
                      <div id="detail-weather-meta" class="small text-muted mt-1">—</div>
                    </div>
                  </div>
                  <div id="detail-weather-loading" class="small text-muted mt-1">Loading weather…</div>
                  <div id="detail-weather-error" class="small text-danger mt-1 d-none">—</div>
                </div>
              </div>
            </div>
            <div id="detail-map-wrap" class="mt-4" style="display:none;">
              <div class="small fw-bold text-secondary mb-2">Location on map</div>
              <div id="detail-map-embed"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      window.authRequiredForLocate = {{ auth()->check() ? 'false' : 'true' }};
      window.loginUrl = @json(route('login', ['redirect' => url()->current()]));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/landing.js"></script>
  </body>
</html>
