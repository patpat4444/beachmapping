<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dagat ta bAI — Explore beaches & places</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/landing.css">
  </head>
  <body>
    <header class="landing-appbar d-flex align-items-center">
      <a href="/landing" class="brand">Dagat ta bAI</a>
      <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        <span class="text-white opacity-90 me-2 d-none d-md-inline" style="font-size:0.9rem">Explore places</span>
        <div class="search-box">
          <input type="text" placeholder="Search place or location…" aria-label="Search" />
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
        <div class="landing-filters">
          <div>
            <div class="filter-label">Type</div>
            <select class="form-select" aria-label="Filter by type">
              <option>All</option>
              <option>Beaches</option>
            </select>
          </div>
          <div>
            <div class="filter-label">Rating</div>
            <select class="form-select" aria-label="Filter by rating">
              <option>Any</option>
              <option>4+ stars</option>
            </select>
          </div>
          <button type="button" id="locate-me" class="btn btn-locate">📍 Locate me</button>
        </div>
        <div id="place-cards" class="landing-cards-wrap cards overflow-auto flex-grow-1">
          <!-- Cards populated by JS -->
        </div>
      </div>

      <div class="landing-map-wrap">
        <div id="map" class="h-100 w-100 rounded shadow-sm"></div>
      </div>
    </main>

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
