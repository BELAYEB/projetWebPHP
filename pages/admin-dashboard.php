<?php 
session_start();

?>



<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - ContactFlow CRM</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>

  <body>
    <div class="dashboard-container">
      <aside class="sidebar">
        <div class="sidebar-header">
          <i class="fas fa-project-diagram logo-icon"></i>
          <h2>ContactFlow</h2>
          <button id="toggleSidebar" class="toggle-sidebar">
            <i class="fas fa-bars"></i>
          </button>
        </div>
        <div class="sidebar-content">
          <ul class="sidebar-menu">
            <li class="active">
              <a href="admin-dashboard.html">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li>
              <a href="admin-requests.html">
                <i class="fas fa-ticket-alt"></i>
                <span>Service Requests</span>
              </a>
            </li>
            <li>
              <a href="admin-tasks.html">
                <i class="fas fa-tasks"></i>
                <span>Task Board</span>
              </a>
            </li>
            <li>
              <a href="admin-clients.html">
                <i class="fas fa-users"></i>
                <span>Clients</span>
              </a>
            </li>
            <li>
              <a href="admin-analytics.html">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="sidebar-footer">
          <button id="logoutBtn" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </button>
        </div>
      </aside>

      <main class="main-content">
        <header class="content-header">
          <div class="header-left">
            <h1>Admin Dashboard</h1>
          </div>
          <div class="header-right">
            <div class="user-profile">
              <img
                src="../assets/images/avatar.png alt="
                Admin
                Avatar
                id="userAvatar"
              />
              <span id="userName">name</span>
            </div>
          </div>
        </header>

        <div class="dashboard-content">
          <div class="dashboard-header">
            <h2>Welcome, <span> <?= $_SESSION['user_name'] ?> </span>!</h2>
            <div class="dashboard-actions">
              <button id="exportDataBtn" class="btn-secondary">
                <i class="fas fa-download"></i> Export Data
              </button>
              <button id="importDataBtn" class="btn-secondary">
                <i class="fas fa-upload"></i> Import Data
              </button>
            </div>
          </div>

          <div class="dashboard-stats">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-ticket-alt"></i>
              </div>
              <div class="stat-info">
                <h3>Total Requests</h3>
                <p id="totalRequests">0</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-spinner"></i>
              </div>
              <div class="stat-info">
                <h3>In Progress</h3>
                <p id="inProgressRequests">0</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="stat-info">
                <h3>Completed</h3>
                <p id="completedRequests">0</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-star"></i>
              </div>
              <div class="stat-info">
                <h3>Avg. Rating</h3>
                <p id="avgRating">0.0</p>
              </div>
            </div>
          </div>

          <div class="admin-dashboard-widgets">
            <div class="widget">
              <div class="widget-header">
                <h3>Recent Requests</h3>
                <div class="widget-actions">
                  <button class="btn-icon widget-refresh">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
              <div class="widget-content">
                <div class="request-list" id="recentRequestsList">
                  <div class="empty-state">
                    <i class="fas fa-ticket-alt"></i>
                    <p>No recent requests</p>
                  </div>
                </div>
              </div>
              <div class="widget-footer">
                <a href="admin-requests.html">View All Requests</a>
              </div>
            </div>

            <div class="widget">
              <div class="widget-header">
                <h3>Request Status</h3>
                <div class="widget-actions">
                  <button class="btn-icon widget-refresh">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
              <div class="widget-content">
                <canvas id="requestStatusChart"></canvas>
              </div>
            </div>

            <div class="widget">
              <div class="widget-header">
                <h3>Recent Activity</h3>
                <div class="widget-actions">
                  <button class="btn-icon widget-refresh">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
              <div class="widget-content">
                <div class="activity-list" id="recentActivityList">
                  <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No recent activity</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="widget">
              <div class="widget-header">
                <h3>Client Satisfaction</h3>
                <div class="widget-actions">
                  <button class="btn-icon widget-refresh">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
              <div class="widget-content">
                <canvas id="satisfactionChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <div id="notificationPanel" class="notification-panel">
      <div class="notification-header">
        <h3>Notifications</h3>
        <button id="closeNotifications" class="btn-icon">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="notification-content">
        <div id="notificationList">
          <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <p>No notifications</p>
          </div>
        </div>
      </div>
      <div class="notification-footer">
        <button id="markAllReadBtn" class="btn-text">Mark All as Read</button>
        <button id="clearAllBtn" class="btn-text">Clear All</button>
      </div>
    </div>

    <div id="importDataModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Import Data</h2>
          <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="importType">Select Import Type</label>
            <select id="importType">
              <option value="clients">Clients</option>
              <option value="requests">Service Requests</option>
            </select>
          </div>
          <div class="form-group">
            <label for="importFile">Upload CSV File</label>
            <input type="file" id="importFile" accept=".csv" />
          </div>
          <div class="import-preview">
            <h4>Preview</h4>
            <div id="importPreview" class="preview-content">
              <p>No file selected</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-secondary close-modal">Cancel</button>
          <button id="confirmImportBtn" class="btn-primary">Import Data</button>
        </div>
      </div>
    </div>

    <div id="notification" class="notification"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/localStorage.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/domUtils.js"></script>
    <script src="../assets/js/exportUtils.js"></script>
    <script src="../assets/js/admin-dashboard.js"></script>
  </body>
</html>
