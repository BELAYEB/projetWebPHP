<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Client List - ContactFlow CRM</title>

  <!-- Styles -->
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
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
          <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
          <li><a href="admin-requests.html"><i class="fas fa-ticket-alt"></i><span>Service Requests</span></a></li>
          <li><a href="admin-tasks.php"><i class="fas fa-tasks"></i><span>Task Board</span></a></li>
          <li><a href="admin-client.php"><i class="fas fa-users"></i><span>Clients</span></a></li>
          <li><a href="admin-members.php"><i class="fas fa-users"></i><span>Members</span></a></li>
          <li><a href="admin-analytics.php"><i class="fas fa-chart-line"></i><span>Analytics</span></a></li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <button id="logoutBtn" class="btn-logout">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Navbar -->
      <header class="content-header">
        <div class="header-left">
          <h1>Client List</h1>
        </div>
        <div class="header-right">
          <div class="user-info">
            <span class="username"><?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
            <i class="fas fa-user-circle"></i>
          </div>
        </div>
      </header>

      <section class="client-list-section">
        <table class="client-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>All Tasks</th>
              <th>Tasks Completed</th>
              <th>Tasks In Progress</th>
              <th>Tasks To Do</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $clientstmt = $pdo->query("SELECT id, name FROM users WHERE role = 'client'");
            $clients = $clientstmt->fetchAll(PDO::FETCH_ASSOC);

            // Task counts by status
            $taskStatusStmt = $pdo->prepare("
        SELECT 
          status,
          COUNT(*) AS task_count
        FROM request
        WHERE client_id = ?
        GROUP BY status
      ");

            // Total task count
            $totalTaskStmt = $pdo->prepare("
        SELECT COUNT(*) AS total_tasks FROM request WHERE client_id = ?
      ");

            foreach ($clients as $client) {
              $completed = $inProgress = $toDo = 0;

              // Get total tasks
              $totalTaskStmt->execute([$client['id']]);
              $totalTasks = $totalTaskStmt->fetch(PDO::FETCH_ASSOC)['total_tasks'] ?? 0;

              // Get task counts by status
              $taskStatusStmt->execute([$client['id']]);
              $tasks = $taskStatusStmt->fetchAll(PDO::FETCH_ASSOC);

              foreach ($tasks as $task) {
                switch ($task['status']) {
                  case 'completed':
                    $completed = $task['task_count'];
                    break;
                  case 'in progress':
                    $inProgress = $task['task_count'];
                    break;
                  case 'to_do':
                    $toDo = $task['task_count'];
                    break;
                }
              }

              echo "<tr>";
              echo "<td>" . htmlspecialchars($client['id']) . "</td>";
              echo "<td>" . htmlspecialchars($client['name']) . "</td>";
              echo "<td>" . $totalTasks . "</td>";
              echo "<td>" . $completed . "</td>";
              echo "<td>" . $inProgress . "</td>";
              echo "<td>" . $toDo . "</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </section>



    </main>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/js/localStorage.js"></script>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/domUtils.js"></script>
  <script src="../assets/js/exportUtils.js"></script>
  <script src="../assets/js/admin-dashboard.js"></script>
</body>

</html>