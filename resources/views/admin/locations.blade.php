<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin — Locations · Dagat Ta bAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/css/admin-dashboard.css" />
  </head>
  <body class="admin-page">
    <header class="appbar">
      <a href="/landing" class="brand">Dagat Ta <em>bAI</em></a>
      <div class="appbar-actions">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
          <i class="fa-solid fa-sun" id="themeIcon"></i>
        </button>
      </div>
    </header>

    <main class="admin-main">
      <div class="admin-grid">
        <!-- Left Sidebar -->
        <div class="sidebar">
          @if(session('success'))
            <div class="alert alert-success mb-3">
              <i class="fa-solid fa-check-circle"></i>
              {{ session('success') }}
            </div>
          @endif

          <!-- Add Location Card -->
          <div class="admin-card">
            <div class="admin-card-header">
              <i class="fa-solid fa-plus-circle"></i>
              Add Location
            </div>
            <div class="admin-card-body">
              <form method="POST" action="/admin/locations" enctype="multipart/form-data" class="add-location-form">
                @csrf
                <div class="form-field">
                  <label>Beach Name</label>
                  <input name="name" class="form-control" required />
                  <span class="field-hint">e.g. White Beach Resort</span>
                </div>

                <div class="form-field">
                  <label>Description</label>
                  <textarea name="description" class="form-control" rows="2"></textarea>
                  <span class="field-hint">Short description of the beach destination</span>
                </div>

                <div class="form-field">
                  <label>Address / Location</label>
                  <input name="address" class="form-control" />
                  <span class="field-hint">City, province or area</span>
                </div>

                <div class="form-row-2">
                  <div class="form-field">
                    <label>Entrance Fees</label>
                    <input name="fees" class="form-control" />
                    <span class="field-hint">e.g. P50 entrance</span>
                  </div>
                  <div class="form-field">
                    <label>Cottage Rates</label>
                    <input name="cottage" class="form-control" />
                    <span class="field-hint">e.g. Small P500</span>
                  </div>
                </div>

                <div class="form-field">
                  <label>Facilities</label>
                  <input name="facilities" class="form-control" />
                  <span class="field-hint">e.g. Restrooms, showers, parking</span>
                </div>

                <div class="form-row-2">
                  <div class="form-field">
                    <label>Latitude</label>
                    <input name="latitude" id="latitude" class="form-control" required />
                    <span class="field-hint">e.g. 10.1234567</span>
                  </div>
                  <div class="form-field">
                    <label>Longitude</label>
                    <input name="longitude" id="longitude" class="form-control" required />
                    <span class="field-hint">e.g. 123.1234567</span>
                  </div>
                </div>

                <p class="map-hint-text">
                  <i class="fa-solid fa-location-dot"></i>
                  Click on the map to set coordinates, or type them manually
                </p>

                <div>
                  <button id="place-from-inputs" type="button" class="btn-place-marker">
                    <i class="fa-solid fa-map-pin"></i>
                    Place marker from coordinates
                  </button>
                </div>

                <div class="form-row-2">
                  <div class="form-field">
                    <label>Rating (0-5)</label>
                    <input name="rating" type="number" min="0" max="5" class="form-control" />
                    <span class="field-hint">Optional</span>
                  </div>
                  <div class="form-field">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="Open" selected>Open</option>
                      <option value="Closed">Closed</option>
                    </select>
                  </div>
                </div>

                <div class="form-field">
                  <label>Beach Photo</label>
                  <input name="image_file" id="image_file" type="file" accept="image/*" class="form-control" />
                  <div class="mt-2" id="image-preview"></div>
                </div>

                <div class="form-field">
                  <label>Google Maps Embed</label>
                  <textarea name="maps_embed_url" class="form-control" rows="3"></textarea>
                  <span class="field-hint">Paste the embed iframe code from Google Maps here</span>
                </div>

                <button type="submit" class="btn-save">
                  <i class="fa-solid fa-floppy-disk"></i>
                  Save Location
                </button>
              </form>
            </div>
          </div>

          <!-- Existing Locations Card -->
          <div class="admin-card">
            <div class="admin-card-header">
              <i class="fa-solid fa-list"></i>
              Existing Locations ({{ $locations->count() }})
            </div>
            <div class="admin-card-body" style="padding: 0;">
              @if($locations->isEmpty())
                <div class="empty-locations">
                  <i class="fa-solid fa-map-location-dot"></i>
                  <p>No locations yet. Add one above.</p>
                </div>
              @else
                <ul class="location-list">
                  @foreach($locations as $loc)
                    <li class="location-item">
                      @if($loc->image)
                        <img src="{{ str_starts_with($loc->image, 'storage/') ? asset($loc->image) : $loc->image }}" alt="" class="location-item-thumb" />
                      @else
                        <div class="location-item-thumb-none">No image</div>
                      @endif
                      <div class="location-item-body">
                        <div class="location-item-name">{{ $loc->name }}</div>
                        <div class="location-item-meta">{{ number_format($loc->latitude, 5) }}, {{ number_format($loc->longitude, 5) }}</div>
                        @if($loc->address)
                          <div class="location-item-meta">{{ Str::limit($loc->address, 40) }}</div>
                        @endif
                      </div>
                      <div class="location-item-actions">
                        <a href="/admin/locations/{{ $loc->id }}/edit" class="btn btn-sm btn-secondary">
                          <i class="fa-solid fa-pen"></i>
                        </a>
                        <form method="POST" action="/admin/locations/{{ $loc->id }}" class="d-inline" onsubmit="return confirm('Delete this location?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>

        <!-- Right Map Section -->
        <div class="map-section">
          <div class="map-header">
            <div class="map-title">
              <i class="fa-solid fa-map"></i>
              Map View
            </div>
            <div class="map-hint">
              <i class="fa-solid fa-hand-pointer"></i>
              Click to set new location
            </div>
          </div>
          <div id="admin-map" class="map-container"></div>
        </div>
      </div>
    </main>

    <script>
      // Theme toggle
      const themeToggle = document.getElementById('themeToggle');
      const themeIcon = document.getElementById('themeIcon');
      const html = document.documentElement;
      
      const savedTheme = localStorage.getItem('admin-dashboard-theme') || 'dark';
      html.setAttribute('data-theme', savedTheme);
      updateThemeIcon(savedTheme);
      
      themeToggle.addEventListener('click', () => {
        const current = html.getAttribute('data-theme') || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('admin-dashboard-theme', next);
        updateThemeIcon(next);
      });
      
      function updateThemeIcon(theme) {
        themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
      }
      
      window.existingLocations = {!! $locationsJson ?? '[]' !!};
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/js/admin-locations.js"></script>
  </body>
</html>
