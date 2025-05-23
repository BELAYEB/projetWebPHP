<?php
session_start();
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");

// Fetch all feedback data
$stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total feedbacks
$totalFeedbacks = count($feedbacks);

// Average rating
$avgStmt = $pdo->query("SELECT AVG(rating) AS average_rating FROM feedback");
$avgRating = round($avgStmt->fetch(PDO::FETCH_ASSOC)['average_rating'], 1);

// Ratings distribution
$distStmt = $pdo->query("SELECT rating, COUNT(*) AS count FROM feedback GROUP BY rating ORDER BY rating");
$ratingsData = [];
$fiveStarCount = 0;
while ($row = $distStmt->fetch(PDO::FETCH_ASSOC)) {
    $ratingsData[$row['rating']] = $row['count'];
    if ($row['rating'] == 5) {
        $fiveStarCount = $row['count'];
    }
}

// % of 5-star ratings
$fiveStarPercent = $totalFeedbacks > 0 ? round(($fiveStarCount / $totalFeedbacks) * 100, 1) : 0;

// Most recent feedback
$latestFeedback = $feedbacks[0] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Analytics - ContactFlow CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .admin-dashboard-widgets .card {
      background-color: #fff;
      padding: 1.2rem;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .card h3 {
      font-size: 1rem;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .card p {
      font-size: 1.8rem;
      font-weight: bold;
      color: #333;
    }

    .feedback-box {
      font-size: 0.9rem;
      background: #f9f9f9;
      border-left: 4px solid #2b7cff;
      padding: 0.8rem;
      border-radius: 8px;
    }

    canvas {
      max-width: 100%;
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
          <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
          <li><a href="admin-requests.php"><i class="fas fa-ticket-alt"></i><span>Service Requests</span></a></li>
          <li><a href="admin-tasks.php"><i class="fas fa-tasks"></i><span>Task Board</span></a></li>
          <li><a href="admin-clients.phpl"><i class="fas fa-users"></i><span>Clients</span></a></li>
          <li><a href="admin-members.php"><i class="fas fa-users"></i><span>Members</span></a></li>
          <li class="active"><a href="admin-analytics.php"><i class="fas fa-chart-line"></i><span>Analytics</span></a></li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <form action="logout.php" method="post">
          <button class="btn-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></button>
        </form>
      </div>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left">
          <h1>Feedback Analytics</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User Avatar" />
            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
          </div>
        </div>
      </header>

      <div class="dashboard-content">
        <div class="admin-dashboard-widgets" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
          <div class="card">
            <h3><i class="fas fa-comments"></i> Total Feedbacks</h3>
            <p><?= $totalFeedbacks ?></p>
          </div>

          <div class="card">
            <h3><i class="fas fa-star-half-alt"></i> Average Rating</h3>
            <p><?= $avgRating ?> / 5</p>
          </div>

          <div class="card">
            <h3><i class="fas fa-star"></i> 5-Star Ratings</h3>
            <p><?= $fiveStarPercent ?>%</p>
          </div>

          <?php if ($latestFeedback): ?>
            <div class="card" style="grid-column: span 2;">
              <h3><i class="fas fa-clock"></i> Most Recent Feedback</h3>
              <div class="feedback-box">
                <strong>From:</strong> <?= htmlspecialchars($latestFeedback['client_name'] ?? 'N/A') ?><br>
                <strong>Rating:</strong> <?= $latestFeedback['rating'] ?> / 5<br>
                <strong>Message:</strong><br><?= nl2br(htmlspecialchars($latestFeedback['comment'] ?? '')) ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="card" style="grid-column: span 2;">
            <h3><i class="fas fa-chart-bar"></i> Ratings Distribution</h3>
            <canvas id="ratingsChart" height="120"></canvas>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    const ratingsData = <?= json_encode(array_values($ratingsData)) ?>;
    const ratingLabels = <?= json_encode(array_keys($ratingsData)) ?>;

    const ctx = document.getElementById('ratingsChart').getContext('2d');
    const ratingsChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ratingLabels,
        datasets: [{
          label: 'Number of Ratings',
          data: ratingsData,
          backgroundColor: 'rgba(54, 162, 235, 0.7)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        },
        animation: {
          duration: 1000,
          easing: 'easeOutBounce'
        }
      }
    });
  </script>

  <script src="../assets/js/localStorage.js"></script>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/domUtils.js"></script>
</body>
</html>
