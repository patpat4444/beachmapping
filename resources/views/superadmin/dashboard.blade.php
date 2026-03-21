<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Super Admin Dashboard · Dagat Ta bAI</title>
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
        <a href="/superadmin/dashboard" class="nav-item active">
          <i class="fa-solid fa-chart-line"></i>
          <span>Dashboard</span>
        </a>
        
        <div class="nav-section">Management</div>
        <a href="/superadmin/admins" class="nav-item {{ request()->is('superadmin/admins*') ? 'active' : '' }}">
          <i class="fa-solid fa-users-gear"></i>
          <span>Beach Owners</span>
        </a>
        <a href="/superadmin/users" class="nav-item {{ request()->is('superadmin/users*') ? 'active' : '' }}">
          <i class="fa-solid fa-users"></i>
          <span>Manage User Accounts</span>
        </a>
        
        <div class="nav-section">System & Data</div>
        <a href="/superadmin/applications" class="nav-item {{ request()->is('superadmin/applications*') ? 'active' : '' }}">
          <i class="fa-solid fa-clipboard-list"></i>
          <span>Applications</span>
        </a>
        <a href="/superadmin/activity-logs" class="nav-item {{ request()->is('superadmin/activity-logs*') ? 'active' : '' }}">
          <i class="fa-solid fa-clock-rotate-left"></i>
          <span>Monitor System Activity</span>
        </a>
        <a href="/superadmin/weather-data" class="nav-item {{ request()->is('superadmin/weather-data*') ? 'active' : '' }}">
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
        <h1 class="page-title">Super Admin Dashboard</h1>
        <div class="header-actions">
          <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
            <i class="fa-solid fa-sun" id="themeIcon"></i>
          </button>
          <a href="/landing" class="btn btn-secondary"><i class="fa-solid fa-external-link-alt"></i> View Site</a>
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

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
            <i class="fa-solid fa-users"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ $stats['total_admins'] }}</div>
            <div class="stat-label">Total Admins</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
            <i class="fa-solid fa-user-check"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ $stats['active_admins'] }}</div>
            <div class="stat-label">Active Admins</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <i class="fa-solid fa-map-pin"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ $stats['total_locations'] }}</div>
            <div class="stat-label">Locations</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
            <i class="fa-solid fa-clipboard-list"></i>
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ $stats['pending_applications'] }}</div>
            <div class="stat-label">Pending Applications</div>
          </div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="dashboard-card full-width recent-activity-card">
          <div class="card-header">
            <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h3>
          </div>
          <div class="card-body activity-scroll">
            @if($recentActivities->count() > 0)
              <div class="activity-list">
                @foreach($recentActivities as $activity)
                  <div class="activity-item">
                    <div class="activity-icon">
                      @if($activity->action === 'create')
                        <i class="fa-solid fa-plus" style="color: #10b981;"></i>
                      @elseif($activity->action === 'update')
                        <i class="fa-solid fa-pen" style="color: #0ea5e9;"></i>
                      @elseif($activity->action === 'delete')
                        <i class="fa-solid fa-trash" style="color: #ef4444;"></i>
                      @elseif($activity->action === 'approve')
                        <i class="fa-solid fa-check" style="color: #10b981;"></i>
                      @else
                        <i class="fa-solid fa-circle-info" style="color: #f59e0b;"></i>
                      @endif
                    </div>
                    <div class="activity-content">
                      <div class="activity-description">{{ $activity->description }}</div>
                      <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
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
      // Theme toggle
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
