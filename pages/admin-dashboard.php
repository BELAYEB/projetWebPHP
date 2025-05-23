<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();

// Dashboard stats
$stmt = $pdo->query("SELECT * FROM request");
$totalRequests = $stmt->rowCount();

$stmtinprogress = $pdo->query("SELECT * FROM request WHERE status='in progress'");
$totalrequestinprogress = $stmtinprogress->rowCount();

$stmticompleted = $pdo->query("SELECT * FROM request WHERE status='completed'");
$totalrequestcompleted = $stmticompleted->rowCount();

// Recent requests
$recentRequests = $pdo->query("SELECT id, title ,status,description FROM request ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Recent tasks
$recentTasks = $pdo->query("SELECT id, title, description, assigned_to, status FROM task ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - ContactFlow CRM</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .dashboard-table td {
      word-wrap: break-word;
      max-width: 250px;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <div class="sidebar-header">
        <i class="fas fa-project-diagram logo-icon"></i>
        <h2>ContactFlow</h2>
        <button id="toggleSidebar" class="toggle-sidebar"><i class="fas fa-bars"></i></button>
      </div>
      <div class="sidebar-content">
        <ul class="sidebar-menu">
          <li class="active"><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
          <li><a href="admin-tasks.php"><i class="fas fa-tasks"></i><span>Task Board</span></a></li>
          <li><a href="admin-client.php"><i class="fas fa-users"></i><span>Clients</span></a></li>
          <li><a href="admin-members.php"><i class="fas fa-users"></i><span>Members</span></a></li>
          <li><a href="admin-analytics.php"><i class="fas fa-chart-line"></i><span>Analytics</span></a></li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <a href="login.html" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
      </div>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left">
          <h1>Admin Dashboard</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= $_SESSION['user_name'] ?></span>
          </div>
        </div>
      </header>

      <div class="dashboard-content">
        <div class="dashboard-header">
          <h2>Welcome, <span><?= $_SESSION['user_name'] ?></span>!</h2>
        </div>

        <div class="dashboard-stats">
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="stat-info">
              <h3>Total Requests</h3>
              <p><?= $totalRequests ?></p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-spinner"></i></div>
            <div class="stat-info">
              <h3>In Progress</h3>
              <p><?= $totalrequestinprogress ?></p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
              <h3>Completed</h3>
              <p><?= $totalrequestcompleted ?></p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-info">
              <h3>Avg. Rating</h3>
              <p>0.0</p>
            </div>
          </div>
        </div>

   <div class="admin-dashboard-widgets" style="display: grid; gap: 2rem; grid-template-columns: 1fr 1fr;">
  
  <!-- Recent Tasks -->
  <div class="widget" style="background: #fff; padding: 1rem 1.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <div class="widget-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="font-size: 1.2rem;">📝 Recent Tasks</h3>
      <button class="btn-icon widget-refresh"><i class="fas fa-sync-alt"></i></button>
    </div>
    <?php if (count($recentTasks) > 0): ?>
      <?php foreach ($recentTasks as $task): ?>
        <div style="border-bottom: 1px solid #eee; padding: 0.8rem 0;">
          <h4 style="margin: 0; font-size: 1rem; color: #333;"><?= htmlspecialchars($task['title']) ?></h4>
          <p style="margin: 0.3rem 0; color: #666;"><?= htmlspecialchars($task['description']) ?></p>
          <div style="font-size: 0.85rem; color: #555;">
            👤 <?= htmlspecialchars($task['assigned_to']) ?> — 
            <span style="color: <?= $task['status'] === 'completed' ? '#28a745' : ($task['status'] === 'in progress' ? '#ffc107' : '#dc3545') ?>;">
              <?= htmlspecialchars($task['status']) ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="color: #888;">No recent tasks</p>
    <?php endif; ?>
  </div>

  <!-- Recent Requests -->
  <div class="widget" style="background: #fff; padding: 1rem 1.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <div class="widget-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="font-size: 1.2rem;">📨 Recent Requests</h3>
      <button class="btn-icon widget-refresh"><i class="fas fa-sync-alt"></i></button>
    </div>
    <?php if (count($recentRequests) > 0): ?>
      <?php foreach ($recentRequests as $req): ?>
        <div style="border-bottom: 1px solid #eee; padding: 0.8rem 0;">
          <h4 style="margin: 0; font-size: 1rem; color: #333;"><?= htmlspecialchars($req['title']) ?></h4>
          <p style="margin: 0.3rem 0; color: #666;"><?= htmlspecialchars($req['description']) ?></p>
          <div style="font-size: 0.85rem; color: #555;">
            <span style="color: <?= $req['status'] === 'completed' ? '#28a745' : ($req['status'] === 'in progress' ? '#ffc107' : '#dc3545') ?>;">
              <?= htmlspecialchars($req['status']) ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="color: #888;">No recent requests</p>
    <?php endif; ?>
  </div>

</div>

      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/js/localStorage.js"></script>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/domUtils.js"></script>
  <script src="../assets/js/exportUtils.js"></script>
  <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>
