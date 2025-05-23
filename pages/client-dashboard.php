<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();
$stmt = $pdo->query("SELECT * FROM request");
$totalRequests = $stmt->rowCount();

$completedStmt = $pdo->query("SELECT * FROM request WHERE status = 'completed'");
$totalCompletedRequests = $completedStmt->rowCount();

$inprogressStmt = $pdo->query("SELECT * FROM request WHERE status = 'in progress'");
$inprogressRequests = $inprogressStmt->rowCount();
//var_dump($_SESSION[user_name]);
?>



<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Client Dashboard - ContactFlow CRM</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
            <a href="client-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="request-service.php">
              <i class="fas fa-plus-circle"></i>
              <span>New Request</span>
            </a>
          </li>
          <li>
            <a href="view-requests.php">
              <i class="fas fa-list-alt"></i>
              <span>My Requests</span>
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
          <h1>Client Dashboard</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= $_SESSION['user_name'] ?> </span>
          </div>
        </div>
      </header>

      <div class="dashboard-content">
        <div class="dashboard-header">
          <h2>Welcome, <?= $_SESSION['user_name'] ?> </span>!</h2>

          <div class="dashboard-actions">
         
          </div>
        </div>

        <div class="dashboard-stats">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-ticket-alt"></i>
            </div>
            <div class="stat-info">
              <h3>Total Requests</h3>
              <p id="totalRequests"><?= $totalRequests ?></p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-info">
              <h3>In Progress</h3>
              <p id="inProgressRequests"><?= $inprogressRequests ?></p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
              <h3>Completed</h3>
              <p id="completedRequests"><?= $totalCompletedRequests ?></p>
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

        <div class="dashboard-widgets" id="dashboardWidgets">
          <div class="widget" data-widget-id="recentRequests">
            <div class="widget-header">
              <h3>Recent Requests</h3>
              <div class="widget-actions">
                <button class="btn-icon widget-refresh">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn-icon widget-remove">
                  <i class="fas fa-times"></i>
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
              <a href="view-requests.html">View All Requests</a>
            </div>
          </div>

          <div class="widget" data-widget-id="highPriorityTasks">
            <div class="widget-header">
              <h3>High Priority Tasks</h3>
              <div class="widget-actions">
                <button class="btn-icon widget-refresh">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn-icon widget-remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="widget-content">
              <div class="task-list" id="highPriorityTasksList">
                <div class="empty-state">
                  <i class="fas fa-tasks"></i>
                  <p>No high priority tasks</p>
                </div>
              </div>
            </div>
          </div>

          <div class="widget" data-widget-id="upcomingTasks">
            <div class="widget-header">
              <h3>Upcoming Tasks</h3>
              <div class="widget-actions">
                <button class="btn-icon widget-refresh">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn-icon widget-remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="widget-content">
              <div class="task-list" id="upcomingTasksList">
                <div class="empty-state">
                  <i class="fas fa-calendar"></i>
                  <p>No upcoming tasks</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div id="addWidgetModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add Widget</h2>
        <button class="close-modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="widget-options">
          <div class="widget-option" data-widget-id="recentRequests">
            <i class="fas fa-ticket-alt"></i>
            <h4>Recent Requests</h4>
            <p>View your most recent service requests</p>
          </div>
          <div class="widget-option" data-widget-id="highPriorityTasks">
            <i class="fas fa-exclamation-circle"></i>
            <h4>High Priority Tasks</h4>
            <p>View tasks marked as high priority</p>
          </div>
          <div class="widget-option" data-widget-id="upcomingTasks">
            <i class="fas fa-calendar"></i>
            <h4>Upcoming Tasks</h4>
            <p>View tasks scheduled for the near future</p>
          </div>
          <div class="widget-option" data-widget-id="requestStatus">
            <i class="fas fa-chart-pie"></i>
            <h4>Request Status</h4>
            <p>View the status of all your requests</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary close-modal">Cancel</button>
        <button id="addWidgetBtn" class="btn-primary">Add Widget</button>
      </div>
    </div>
  </div>

  <div id="notification" class="notification"></div>

  <script src="../assets/js/localStorage.js"></script>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/domUtils.js"></script>
  <script src="../assets/js/client-dashboard.js"></script>
</body>

</html>