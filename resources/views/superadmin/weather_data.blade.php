<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Weather & AI Data · Dagat Ta bAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="/css/superadmin-dashboard.css" />
  </head>
  <body class="superadmin-page">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon"><i class="fa-solid fa-water"></i></div>
        <div class="brand-text">Dagat Ta <em>bAI</em></div>
      </div>
      <nav class="nav">
        <div class="nav-section">Overview</div>
        <a href="/superadmin/dashboard" class="nav-item">
          <i class="fa-solid fa-chart-line"></i>
          <span>Dashboard</span>
        </a>
        
        <div class="nav-section">Management</div>
        <a href="/superadmin/admins" class="nav-item">
          <i class="fa-solid fa-users-gear"></i>
          <span>Beach Owners</span>
        </a>
        <a href="/superadmin/users" class="nav-item">
          <i class="fa-solid fa-users"></i>
          <span>Manage User Accounts</span>
        </a>
        
        <div class="nav-section">System & Data</div>
        <a href="/superadmin/applications" class="nav-item">
          <i class="fa-solid fa-clipboard-list"></i>
          <span>Applications</span>
        </a>
        <a href="/superadmin/activity-logs" class="nav-item">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <span>Monitor System Activity</span>
        </a>
        <a href="/superadmin/weather-data" class="nav-item active">
          <i class="fa-solid fa-cloud-sun"></i>
          <span>Update Weather & AI Data</span>
        </a>
      </nav>
      
      <div class="user-menu">
        <div class="user-info">
          <div class="user-avatar"><i class="fa-solid fa-user-shield"></i></div>
          <div class="user-details">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">Super Admin</div>
          </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="logout-form" onsubmit="event.preventDefault(); document.getElementById('superadmin-logout').submit();">
          @csrf
          <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
        </form>
        <form id="superadmin-logout" method="POST" action="{{ route('logout') }}" style="display: none;">
          @csrf
          <input type="hidden" name="redirect" value="/superadmin/login">
        </form>
      </div>
    </aside>

    <main class="main-content">
      <header class="header">
        <h1 class="page-title">Update Weather & AI Data</h1>
        <div class="header-actions">
          <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
            <i class="fa-solid fa-sun" id="themeIcon"></i>
          </button>
        </div>
      </header>

      @if(session('success'))
        <div class="alert alert-success">
          <i class="fa-solid fa-check-circle"></i>
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-error">
          <i class="fa-solid fa-circle-exclamation"></i>
          {{ session('error') }}
        </div>
      @endif

      <div class="content-grid">
        <!-- Weather API Status -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-cloud"></i> Weather API</h3>
          </div>
          <div class="card-body">
            <div class="info-row">
              <span class="info-label">Status</span>
              <span class="status-badge active"><i class="fa-solid fa-check"></i> Active</span>
            </div>
            <div class="info-row">
              <span class="info-label">Provider</span>
              <span class="info-value">OpenWeatherMap</span>
            </div>
            <div class="info-row">
              <span class="info-label">Last Update</span>
              <span class="info-value">Just now</span>
            </div>
            <button class="btn btn-primary" style="margin-top: 15px; width: 100%;">
              <i class="fa-solid fa-rotate"></i> Refresh All Weather Data
            </button>
          </div>
        </div>

        <!-- AI Data Status -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa-solid fa-robot"></i> AI Data</h3>
          </div>
          <div class="card-body">
            <div class="info-row">
              <span class="info-label">Status</span>
              <span class="status-badge active"><i class="fa-solid fa-check"></i> Active</span>
            </div>
            <div class="info-row">
              <span class="info-label">Model</span>
              <span class="info-value">Dagat Ta bAI Assistant</span>
            </div>
            <div class="info-row">
              <span class="info-label">Beach Data</span>
              <span class="info-value">{{ $locations->count() }} locations</span>
            </div>
            <button class="btn btn-primary" style="margin-top: 15px; width: 100%;">
              <i class="fa-solid fa-brain"></i> Update AI Training Data
            </button>
          </div>
        </div>

        <!-- Beach Locations Weather -->
        <div class="card full-width">
          <div class="card-header">
            <h3><i class="fa-solid fa-map-location-dot"></i> Beach Destinations Weather Data ({{ $locations->count() }})</h3>
          </div>
          <div class="card-body">
            @if($locations->count() > 0)
              <div class="table-responsive">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Location</th>
                      <th>Coordinates</th>
                      <th>Current Weather</th>
                      <th>Last Updated</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($locations as $location)
                      <tr>
                        <td>
                          <div class="user-cell">
                            <div class="user-avatar-sm"><i class="fa-solid fa-location-dot"></i></div>
                            <span>{{ $location->name }}</span>
                          </div>
                        </td>
                        <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                        <td>
                          <span class="badge badge-info">
                            <i class="fa-solid fa-temperature-half"></i> --°C
                          </span>
                        </td>
                        <td>--</td>
                        <td>
                          <div class="action-buttons">
                            <button class="btn btn-sm btn-secondary" title="View Weather">
                              <i class="fa-solid fa-cloud-sun"></i>
                            </button>
                            <button class="btn btn-sm btn-info" title="Update Data">
                              <i class="fa-solid fa-rotate"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="empty-state">
                <i class="fa-solid fa-map-location-dot"></i>
                <p>No beach destinations added yet.</p>
                <a href="/admin/locations" class="btn btn-primary" style="margin-top: 15px;">
                  <i class="fa-solid fa-plus"></i> Add Locations
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </main>

    <script>
      const themeToggle = document.getElementById('themeToggle');
      const themeIcon = document.getElementById('themeIcon');
      const html = document.documentElement;
      
      const savedTheme = localStorage.getItem('superadmin-theme') || 'dark';
      html.setAttribute('data-theme', savedTheme);
      updateThemeIcon(savedTheme);
      
      themeToggle.addEventListener('click', () => {
        const current = html.getAttribute('data-theme') || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('superadmin-theme', next);
        updateThemeIcon(next);
      });
      
      function updateThemeIcon(theme) {
        themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
      }
    </script>
  </body>
</html>
