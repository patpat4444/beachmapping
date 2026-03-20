<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit {{ $location->name }} · Admin · Dagat Ta bAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/css/admin-dashboard.css" />
  </head>
  <body class="admin-page">
    <header class="appbar">
      <a href="/landing" class="brand">Dagat Ta <em>bAI</em></a>
      <span class="appbar-divider">—</span>
      <span class="appbar-title">Edit Location</span>
      <div class="appbar-actions">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
          <i class="fa-solid fa-sun" id="themeIcon"></i>
        </button>
        <a href="/admin/locations" class="nav-link-admin">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to locations</span>
        </a>
      </div>
    </header>

    <main class="admin-main">
      <div class="admin-grid">
        <!-- Left Sidebar -->
        <div class="sidebar">
          @if($errors->any())
            <div class="alert alert-danger mb-3">
              <i class="fa-solid fa-exclamation-circle"></i>
              <ul class="mb-0" style="margin-top: 0.5rem; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="admin-card">
            <div class="admin-card-header">
              <i class="fa-solid fa-pen-to-square"></i>
              Edit: {{ $location->name }}
            </div>
            <div class="admin-card-body">
              <form method="POST" action="/admin/locations/{{ $location->id }}" enctype="multipart/form-data" class="form-grid">
                @csrf
                @method('PUT')
                <div class="form-row">
                  <label>Name</label>
                  <input name="name" value="{{ old('name', $location->name) }}" class="form-control" required />
                </div>

                <div class="form-row-full">
                  <label>Description</label>
                  <textarea name="description" class="form-control" rows="2">{{ old('description', $location->description) }}</textarea>
                </div>

                <div class="form-row">
                  <label>Address</label>
                  <input name="address" value="{{ old('address', $location->address) }}" class="form-control" placeholder="City, province or area" />
                </div>

                <div class="form-row">
                  <label>Fees</label>
                  <input name="fees" value="{{ old('fees', $location->fees) }}" class="form-control" placeholder="e.g. Entrance ₱50" />
                </div>

                <div class="form-row">
                  <label>Facilities</label>
                  <input name="facilities" value="{{ old('facilities', $location->facilities) }}" class="form-control" placeholder="e.g. Restrooms, showers" />
                </div>

                <div class="form-row">
                  <label>Cottage</label>
                  <input name="cottage" value="{{ old('cottage', $location->cottage) }}" class="form-control" placeholder="e.g. Small ₱500" />
                </div>

                <div class="form-row">
                  <label>Latitude</label>
                  <input name="latitude" value="{{ old('latitude', $location->latitude) }}" class="form-control" required />
                </div>

                <div class="form-row">
                  <label>Longitude</label>
                  <input name="longitude" value="{{ old('longitude', $location->longitude) }}" class="form-control" required />
                </div>

                <p class="form-helper" style="margin-left: 116px;">
                  <i class="fa-solid fa-hand-pointer"></i>
                  Drag the marker on the map to update coordinates
                </p>

                <div class="form-row">
                  <label>Rating</label>
                  <input name="rating" type="number" min="0" max="5" value="{{ old('rating', $location->rating) }}" class="form-control" placeholder="0-5" />
                </div>

                <div class="form-row">
                  <label>Status</label>
                  <select name="status" class="form-control status-select">
                    <option value="Open" {{ old('status', $location->status) == 'Open' ? 'selected' : '' }}>Open</option>
                    <option value="Closed" {{ old('status', $location->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                  </select>
                </div>

                <div class="form-row-full">
                  <label>Replace Image</label>
                  <input name="image_file" type="file" accept="image/*" class="form-control" />
                  @if($location->image)
                    <div class="image-preview-wrap mt-2">
                      <img src="{{ $location->image && str_starts_with($location->image,'storage/') ? asset($location->image) : $location->image }}" alt="" />
                      <span class="text-muted small">Current image</span>
                    </div>
                  @endif
                </div>

                <div class="form-row-full">
                  <label>Google Maps Embed</label>
                  <textarea name="maps_embed_url" class="form-control" rows="3" placeholder="Paste the embed code from Google Maps here">{{ old('maps_embed_url', $location->maps_embed_url) }}</textarea>
                </div>

                <div class="form-row" style="margin-top: 0.5rem;">
                  <label></label>
                  <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-save"></i>
                    Update Location
                  </button>
                </div>
                <div class="form-row">
                  <label></label>
                  <a href="/admin/locations" class="btn btn-secondary" style="flex: 1;">
                    Cancel
                  </a>
                </div>
              </form>
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
              <i class="fa-solid fa-arrows-up-down-left-right"></i>
              Drag marker to change position
            </div>
          </div>
          <div id="edit-map" class="map-container"></div>
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
      
      // Map initialization
      document.addEventListener('DOMContentLoaded', function(){
        var lat = parseFloat('{{ $location->latitude }}');
        var lng = parseFloat('{{ $location->longitude }}');
        var map = L.map('edit-map').setView([lat || 12.8797, lng || 121.7740], lat && lng ? 13 : 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19, attribution:'&copy; OpenStreetMap contributors' }).addTo(map);
        var marker = L.marker([lat, lng], {draggable:true}).addTo(map);
        marker.on('dragend', function(e){
          var p = e.target.getLatLng();
          document.querySelector('[name="latitude"]').value = p.lat.toFixed(7);
          document.querySelector('[name="longitude"]').value = p.lng.toFixed(7);
        });
      });
    </script>
  </body>
</html>
