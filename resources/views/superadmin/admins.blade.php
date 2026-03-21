<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Beach Owners · Dagat Ta bAI</title>
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
        <a href="/superadmin/admins" class="nav-item {{ request()->is('superadmin/admins*') ? 'active' : '' }}">
          <i class="fa-solid fa-users-gear"></i>
          <span>Beach Owners</span>
        </a>
        <a href="/superadmin/users" class="nav-item {{ request()->is('superadmin/users*') ? 'active' : '' }}">
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
        <h1 class="page-title">Beach Owners</h1>
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
        <div class="card full-width">
          <div class="card-header">
            <h3><i class="fa-solid fa-users"></i> All Beach Owners ({{ $admins->count() }})</h3>
          </div>
          <div class="card-body">
            @if($admins->count() > 0)
              <div class="table-responsive">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($admins as $admin)
                      <tr>
                        <td>
                          <div class="user-cell">
                            <div class="user-avatar-sm">{{ substr($admin->name, 0, 1) }}</div>
                            <span>{{ $admin->name }}</span>
                            @if($admin->isSuperAdmin())
                              <span class="badge badge-super">SUPER</span>
                            @endif
                          </div>
                        </td>
                        <td>{{ $admin->email }}</td>
                        <td>
                          <span class="badge badge-{{ $admin->role }}">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
                        </td>
                        <td>
                          <span class="status-badge {{ $admin->is_active ? 'active' : 'inactive' }}">
                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                          </span>
                        </td>
                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                        <td>
                          <div class="action-buttons">
                            <a href="{{ route('superadmin.admins.details', $admin) }}" class="btn btn-sm btn-secondary" title="View Details">
                              <i class="fa-solid fa-eye"></i>
                            </a>
                            @if(!$admin->isSuperAdmin())
                              <form method="POST" action="{{ route('superadmin.admins.toggle', $admin) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $admin->is_active ? 'btn-warning' : 'btn-success' }}" title="{{ $admin->is_active ? 'Deactivate' : 'Activate' }}">
                                  <i class="fa-solid {{ $admin->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                              </form>
                              <form method="POST" action="{{ route('superadmin.admins.reset-pin', $admin) }}" class="inline-form" onsubmit="return confirm('Reset PIN for this admin?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info" title="Reset PIN">
                                  <i class="fa-solid fa-key"></i>
                                </button>
                              </form>
                              <form method="POST" action="{{ route('superadmin.admins.delete', $admin) }}" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                  <i class="fa-solid fa-trash"></i>
                                </button>
                              </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="empty-state">
                <i class="fa-solid fa-users-slash"></i>
                <p>No beach owners yet. They will appear here when applications are approved.</p>
                <a href="{{ route('superadmin.applications') }}" class="btn btn-primary" style="margin-top: 15px;">
                  <i class="fa-solid fa-clipboard-list"></i> View Applications
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
