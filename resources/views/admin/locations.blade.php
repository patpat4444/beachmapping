<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin — Locations · Dagat ta bAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/landing.css">
    <link rel="stylesheet" href="/admin.css">
  </head>
  <body class="admin-page">
    <header class="appbar d-flex align-items-center px-3 px-md-4">
      <a href="/landing" class="brand me-3">Dagat ta bAI</a>
      <span class="text-white opacity-75 me-2">—</span>
      <span class="text-white fw-normal">Admin · Locations</span>
      <div class="ms-auto">
        <a href="/landing" class="nav-link-admin">← Back to app</a>
      </div>
    </header>

    <main class="container-fluid py-4 px-3 px-md-4">
      <div class="row g-4">
        <div class="col-lg-4">
          @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
          @endif

          <div class="card mb-4">
            <div class="card-header-custom">Add location</div>
            <div class="card-body-custom">
              <form method="POST" action="/admin/locations" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input name="name" class="form-control" placeholder="e.g. White Beach" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="2" placeholder="Short description"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Address / Location</label>
                  <input name="address" class="form-control" placeholder="City, province or area" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Fees</label>
                  <input name="fees" class="form-control" placeholder="e.g. Entrance ₱50, parking ₱30" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Facilities</label>
                  <textarea name="facilities" class="form-control" rows="2" placeholder="e.g. Restrooms, showers, parking"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Cottage</label>
                  <input name="cottage" class="form-control" placeholder="e.g. Small ₱500, large ₱1500" />
                </div>
                <div class="row g-2">
                  <div class="col-6">
                    <label class="form-label">Latitude</label>
                    <input name="latitude" id="latitude" class="form-control" placeholder="e.g. 10.1234567" required />
                  </div>
                  <div class="col-6">
                    <label class="form-label">Longitude</label>
                    <input name="longitude" id="longitude" class="form-control" placeholder="e.g. 123.1234567" required />
                  </div>
                </div>
                <p class="coord-hint">Click on the map to set coordinates, or type them and use the button below.</p>
                <div class="mb-3">
                  <button id="place-from-inputs" type="button" class="btn btn-sm btn-outline-secondary">Place marker from coordinates</button>
                </div>
                <div class="mb-3">
                  <label class="form-label">Rating (0–5)</label>
                  <input name="rating" type="number" min="0" max="5" class="form-control" placeholder="Optional" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Image</label>
                  <input name="image_file" id="image_file" type="file" accept="image/*" class="form-control" />
                  <div class="mt-2" id="image-preview"></div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Google Maps Embed</label>
                  <textarea name="maps_embed_url" class="form-control" rows="3" placeholder="Paste the embed code from Google Maps here"></textarea>
                  <small class="text-muted d-block mt-1">Get the embed code by clicking "Share" on Google Maps, then copying the iframe code.</small>
                </div>
                <button type="submit" class="btn btn-primary">Save location</button>
              </form>
            </div>
          </div>

          <div class="card">
            <div class="card-header-custom">Existing locations</div>
            <div class="card-body-custom p-0">
              @if($locations->isEmpty())
                <div class="empty-locations">No locations yet. Add one above.</div>
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
                        <a href="/admin/locations/{{ $loc->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="/admin/locations/{{ $loc->id }}" class="d-inline" onsubmit="return confirm('Delete this location?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </div>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="page-title">Map — click to set new location</div>
          <div id="admin-map" style="height: calc(100vh - 140px); min-height: 400px;" class="rounded shadow-sm"></div>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
      window.existingLocations = {!! $locationsJson ?? '[]' !!};
    </script>
    <script src="/admin-locations.js"></script>
  </body>
</html>
