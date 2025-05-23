<?php
require_once 'config.php';
session_start();

// DB Connection
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");

// Fetch all requests
$stmt = $pdo->query("SELECT id, title, status, type, priority, LEFT(created_at, 10) AS created_at FROM request");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary counts
$totalRequests = count($requests);
$totalCompletedRequests = 0;
$inprogressRequests = 0;
$highPriorityTasks = [];

foreach ($requests as $req) {
    if (strtolower($req['status']) === 'completed') {
        $totalCompletedRequests++;
    }
    if (strtolower($req['status']) === 'in progress') {
        $inprogressRequests++;
    }
    if (strtolower($req['priority']) === 'high' || strtolower($req['priority']) === 'urgent') {
        $highPriorityTasks[] = $req;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Client Dashboard - ContactFlow CRM</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    /* Basic table styling for dashboard widgets */
    .widget table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .widget table th,
    .widget table td {
      padding: 8px 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .widget table th {
      background-color: #f5f5f5;
    }

    .empty-state {
      text-align: center;
      color: #888;
      padding: 20px 0;
    }

    .empty-state i {
      font-size: 40px;
      margin-bottom: 10px;
      color: #bbb;
    }
  </style>
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
            <a href="request-service.html">
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
          <li>
            <a href="client-feedback.php">
              <i class="fas fa-list-alt"></i>
              <span>Feedback</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <a id="logoutBtn" class="btn-logout" href="login.html">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left">
          <h1>Client Dashboard</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s"
              alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
          </div>
        </div>
      </header>

      <div class="dashboard-content">
        <div class="dashboard-header">
          <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
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
        </div>

        <div class="dashboard-widgets" id="dashboardWidgets">

          <!-- Recent Requests Widget -->
          <div class="widget" data-widget-id="recentRequests">
            <div class="widget-header">
              <h3>Recent Requests</h3>
              <div class="widget-actions">
                <button class="btn-icon widget-refresh" onclick="location.reload()">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn-icon widget-remove" onclick="this.closest('.widget').remove()">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="widget-content">
              <?php if (count($requests) === 0): ?>
                <div class="empty-state">
                  <i class="fas fa-ticket-alt"></i>
                  <p>No recent requests</p>
                </div>
              <?php else: ?>
                <table>
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Type</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Show last 5 requests (most recent first)
                    $recent = array_slice(array_reverse($requests), 0, 5);
                    foreach ($recent as $req): ?>
                      <tr>
                        <td><?= htmlspecialchars($req['title']) ?></td>
                        <td><?= htmlspecialchars($req['type']) ?></td>
                        <td><?= htmlspecialchars($req['status']) ?></td>
                        <td><?= htmlspecialchars($req['created_at']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endif; ?>
            </div>
            <div class="widget-footer">
              <a href="view-requests.php">View All Requests</a>
            </div>
          </div>

          <!-- High Priority Tasks Widget -->
          <div class="widget" data-widget-id="highPriorityTasks">
            <div class="widget-header">
              <h3>High Priority Tasks</h3>
              <div class="widget-actions">
                <button class="btn-icon widget-refresh" onclick="location.reload()">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn-icon widget-remove" onclick="this.closest('.widget').remove()">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <div class="widget-content">
              <?php if (count($highPriorityTasks) === 0): ?>
                <div class="empty-state">
                  <i class="fas fa-tasks"></i>
                  <p>No high priority tasks</p>
                </div>
              <?php else: ?>
                <table>
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Priority</th>
                      <th>Status</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($highPriorityTasks as $task): ?>
                      <tr>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= htmlspecialchars($task['priority']) ?></td>
                        <td><?= htmlspecialchars($task['status']) ?></td>
                        <td><?= htmlspecialchars($task['created_at']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/main.js"></script>
</body>

</html>
