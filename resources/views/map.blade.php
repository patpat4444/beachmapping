<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Explore beaches & places</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="/css/map.css">
    <link rel="icon" type="image/png" href="/storage/locations/logo.png">
  </head>
  <body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Header with User Menu -->
    <div class="map-header">
      <button class="user-menu-btn" onclick="toggleUserMenu()">
        <i class="fa-solid fa-user"></i>
      </button>
    </div>

    <main class="landing-main">
      <div class="landing-sidebar collapsed" id="landing-sidebar">
        <!-- Close button inside sidebar -->
        <button class="sidebar-close-btn" id="sidebar-close-btn" aria-label="Close sidebar">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
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
        <div class="results-count">Showing 5 beach destinations</div>
        <div id="place-cards" class="landing-cards-wrap">
          <!-- Beach cards will be populated by JS -->
        </div>
      </div>

      <div class="landing-map-wrap" id="map-section">
        <div id="map" class="h-100 w-100 shadow-sm"></div>
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

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 550px; margin: 1rem auto;">
        <div class="modal-content" style="background: #1a2a3a; border-radius: 20px; border: none; overflow: hidden; color: #fff;">
          <button type="button" class="btn-close btn-close-white detail-close" data-bs-dismiss="modal" aria-label="Close"></button>
          
          <!-- Header Image Section -->
          <div class="detail-header-image">
            <img id="detail-image" src="" alt="" />
            <div class="detail-header-overlay">
              <div class="detail-title-section">
                <h2 id="detail-title" class="detail-title">Ranola Beach</h2>
                <div class="detail-stars">
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-regular fa-star"></i>
                  <span class="star-count">4.2 (124)</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-body detail-body">
            <!-- Address -->
            <div class="detail-address">
              <i class="fa-solid fa-location-dot"></i>
              <span id="detail-location">Sta. Maria, Sual, Quezon, PH</span>
            </div>
            
            <!-- Hours and Fee -->
            <div class="detail-meta-row">
              <div class="detail-meta-item">
                <span class="meta-label">Open hours:</span>
                <span class="meta-value">6:00 AM - 10:00 PM</span>
              </div>
              <div class="detail-meta-item">
                <span class="meta-label">Entrance fee:</span>
                <span class="meta-value" id="detail-fees">₱50 / person</span>
              </div>
            </div>
            
            <!-- Activity Tags -->
            <div class="detail-tags">
              <span class="detail-tag">Swimming</span>
              <span class="detail-tag">Public</span>
              <span class="detail-tag">Picnic</span>
            </div>
            
            <!-- Weather Section -->
            <div class="detail-weather-section">
              <div class="weather-main">
                <div class="weather-temp-large">
                  <span id="detail-weather-temp">28</span><span class="temp-unit">°C</span>
                </div>
                <div class="weather-desc" id="detail-weather-desc">Scattered clouds</div>
                <div class="weather-icon-large" id="detail-weather-emoji">
                  <i class="fa-solid fa-cloud-sun"></i>
                </div>
              </div>
              
              <!-- Weather Metrics -->
              <div class="weather-metrics-row">
                <div class="weather-metric">
                  <div class="metric-icon"><i class="fa-solid fa-water"></i></div>
                  <div class="metric-value" id="detail-weather-waves">0.3m</div>
                  <div class="metric-label">Waves</div>
                </div>
                <div class="weather-metric">
                  <div class="metric-icon"><i class="fa-solid fa-wind"></i></div>
                  <div class="metric-value" id="detail-weather-wind">4.3</div>
                  <div class="metric-label">Wind</div>
                </div>
                <div class="weather-metric">
                  <div class="metric-icon"><i class="fa-solid fa-droplet"></i></div>
                  <div class="metric-value" id="detail-weather-humidity">68%</div>
                  <div class="metric-label">Humidity</div>
                </div>
                <div class="weather-metric">
                  <div class="metric-icon"><i class="fa-solid fa-sun"></i></div>
                  <div class="metric-value" id="detail-weather-uv">—</div>
                  <div class="metric-label">UV</div>
                </div>
              </div>
            </div>
            
            <!-- 4-Day Forecast -->
            <div class="detail-forecast-section">
              <div class="forecast-title">4-DAY FORECAST</div>
              <div class="forecast-days" id="detail-forecast-list">
                <div class="forecast-day">
                  <div class="day-label">TODAY</div>
                  <div class="day-icon"><i class="fa-solid fa-cloud-sun"></i></div>
                  <div class="day-temp">28°</div>
                </div>
                <div class="forecast-day">
                  <div class="day-label">SAT</div>
                  <div class="day-icon"><i class="fa-solid fa-sun"></i></div>
                  <div class="day-temp">27°</div>
                </div>
                <div class="forecast-day">
                  <div class="day-label">SUN</div>
                  <div class="day-icon"><i class="fa-solid fa-cloud-rain"></i></div>
                  <div class="day-temp">30°</div>
                </div>
                <div class="forecast-day">
                  <div class="day-label">MON</div>
                  <div class="day-icon"><i class="fa-solid fa-cloud"></i></div>
                  <div class="day-temp">30°</div>
                </div>
              </div>
            </div>
            
            <!-- Tide Section -->
            <div class="detail-tide-section">
              <div class="tide-title">TIDE</div>
              <div class="tide-info-row">
                <div class="tide-item">
                  <div class="tide-label">LOW TIDE</div>
                  <div class="tide-value" id="detail-tide-next-low">0.2 m</div>
                  <div class="tide-time">11:20 AM</div>
                </div>
                <div class="tide-item">
                  <div class="tide-label">HIGH TIDE</div>
                  <div class="tide-value" id="detail-tide-next-high">1.1 m</div>
                  <div class="tide-time">06:50 PM</div>
                </div>
              </div>
              
              <!-- Tide Wave Graph -->
              <div class="tide-wave-graph">
                <svg viewBox="0 0 200 40" preserveAspectRatio="none">
                  <path d="M0,30 Q25,5 50,30 T100,30 T150,30 T200,30" fill="rgba(42,157,184,0.3)" stroke="#2a9db8" stroke-width="2"/>
                </svg>
                <div class="wave-marker" style="left: 15%;"></div>
              </div>
            </div>
            
            <!-- Panorama Section -->
            <div id="panorama-section" style="display: none; margin-bottom: 1rem;">
              <div style="font-size: 0.6875rem; font-weight: 700; color: #6a7a8a; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem;">360° PANORAMA VIEW</div>
              <div id="panorama-container" style="width: 100%; height: 400px; background: #0a1520; border-radius: 16px; border: 1px solid rgba(42, 157, 184, 0.3); overflow: hidden; position: relative;">
                <iframe id="tour-embeded" name="Tesing2" src="https://tour.panoee.net/iframe/69bbed1a5ab829b008b63cbb" frameBorder="0" width="100%" height="100%" scrolling="no" allowvr="yes" allow="vr; xr; accelerometer; gyroscope; autoplay;" allowFullScreen="false" webkitallowfullscreen="false" mozallowfullscreen="false" loading="lazy" style="border-radius: 16px;"></iframe>
              </div>
            </div>
            
            <script>
              var pano_iframe_name = "tour-embeded";
              window.addEventListener("devicemotion", function(e){ 
                var iframe = document.getElementById(pano_iframe_name); 
                if (iframe) iframe.contentWindow.postMessage({ 
                  type:"devicemotion", 
                  deviceMotionEvent:{ 
                    acceleration:{ x:e.acceleration.x, y:e.acceleration.y, z:e.acceleration.z }, 
                    accelerationIncludingGravity:{ x:e.accelerationIncludingGravity.x, y:e.accelerationIncludingGravity.y, z:e.accelerationIncludingGravity.z }, 
                    rotationRate:{ alpha:e.rotationRate.alpha, beta:e.rotationRate.beta, gamma:e.rotationRate.gamma }, 
                    interval:e.interval, 
                    timeStamp:e.timeStamp 
                  } 
                }, "*"); 
              });
            </script>
            
            <!-- Explore Section -->
            <div class="detail-explore-section">
              <div class="explore-title">EXPLORE</div>
              <div class="explore-buttons">
                <button class="explore-btn" id="btn-360-view" onclick="var pano=document.getElementById('panorama-section');if(pano.style.display==='none'){pano.style.display='block';this.querySelector('.explore-label').textContent='Close 360°';}else{pano.style.display='none';this.querySelector('.explore-label').textContent='360° View';}">
                  <div class="explore-icon"><i class="fa-solid fa-vr-cardboard"></i></div>
                  <div class="explore-label">360° View</div>
                </button>
                <button class="explore-btn" id="btn-map-view" onclick="var data=window.currentBeachData;var modalEl=document.getElementById('detailModal');var bsModal=bootstrap.Modal.getInstance(modalEl);if(bsModal)bsModal.hide();if(data&&data.lat&&data.lng){setTimeout(function(){map.setView([data.lat,data.lng],16);},300);}else{setTimeout(function(){map.setView([10.5,123.9],15);},300);}">
                  <div class="explore-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                  <div class="explore-label">Map</div>
                </button>
              </div>
            </div>
          </div>
          
          <!-- Footer Buttons -->
          <div class="detail-footer">
            <button class="detail-btn btn-share" onclick="if(navigator.share){navigator.share({title:document.getElementById('detail-title').textContent,text:'Check out this beach!',url:window.location.href}).catch(function(){});}else{alert('Share: ' + document.getElementById('detail-title').textContent + ' - Copy this link: ' + window.location.href);}">
              <i class="fa-solid fa-share-nodes"></i>
              Share
            </button>
            <button class="detail-btn btn-save" onclick="alert('Save location: Feature coming soon!')">
              <i class="fa-solid fa-bookmark"></i>
              Save location
            </button>
          </div>
          
          <!-- Hidden elements for JS compatibility -->
          <div style="display: none;">
            <div id="detail-rating"></div>
            <p id="detail-description"></p>
            <p id="detail-distance-wrap" style="display:none"><span id="detail-distance"></span></p>
            <p id="detail-facilities-wrap"><span id="detail-facilities"></span></p>
            <p id="detail-cottage-wrap"><span id="detail-cottage"></span></p>
            <div id="detail-weather-location"></div>
            <div id="detail-weather-loading">Loading weather…</div>
            <div id="detail-weather-error" class="d-none">—</div>
            <div id="detail-marine-tide" class="d-none">
              <div id="detail-marine-waves"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      window.authRequiredForLocate = {{ auth()->check() ? 'false' : 'true' }};
      window.loginUrl = @json(route('login', ['redirect' => url()->current()]));
      
      // Test modal function for debugging
      window.sendAiQuestion = function(question) {
        aiInput.value = question;
        console.log('AI Question:', question);
      };
      
      // Sidebar Toggle Functionality
      var sidebarToggle = document.getElementById('sidebar-toggle');
      var sidebarCloseBtn = document.getElementById('sidebar-close-btn');
      var sidebar = document.getElementById('landing-sidebar');
      var mapWrap = document.getElementById('map-section');
      var sidebarOpen = false;
      
      function toggleSidebar() {
        sidebarOpen = !sidebarOpen;
        if (sidebarOpen) {
          sidebar.classList.remove('collapsed');
          mapWrap.classList.add('sidebar-visible');
          sidebarToggle.classList.add('sidebar-open');
          sidebarToggle.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        } else {
          sidebar.classList.add('collapsed');
          mapWrap.classList.remove('sidebar-visible');
          sidebarToggle.classList.remove('sidebar-open');
          sidebarToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }
      }
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
      }
      
      if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', toggleSidebar);
      }
      
      // Auto-test modal after 3 seconds (for debugging)
      setTimeout(function() {
        console.log('Modal test ready. Run window.testModal() in console to test.');
      }, 3000);
    </script>
    <script src="/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/js/landing.js"></script>
  </body>
</html>
