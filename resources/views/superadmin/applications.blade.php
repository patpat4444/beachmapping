<!doctype html>
<html lang="en" data-theme="dark">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Beach Owner Applications · Dagat Ta bAI</title>
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
        <a href="/superadmin/applications" class="nav-item active">
          <i class="fa-solid fa-clipboard-list"></i>
          <span>Applications</span>
          @if($stats['pending'] > 0)
            <span class="nav-badge">{{ $stats['pending'] }}</span>
          @endif
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
        <h1 class="page-title">Beach Owner Applications</h1>
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

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon pending"><i class="fa-solid fa-clock"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon approved"><i class="fa-solid fa-check"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ $stats['approved'] }}</div>
            <div class="stat-label">Approved</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon rejected"><i class="fa-solid fa-xmark"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ $stats['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="fa-solid fa-clipboard-list"></i></div>
          <div class="stat-info">
            <div class="stat-value">{{ $stats['pending'] + $stats['approved'] + $stats['rejected'] }}</div>
            <div class="stat-label">Total</div>
          </div>
        </div>
      </div>

      <div class="content-grid">
        <div class="card full-width">
          <div class="card-header">
            <h3><i class="fa-solid fa-users"></i> All Applications ({{ $applications->total() }})</h3>
            <div class="filter-tabs">
              <a href="?status=all" class="tab {{ request('status', 'all') === 'all' ? 'active' : '' }}">All</a>
              <a href="?status=pending" class="tab {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
              <a href="?status=approved" class="tab {{ request('status') === 'approved' ? 'active' : '' }}">Approved</a>
              <a href="?status=rejected" class="tab {{ request('status') === 'rejected' ? 'active' : '' }}">Rejected</a>
            </div>
          </div>
          <div class="card-body">
            @if($applications->count() > 0)
              <div class="table-responsive">
                <table class="data-table applications-table">
                  <thead>
                    <tr>
                      <th>Full Name</th>
                      <th>Email</th>
                      <th>Business Name</th>
                      <th>Documents</th>
                      <th>Status</th>
                      <th>Submitted</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($applications as $application)
                      <tr class="status-{{ $application->status }}">
                        <td>
                          <div class="user-cell">
                            <div class="user-avatar-sm">{{ substr($application->full_name, 0, 1) }}</div>
                            <span>{{ $application->full_name }}</span>
                          </div>
                        </td>
                        <td>{{ $application->email }}</td>
                        <td>{{ $application->business_name }}</td>
                        <td>
                          <div class="doc-links">
                            @if($application->bir_document)
                              <a href="{{ asset('storage/' . $application->bir_document) }}" target="_blank" class="doc-link" title="View BIR Document">
                                <i class="fa-solid fa-file-pdf"></i> BIR
                              </a>
                            @endif
                            @if($application->business_permit)
                              <a href="{{ asset('storage/' . $application->business_permit) }}" target="_blank" class="doc-link" title="View Business Permit">
                                <i class="fa-solid fa-file-pdf"></i> Permit
                              </a>
                            @endif
                            @if(!$application->bir_document && !$application->business_permit)
                              <span class="text-muted">No docs</span>
                            @endif
                          </div>
                        </td>
                        <td>
                          <span class="status-badge status-{{ $application->status }}">
                            {{ ucfirst($application->status) }}
                          </span>
                        </td>
                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                        <td>
                          <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="showDetails({{ $application->id }})" title="View Details">
                              <i class="fa-solid fa-eye"></i>
                            </button>
                            
                            @if($application->isPending())
                              <form method="POST" action="{{ route('superadmin.applications.approve', $application) }}" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this application? A PIN will be emailed to the applicant.')" title="Approve">
                                  <i class="fa-solid fa-check"></i>
                                </button>
                              </form>
                              <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $application->id }})" title="Reject">
                                <i class="fa-solid fa-xmark"></i>
                              </button>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              
              <div class="pagination-wrapper">
                {{ $applications->links() }}
              </div>
            @else
              <div class="empty-state">
                <i class="fa-solid fa-clipboard"></i>
                <p>No applications found</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </main>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal" style="display: none;">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Reject Application</h3>
          <button type="button" class="close-btn" onclick="hideRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label>Rejection Reason (Optional)</label>
              <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Explain why the application is being rejected..."></textarea>
              <small class="help-text">This will be included in the email sent to the applicant.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideRejectModal()">Cancel</button>
            <button type="submit" class="btn btn-danger">Reject Application</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="modal" style="display: none;">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Application Details</h3>
          <button type="button" class="close-btn" onclick="hideDetailsModal()">&times;</button>
        </div>
        <div class="modal-body" id="detailsContent">
          <!-- Content loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="hideDetailsModal()">Close</button>
        </div>
      </div>
    </div>

    <style>
      .applications-table .doc-links {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
      }
      .applications-table .doc-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: var(--accent-primary);
        color: white;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none;
      }
      .applications-table .doc-link:hover {
        opacity: 0.9;
      }
      .nav-badge {
        background: #ef4444;
        color: white;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: auto;
      }
      .filter-tabs {
        display: flex;
        gap: 8px;
      }
      .filter-tabs .tab {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        color: var(--text-secondary);
        background: var(--bg-secondary);
      }
      .filter-tabs .tab.active {
        background: var(--accent-primary);
        color: white;
      }
      .filter-tabs .tab:hover {
        color: var(--text-primary);
      }
      .status-pending { background: rgba(245, 158, 11, 0.05); }
      .status-approved { background: rgba(16, 185, 129, 0.05); }
      .status-rejected { background: rgba(239, 68, 68, 0.05); }
      
      .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
      }
      .modal-content {
        background: var(--bg-primary);
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
      }
      .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
      }
      .modal-header h3 {
        margin: 0;
        font-size: 18px;
      }
      .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-muted);
      }
      .modal-body {
        padding: 20px;
      }
      .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px;
        border-top: 1px solid var(--border-color);
      }
      .help-text {
        color: var(--text-muted);
        font-size: 12px;
        margin-top: 4px;
      }
      .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 20px;
      }
    </style>

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
      
      function showRejectModal(applicationId) {
        const form = document.getElementById('rejectForm');
        form.action = `/superadmin/applications/${applicationId}/reject`;
        document.getElementById('rejectModal').style.display = 'flex';
      }
      
      function hideRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
      }
      
      function showDetails(applicationId) {
        // Find application data from the table
        const applications = @json($applications->items());
        const app = applications.find(a => a.id === applicationId);
        
        if (app) {
          const content = document.getElementById('detailsContent');
          content.innerHTML = `
            <div class="detail-row">
              <strong>Full Name:</strong> ${app.full_name}
            </div>
            <div class="detail-row">
              <strong>Email:</strong> ${app.email}
            </div>
            <div class="detail-row">
              <strong>Phone:</strong> ${app.phone || 'N/A'}
            </div>
            <div class="detail-row">
              <strong>Business Name:</strong> ${app.business_name}
            </div>
            <div class="detail-row">
              <strong>Business Address:</strong> ${app.business_address}
            </div>
            <div class="detail-row">
              <strong>Status:</strong> <span class="status-badge status-${app.status}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span>
            </div>
            <div class="detail-row">
              <strong>Submitted:</strong> ${new Date(app.created_at).toLocaleDateString()}
            </div>
            ${app.message ? `<div class="detail-row"><strong>Message:</strong><p>${app.message}</p></div>` : ''}
            ${app.rejection_reason ? `<div class="detail-row"><strong>Rejection Reason:</strong><p>${app.rejection_reason}</p></div>` : ''}
          `;
          document.getElementById('detailsModal').style.display = 'flex';
        }
      }
      
      function hideDetailsModal() {
        document.getElementById('detailsModal').style.display = 'none';
      }
      
      // Close modals on outside click
      window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
          event.target.style.display = 'none';
        }
      }
    </script>
  </body>
</html>
