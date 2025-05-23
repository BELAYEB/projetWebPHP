<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();
// Fetch the service requests
$stmt = $pdo->query("SELECT id,title,description,status from task");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Tasks - ContactFlow CRM</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
    }

    thead {
      background-color: #f8f9fa;
    }

    th,
    td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }

    .pagination i {
      margin: 0 10px;
      cursor: pointer;
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
          <li>
            <a href="member-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="active">
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
          <h1>My Tasks</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="../assets/images/avatar.png" alt="Employee Avatar" id="userAvatar" />
            <span id="userName">Employee</span>
          </div>
        </div>
      </header>

      <div class="dashboard-content">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Type</th>
              <th>Status</th>
              <th>action</th>


            </tr>
          </thead>
          <tbody id="requestsTableBody">
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                  <form method="post" style="margin:0;" action="update_status.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                    <button type="submit" name="complete">Completed</button>
                  </form>

                </td>


              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div class="pagination">
          <i class="fas fa-chevron-left"></i>
          <span>Page 1 of 1</span>
          <i class="fas fa-chevron-right"></i>
        </div>
      </div>
    </main>
  </div>
</body>
<script>
  document.querySelectorAll('.complete-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');

      fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Optionally update the status cell in the row
            button.closest('tr').querySelector('td:nth-child(4)').textContent = 'completed';
            // Disable the button after completion to prevent multiple clicks
            button.disabled = true;
            button.textContent = 'Completed';
          } else {
            alert('Failed to update status');
          }
        })
        .catch(() => alert('Error in request'));
    });
  });
</script>

</html>