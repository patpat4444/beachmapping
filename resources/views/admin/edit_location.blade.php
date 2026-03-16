<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit {{ $location->name }} · Admin · Dagat ta bAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/landing.css">
    <link rel="stylesheet" href="/admin.css">
  </head>
  <body class="admin-page">
    <header class="appbar d-flex align-items-center px-3 px-md-4">
      <a href="/landing" class="brand me-3">Dagat ta bAI</a>
      <span class="text-white opacity-75 me-2">—</span>
      <span class="text-white fw-normal">Edit location</span>
      <div class="ms-auto">
        <a href="/admin/locations" class="nav-link-admin">← Back to locations</a>
      </div>
    </header>

    <main class="container-fluid py-4 px-3 px-md-4">
      <div class="row g-4">
        <div class="col-lg-5">
          @if($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="card edit-form-card">
            <div class="card-header-custom">Edit: {{ $location->name }}</div>
            <div class="card-body-custom">
              <form method="POST" action="/admin/locations/{{ $location->id }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" value="{{ old('name', $location->name) }}" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3">{{ old('description', $location->description) }}</textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Address / Location</label>
                  <input name="address" value="{{ old('address', $location->address) }}" class="form-control" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Fees</label>
                  <input name="fees" value="{{ old('fees', $location->fees) }}" class="form-control" placeholder="e.g. Entrance ₱50" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Facilities</label>
                  <textarea name="facilities" class="form-control" rows="2" placeholder="e.g. Restrooms, showers">{{ old('facilities', $location->facilities) }}</textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Cottage</label>
                  <input name="cottage" value="{{ old('cottage', $location->cottage) }}" class="form-control" placeholder="e.g. Small ₱500" />
                </div>
                <div class="row g-2">
                  <div class="col-6">
                    <label class="form-label">Latitude</label>
                    <input name="latitude" value="{{ old('latitude', $location->latitude) }}" class="form-control" required />
                  </div>
                  <div class="col-6">
                    <label class="form-label">Longitude</label>
                    <input name="longitude" value="{{ old('longitude', $location->longitude) }}" class="form-control" required />
                  </div>
                </div>
                <p class="form-helper">Drag the marker on the map to update coordinates.</p>
                <div class="mb-3">
                  <label class="form-label">Rating (0–5)</label>
                  <input name="rating" type="number" min="0" max="5" value="{{ old('rating', $location->rating) }}" class="form-control" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Replace image (optional)</label>
                  <input name="image_file" type="file" accept="image/*" class="form-control" />
                  @if($location->image)
                    <div class="image-preview-wrap mt-2">
                      <span class="text-muted small">Current:</span>
                      <img src="{{ $location->image && str_starts_with($location->image,'storage/') ? asset($location->image) : $location->image }}" alt="" />
                    </div>
                  @endif
                </div>
                <div class="mb-3">
                  <label class="form-label">Google Maps Embed (optional)</label>
                  <textarea name="maps_embed_url" class="form-control" rows="3" placeholder="Paste the embed code from Google Maps here">{{ old('maps_embed_url', $location->maps_embed_url) }}</textarea>
                  <small class="text-muted d-block mt-1">Get the embed code by clicking "Share" on Google Maps, then copying the iframe code.</small>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Update location</button>
                  <a href="/admin/locations" class="btn btn-outline-secondary">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="page-title">Map — drag marker to change position</div>
          <div id="edit-map" style="height: 500px;" class="rounded shadow-sm"></div>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
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
