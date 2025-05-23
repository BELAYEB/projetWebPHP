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
  <link rel="stylesheet" href="../assets/css/style.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f8;
    }

    .dashboard-container {
      display: flex;
      height: 100vh;
    }

    .main-content {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 20px;
    }

    .client-list-section {
      background-color: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    table.client-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .client-table thead {
      background-color: #3498db;
      color: #fff;
    }

    .client-table th,
    .client-table td {
      padding: 14px 18px;
      text-align: left;
    }

    .client-table tbody tr {
      background-color: #fdfdfd;
      border-bottom: 1px solid #eee;
      transition: background-color 0.3s;
    }

    .client-table tbody tr:hover {
      background-color: #f0f8ff;
    }

    .status-completed {
      color: #27ae60;
      font-weight: bold;
    }

    .status-in-progress {
      color: #f39c12;
      font-weight: bold;
    }

    .status-to-do {
      color: #c0392b;
      font-weight: bold;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-info .username {
      font-weight: bold;
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
          <li class="active"><a href="admin-dashboard.php"><i
                class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
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
          <h1>Client List</h1>
        </div>
     <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s"
              alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= $_SESSION['user_name'] ?></span>
          </div>
        </div>
      </header>

      <section class="client-list-section">
        <table class="client-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>All Request</th>
              <th>Request Completed</th>
              <th>Request In Progress</th>
              <th>Request To Do</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $clientstmt = $pdo->query("SELECT id, name FROM users WHERE role = 'client'");
            $clients = $clientstmt->fetchAll(PDO::FETCH_ASSOC);

            $taskStatusStmt = $pdo->prepare("
              SELECT status, COUNT(*) AS task_count
              FROM request
              WHERE client_id = ?
              GROUP BY status
            ");

            $totalTaskStmt = $pdo->prepare("
              SELECT COUNT(*) AS total_tasks FROM request WHERE client_id = ?
            ");

            foreach ($clients as $client) {
              $completed = $inProgress = $toDo = 0;

              $totalTaskStmt->execute([$client['id']]);
              $totalTasks = $totalTaskStmt->fetch(PDO::FETCH_ASSOC)['total_tasks'] ?? 0;

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
              echo "<td class='status-completed'>" . $completed . "</td>";
              echo "<td class='status-in-progress'>" . $inProgress . "</td>";
              echo "<td class='status-to-do'>" . $toDo . "</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>

</html>
