<!doctype html>
<html lang="en">
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
      <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        <div class="search-box">
          <i class="fa-solid fa-search search-icon"></i>
          <input type="text" id="beach-search" placeholder="Search beaches..." aria-label="Search beaches" />
        </div>
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
        <!-- Weather removed - now in beach details modal -->
      </aside>
    </main>

    <!-- AI Chat Widget -->
    <div class="ai-chat-widget" id="ai-chat-widget">
      <!-- Collapsed State - Circle Button -->
      <button class="ai-chat-circle" id="ai-chat-toggle" type="button" aria-label="Open AI Assistant">
        <i class="fa-solid fa-robot"></i>
        <span class="ai-chat-pulse"></span>
      </button>
      
      <!-- Expanded State - Chat Interface -->
      <div class="ai-chat-panel" id="ai-chat-panel">
        <div class="ai-chat-header">
          <div class="ai-header-info">
            <i class="fa-solid fa-robot ai-bot-icon"></i>
            <div>
              <h5 class="ai-chat-title">AI Assistant</h5>
              <span class="ai-status">Online</span>
            </div>
          </div>
          <button type="button" class="ai-chat-close" id="ai-chat-close" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="ai-chat-body">
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
        <div class="ai-chat-footer">
          <div class="ai-input-row">
            <input type="text" class="ai-input" id="ai-chat-input" placeholder="Ask about beaches..." aria-label="Ask about beaches" />
            <button type="button" class="ai-send" id="ai-chat-send" aria-label="Send">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
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
            <!-- Main Weather Metrics -->
            <div class="weather-metrics-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Feels Like</div>
                <div id="weather-feels-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Humidity</div>
                <div id="weather-humidity-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Wind</div>
                <div id="weather-wind-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">UV Index</div>
                <div id="weather-uv-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Pressure</div>
                <div id="weather-pressure-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Visibility</div>
                <div id="weather-visibility-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Clouds</div>
                <div id="weather-clouds-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
              <div class="metric-box" style="background: var(--glass-bg-2); padding: 1rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); text-transform: uppercase; letter-spacing: 0.05em;">Updated</div>
                <div id="weather-updated-more" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
              </div>
            </div>

            <!-- Marine Conditions Section -->
            <div id="marine-info-section" class="d-none mt-3 p-3 rounded-3" style="background: var(--glass-bg-2); border: 1px solid var(--glass-border);">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                  <i class="fa-solid fa-water" style="color: #7ecce0;"></i>
                  <span style="font-weight: 600; color: var(--page-text);">Marine Conditions</span>
                </div>
              </div>
              <div class="d-flex justify-content-between text-center">
                <div>
                  <div style="font-size: 0.75rem; color: var(--muted-text);">Wave Height</div>
                  <div id="marine-wave-height" style="font-size: 1.5rem; font-weight: 700; color: var(--page-text);">—</div>
                </div>
                <div>
                  <div style="font-size: 0.75rem; color: var(--muted-text);">Sea Temp</div>
                  <div id="marine-sea-temp" style="font-size: 1.5rem; font-weight: 700; color: var(--page-text);">—</div>
                </div>
                <div>
                  <div style="font-size: 0.75rem; color: var(--muted-text);">Wave Period</div>
                  <div id="marine-wave-period" style="font-size: 1.5rem; font-weight: 700; color: var(--page-text);">—</div>
                </div>
              </div>
            </div>

            <!-- Tide Information Section -->
            <div id="tide-info-section" class="d-none mt-3 p-3 rounded-3" style="background: var(--glass-bg-2); border: 1px solid var(--glass-border);">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                  <i class="fa-solid fa-water" style="color: #7ecce0;"></i>
                  <span style="font-weight: 600; color: var(--page-text);">Tide Information</span>
                </div>
                <button class="btn btn-sm" id="view-monthly-tides-btn" style="background: rgba(126,204,224,0.2); color: var(--page-text); border: 1px solid var(--glass-border); font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                  <i class="fa-solid fa-calendar me-1"></i>View Monthly
                </button>
              </div>
              <div class="d-flex justify-content-between text-center mb-3">
                <div>
                  <div style="font-size: 0.75rem; color: var(--muted-text);">Current</div>
                  <div id="tide-current-status" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text); text-transform: capitalize;">—</div>
                </div>
                <div>
                  <div style="font-size: 0.75rem; color: var(--muted-text);">Next High</div>
                  <div id="tide-next-high" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
                </div>
                <div>
                  <div style="font-size: 0.75rem; color: var(--muted-text);">Next Low</div>
                  <div id="tide-next-low" style="font-size: 1.25rem; font-weight: 700; color: var(--page-text);">—</div>
                </div>
              </div>
              <!-- Tide Graph -->
              <div class="tide-graph-container" style="background: var(--glass-bg); border-radius: 12px; padding: 1rem; border: 1px solid var(--glass-border);">
                <div style="font-size: 0.75rem; color: var(--muted-text); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">24-Hour Tide Pattern</div>
                <div class="tide-graph" style="position: relative; height: 60px; background: linear-gradient(to bottom, rgba(126,204,224,0.1), rgba(126,204,224,0.05)); border-radius: 8px; overflow: hidden;">
                  <div class="tide-curve" style="position: absolute; bottom: 0; left: 0; right: 0; height: 100%; background: linear-gradient(to top, #2a9db8, #7ecce0); opacity: 0.3; clip-path: polygon(0% 70%, 15% 30%, 25% 30%, 35% 70%, 50% 70%, 65% 20%, 75% 20%, 85% 70%, 100% 70%, 100% 100%, 0% 100%);"></div>
                  <div class="tide-current-marker" id="tide-marker" style="position: absolute; top: 50%; transform: translateY(-50%); width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 10; left: 35%;"></div>
                  <div class="tide-high-point" style="position: absolute; top: 10%; font-size: 0.625rem; color: var(--muted-text); font-weight: 600; left: 20%;">High</div>
                  <div class="tide-low-point" style="position: absolute; bottom: 10%; font-size: 0.625rem; color: var(--muted-text); font-weight: 600; left: 45%;">Low</div>
                  <div class="tide-high-point" style="position: absolute; top: 10%; font-size: 0.625rem; color: var(--muted-text); font-weight: 600; left: 70%;">High</div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.625rem; color: var(--muted-text);">
                  <span>12AM</span>
                  <span>6AM</span>
                  <span>12PM</span>
                  <span>6PM</span>
                  <span>12AM</span>
                </div>
              </div>
              <div id="tide-note" class="d-none mt-2" style="font-size: 0.75rem; color: var(--muted-text); font-style: italic;"></div>
            </div>

            <!-- Monthly Tides Modal -->
            <div class="modal fade" id="monthlyTidesModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                  <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
                    <h5 class="modal-title" style="color: var(--page-text);">
                      <i class="fa-solid fa-calendar-days me-2" style="color: #7ecce0;"></i>
                      Monthly Tide Calendar - Catmon, Cebu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                  </div>
                  <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="monthly-tides-content" style="color: var(--page-text);">
                      <!-- Monthly tides will be populated here -->
                    </div>
                  </div>
                </div>
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
                <div class="detail-weather mt-3 p-3 rounded-3" id="detail-weather-wrap" style="background: var(--glass-bg-2); border: 1px solid var(--glass-border);">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <i class="fa-solid fa-cloud-sun" style="color: #7ecce0;"></i>
                      <span style="font-weight: 600; color: var(--page-text);">Live Weather</span>
                      <span class="badge" style="background: #22c55e; font-size: 0.625rem;">LIVE</span>
                    </div>
                    <span id="detail-weather-location" style="font-size: 0.75rem; color: var(--muted-text);">—</span>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div id="detail-weather-emoji" style="font-size: 2rem;">
                      <i class="fa-solid fa-cloud-sun" style="color: #7ecce0;"></i>
                    </div>
                    <div>
                      <span id="detail-weather-temp" style="font-size: 1.5rem; font-weight: 700; color: var(--page-text);">—</span>
                      <span id="detail-weather-desc" style="font-size: 0.875rem; color: var(--muted-text);">—</span>
                    </div>
                  </div>
                  <!-- Weather Metrics Grid -->
                  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="text-align: center; padding: 0.5rem; background: var(--glass-bg); border-radius: 8px;">
                      <div style="font-size: 0.75rem; color: var(--muted-text);">Waves</div>
                      <div id="detail-weather-waves" style="font-size: 0.875rem; font-weight: 600; color: var(--page-text);">—</div>
                    </div>
                    <div style="text-align: center; padding: 0.5rem; background: var(--glass-bg); border-radius: 8px;">
                      <div style="font-size: 0.75rem; color: var(--muted-text);">Wind</div>
                      <div id="detail-weather-wind" style="font-size: 0.875rem; font-weight: 600; color: var(--page-text);">—</div>
                    </div>
                    <div style="text-align: center; padding: 0.5rem; background: var(--glass-bg); border-radius: 8px;">
                      <div style="font-size: 0.75rem; color: var(--muted-text);">Humidity</div>
                      <div id="detail-weather-humidity" style="font-size: 0.875rem; font-weight: 600; color: var(--page-text);">—</div>
                    </div>
                    <div style="text-align: center; padding: 0.5rem; background: var(--glass-bg); border-radius: 8px;">
                      <div style="font-size: 0.75rem; color: var(--muted-text);">UV</div>
                      <div id="detail-weather-uv" style="font-size: 0.875rem; font-weight: 600; color: var(--page-text);">—</div>
                    </div>
                  </div>
                  <!-- 5-Day Forecast -->
                  <div id="detail-forecast-wrap" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--glass-border);">
                    <div style="font-size: 0.75rem; color: var(--muted-text); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">5-Day Forecast</div>
                    <div class="detail-forecast-list" id="detail-forecast-list" style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                      <!-- Forecast items will be populated by JS -->
                    </div>
                  </div>
                  <!-- Marine & Tide Summary -->
                  <div id="detail-marine-tide" class="d-none" style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--glass-border);">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 0.75rem;">
                      <div style="padding: 0.5rem; background: var(--glass-bg); border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--muted-text);"><i class="fa-solid fa-water me-1" style="color: #7ecce0;"></i>Wave Height</div>
                        <div id="detail-marine-waves" style="font-size: 0.875rem; font-weight: 600; color: var(--page-text);">—</div>
                      </div>
                      <div style="padding: 0.5rem; background: var(--glass-bg); border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--muted-text);"><i class="fa-solid fa-arrow-up me-1" style="color: #22c55e;"></i>Next High Tide</div>
                        <div id="detail-tide-next-high" style="font-size: 0.875rem; font-weight: 600; color: var(--page-text);">—</div>
                      </div>
                    </div>
                    <!-- Tide Graph -->
                    <div class="tide-graph-container" style="background: var(--glass-bg); border-radius: 12px; padding: 1rem; border: 1px solid var(--glass-border); margin-bottom: 0.75rem;">
                      <div style="font-size: 0.75rem; color: var(--muted-text); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">24-Hour Tide Pattern</div>
                      <div class="tide-graph" style="position: relative; height: 60px; background: linear-gradient(to bottom, rgba(126,204,224,0.1), rgba(126,204,224,0.05)); border-radius: 8px; overflow: hidden;">
                        <div class="tide-curve" style="position: absolute; bottom: 0; left: 0; right: 0; height: 100%; background: linear-gradient(to top, #2a9db8, #7ecce0); opacity: 0.3; clip-path: polygon(0% 70%, 15% 30%, 25% 30%, 35% 70%, 50% 70%, 65% 20%, 75% 20%, 85% 70%, 100% 70%, 100% 100%, 0% 100%);"></div>
                        <div class="tide-current-marker" id="detail-tide-marker" style="position: absolute; top: 50%; transform: translateY(-50%); width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 10; left: 35%;"></div>
                        <div class="tide-high-point" style="position: absolute; top: 10%; font-size: 0.625rem; color: var(--muted-text); font-weight: 600; left: 20%;">High</div>
                        <div class="tide-low-point" style="position: absolute; bottom: 10%; font-size: 0.625rem; color: var(--muted-text); font-weight: 600; left: 45%;">Low</div>
                        <div class="tide-high-point" style="position: absolute; top: 10%; font-size: 0.625rem; color: var(--muted-text); font-weight: 600; left: 70%;">High</div>
                      </div>
                      <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.625rem; color: var(--muted-text);">
                        <span>12AM</span>
                        <span>6AM</span>
                        <span>12PM</span>
                        <span>6PM</span>
                        <span>12AM</span>
                      </div>
                    </div>
                    <!-- View Monthly Button -->
                    <button class="btn btn-sm w-100" id="view-monthly-tides-detail-btn" style="background: rgba(126,204,224,0.2); color: var(--page-text); border: 1px solid var(--glass-border); font-size: 0.75rem; padding: 0.5rem;">
                      <i class="fa-solid fa-calendar me-1"></i>View Monthly Tide Calendar
                    </button>
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
    <script src="/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/landing.js"></script>
  </body>
</html>
