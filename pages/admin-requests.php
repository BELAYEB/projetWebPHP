<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_id'])) {
  $taskId = $_POST['complete_id'];
  $updateStmt = $pdo->prepare("UPDATE request SET status = 'completed' WHERE id = ?");
  $updateStmt->execute([$taskId]);
}

// Fetch all tasks
$stmt = $pdo->query("SELECT id, title, description, status FROM request");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Task Board - ContactFlow CRM</title>
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
          <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
          <li><a href="admin-requests.html"><i class="fas fa-ticket-alt"></i><span>Service Requests</span></a></li>
          <li class="active"><a href="admin-tasks.php"><i class="fas fa-tasks"></i><span>Task Board</span></a></li>
          <li><a href="admin-client.php"><i class="fas fa-users"></i><span>Clients</span></a></li>
          <li><a href="admin-analytics.php"><i class="fas fa-chart-line"></i><span>Analytics</span></a></li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <a href="login.html" class="btn-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
      </div>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left"><h1>Admin Dashboard</h1></div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= $_SESSION['user_name'] ?></span>
          </div>
        </div>
      </header>

      <div class="content-body">
        <div class="card">
          <div class="card-header">
            <h2>Task List</h2>
          </div>
          <div class="card-body">
            <table class="requests-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tasks as $task): ?>
                  <tr>
                    <td><?= htmlspecialchars($task['id']) ?></td>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['description']) ?></td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td>
                      <?php if ($task['status'] !== 'completed'): ?>
                        <form method="POST" style="display:inline;">
                          <input type="hidden" name="complete_id" value="<?= $task['id'] ?>">
                          <button type="submit" class="btn-primary btn-small">Mark Completed</button>
                        </form>
                      <?php else: ?>
                        <span class="badge badge-success">Done</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
