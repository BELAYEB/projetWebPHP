<?php
session_start();
require_once '../config.php'; // This should define $pdo

// Fetch feedback data
$stmt = $pdo->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total feedbacks
$totalFeedbacks = count($feedbacks);

// Average rating
$avgStmt = $pdo->query("SELECT AVG(rating) AS avg_rating FROM feedback");
$avgRow = $avgStmt->fetch(PDO::FETCH_ASSOC);
$avgRating = round($avgRow['avg_rating'], 1);

// Ratings distribution
$distStmt = $pdo->query("SELECT rating, COUNT(*) AS count FROM feedback GROUP BY rating");
$distribution = [];
while ($row = $distStmt->fetch(PDO::FETCH_ASSOC)) {
    $distribution[$row['rating']] = $row['count'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Analytics</title>
  <link rel="stylesheet" href="admin-styles.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="dashboard-container">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <i class="fas fa-project-diagram logo-icon"></i>
      <h2>ContactFlow</h2>
    </div>
    <div class="sidebar-content">
      <ul class="sidebar-menu">
        <li><a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li><a href="admin-requests.html"><i class="fas fa-ticket-alt"></i><span>Service Requests</span></a></li>
        <li><a href="admin-tasks.php"><i class="fas fa-tasks"></i><span>Task Board</span></a></li>
        <li><a href="admin-clients.html"><i class="fas fa-users"></i><span>Clients</span></a></li>
        <li><a href="admin-members.php"><i class="fas fa-users"></i><span>Members</span></a></li>
        <li class="active"><a href="admin-analytics.php"><i class="fas fa-chart-line"></i><span>Analytics</span></a></li>
      </ul>
    </div>
    <div class="sidebar-footer">
      <button id="logoutBtn" class="btn-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></button>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <header class="content-header">
      <h1>Client Feedback Analytics</h1>
      <div class="user-profile">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User Avatar" />
        <span><?= $_SESSION['user_name'] ?? 'Admin User' ?></span>
      </div>
    </header>

    <div class="dashboard-stats">
      <div class="stat-card"><h3>Total Feedbacks</h3><p><?= $total_feedbacks ?></p></div>
      <div class="stat-card"><h3>Average Rating</h3><p><?= $average_rating ?>/5</p></div>
    </div>

    <div class="analytics-grid">
      <div class="analytics-card">
        <h3>Ratings Distribution</h3>
        <canvas id="ratingChart"></canvas>
      </div>
      <div class="analytics-card">
        <h3>Latest Feedback</h3>
        <?php foreach ($latest_feedbacks as $fb): ?>
          <div class="feedback-entry">
            <strong>Rating: <?= $fb['rating'] ?>/5</strong>
            <p><?= htmlspecialchars($fb['content']) ?></p>
            <small><i class="fas fa-clock"></i> <?= $fb['created_at'] ?></small>
            <hr/>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>

<!-- STYLES -->
<style>
body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background: #f9f9f9;
}
.dashboard-container {
  display: flex;
}
.sidebar {
  width: 220px;
  background: #1e293b;
  color: white;
  height: 100vh;
  display: flex;
  flex-direction: column;
}
.sidebar-header, .sidebar-footer {
  padding: 20px;
  background: #111827;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.sidebar-menu {
  list-style: none;
  padding: 0;
}
.sidebar-menu li {
  padding: 15px 20px;
}
.sidebar-menu li.active, .sidebar-menu li:hover {
  background: #334155;
}
.sidebar-menu li a {
  color: white;
  text-decoration: none;
  display: flex;
  gap: 10px;
}
.main-content {
  flex: 1;
  padding: 20px;
}
.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.dashboard-stats {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}
.stat-card {
  background: white;
  padding: 20px;
  flex: 1;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.analytics-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-top: 20px;
}
.analytics-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.feedback-entry {
  margin-bottom: 15px;
}
</style>

<!-- CHART SCRIPT -->
<script>
const ctx = document.getElementById('ratingChart').getContext('2d');
const ratingChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['1★', '2★', '3★', '4★', '5★'],
    datasets: [{
      label: 'Count',
      data: [<?= $rating_counts[1] ?>, <?= $rating_counts[2] ?>, <?= $rating_counts[3] ?>, <?= $rating_counts[4] ?>, <?= $rating_counts[5] ?>],
      backgroundColor: '#10b981',
      borderRadius: 4
    }]
  },
  options: {
    responsive: true,
    animation: {
      duration: 1000,
      easing: 'easeOutBounce'
    },
    scales: {
      y: {
        beginAtZero: true,
        stepSize: 1
      }
    }
  }
});
</script>
</body>
</html>
