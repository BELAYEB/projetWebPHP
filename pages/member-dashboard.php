<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();

$tasksstmt = $pdo->query("SELECT * FROM task");
$totaltasks = $tasksstmt->rowCount();

$tasksinprogressstmt = $pdo->query("SELECT * FROM task where status='in progress' ");
$tasksinprogress = $tasksinprogressstmt->rowCount();

$taskscompletedstmt = $pdo->query("SELECT * FROM task where status='completed' ");
$taskscompleted = $taskscompletedstmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Employee Dashboard - ContactFlow CRM</title>
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
            <a href="member-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="member-tasks.php">
              <i class="fas fa-tasks"></i>
              <span>My Tasks</span>
            </a>
          </li>

        </ul>
      </div>
      <div class="sidebar-footer">
        <a href="login.html" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>

    </aside>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left">
          <h1>Employee Dashboard</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="../assets/images/avatar.png" alt="Employee Avatar" id="userAvatar" />
            <span id="userName">Employee</span>
          </div>
        </div>
      </header>



      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-ticket-alt"></i>
          </div>
          <div class="stat-info">
            <h3>Total taks</h3>
            <p id="totalRequests"><?= $totaltasks ?></p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-spinner"></i>
          </div>
          <div class="stat-info">
            <h3>In Progress</h3>
            <p id="inProgressRequests"><?= $tasksinprogress ?></p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-info">
            <h3>Completed</h3>
            <p id="completedRequests"><?= $taskscompleted ?></p>
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
  <script src="../assets/js/employee-dashboard.js"></script>
</body>

</html>