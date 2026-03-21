<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Activity Logs · Dagat Ta bAI</title>
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
        <a href="/superadmin/activity-logs" class="nav-item active">
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
        <h1 class="page-title">Activity Logs</h1>
        <div class="header-actions">
          <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
            <i class="fa-solid fa-sun" id="themeIcon"></i>
          </button>
        </div>
      </header>

      <div class="card">
        <div class="card-header">
          <h3><i class="fa-solid fa-list"></i> All System Activity</h3>
        </div>
        <div class="card-body">
          @if($activities->count() > 0)
            <div class="activity-log-list">
              @foreach($activities as $activity)
                <div class="activity-log-item">
                  <div class="activity-log-icon">
                    @if($activity->action === 'create')
                      <i class="fa-solid fa-plus" style="color: #10b981;"></i>
                    @elseif($activity->action === 'update')
                      <i class="fa-solid fa-pen" style="color: #0ea5e9;"></i>
                    @elseif($activity->action === 'delete')
                      <i class="fa-solid fa-trash" style="color: #ef4444;"></i>
                    @elseif($activity->action === 'approve')
                      <i class="fa-solid fa-check" style="color: #10b981;"></i>
                    @elseif($activity->action === 'reject')
                      <i class="fa-solid fa-xmark" style="color: #ef4444;"></i>
                    @elseif($activity->action === 'login')
                      <i class="fa-solid fa-right-to-bracket" style="color: #8b5cf6;"></i>
                    @elseif($activity->action === 'logout')
                      <i class="fa-solid fa-right-from-bracket" style="color: #f59e0b;"></i>
                    @else
                      <i class="fa-solid fa-circle-info" style="color: #6b7280;"></i>
                    @endif
                  </div>
                  <div class="activity-log-content">
                    <div class="activity-log-header">
                      <span class="activity-log-user">{{ $activity->user->name ?? 'System' }}</span>
                      <span class="activity-log-time">{{ $activity->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="activity-log-description">{{ $activity->description }}</div>
                    <div class="activity-log-meta">
                      <span class="activity-log-action">{{ ucfirst($activity->action) }}</span>
                      <span class="activity-log-entity">{{ class_basename($activity->entity_type) }} #{{ $activity->entity_id }}</span>
                      @if($activity->ip_address)
                        <span class="activity-log-ip"><i class="fa-solid fa-network-wired"></i> {{ $activity->ip_address }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            <div class="pagination">
              {{ $activities->links() }}
            </div>
          @else
            <div class="empty-state">
              <i class="fa-solid fa-clipboard"></i>
              <p>No activity logs yet</p>
            </div>
          @endif
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
