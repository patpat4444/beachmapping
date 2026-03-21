<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $admin->name }} · Beach Owner Details · Dagat Ta bAI</title>
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
        <a href="/superadmin/admins" class="nav-item active">
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
        <a href="/superadmin/weather-data" class="nav-item">
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
        <div class="header-left">
          <a href="{{ route('superadmin.admins') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Beach Owners</a>
          <h1 class="page-title">{{ $admin->name }}</h1>
        </div>
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

      <div class="details-grid">
        <div class="info-card">
          <div class="info-header">
            <div class="user-avatar-lg">{{ substr($admin->name, 0, 1) }}</div>
            <div class="info-titles">
              <h2>{{ $admin->name }}</h2>
              <span class="badge badge-{{ $admin->role }}">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
              <span class="status-badge {{ $admin->is_active ? 'active' : 'inactive' }}">
                {{ $admin->is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
          <div class="info-body">
            <div class="info-row">
              <span class="info-label">Email:</span>
              <span class="info-value">{{ $admin->email }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Created:</span>
              <span class="info-value">{{ $admin->created_at->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Last Updated:</span>
              <span class="info-value">{{ $admin->updated_at->diffForHumans() }}</span>
            </div>
          </div>
          <div class="info-actions">
            @if(!$admin->isSuperAdmin())
              <form method="POST" action="{{ route('superadmin.admins.toggle', $admin) }}">
                @csrf
                <button type="submit" class="btn {{ $admin->is_active ? 'btn-warning' : 'btn-success' }}">
                  <i class="fa-solid {{ $admin->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                  {{ $admin->is_active ? 'Deactivate' : 'Activate' }}
                </button>
              </form>
              <form method="POST" action="{{ route('superadmin.admins.reset-pin', $admin) }}" onsubmit="return confirm('Reset PIN for this admin?');">
                @csrf
                <button type="submit" class="btn btn-info">
                  <i class="fa-solid fa-key"></i> Reset PIN
                </button>
              </form>
            @endif
          </div>
        </div>

        <div class="card full-width">
          <div class="card-header">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h3>
          </div>
          <div class="card-body">
            @if($admin->activities->count() > 0)
              <div class="activity-list">
                @foreach($admin->activities as $activity)
                  <div class="activity-item">
                    <div class="activity-icon">
                      @if($activity->action === 'create')
                        <i class="fa-solid fa-plus" style="color: #10b981;"></i>
                      @elseif($activity->action === 'update')
                        <i class="fa-solid fa-pen" style="color: #0ea5e9;"></i>
                      @elseif($activity->action === 'delete')
                        <i class="fa-solid fa-trash" style="color: #ef4444;"></i>
                      @else
                        <i class="fa-solid fa-circle-info" style="color: #f59e0b;"></i>
                      @endif
                    </div>
                    <div class="activity-content">
                      <div class="activity-description">{{ $activity->description }}</div>
                      <div class="activity-meta">{{ $activity->created_at->diffForHumans() }} · {{ $activity->entity_type }}</div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="empty-state">
                <i class="fa-solid fa-clipboard"></i>
                <p>No recent activity</p>
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
