<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_submit'])) {
  $content = trim($_POST['content'] ?? '');
  $rating = intval($_POST['rating'] ?? 0);
  $user_id = $_SESSION['user_id'] ?? null;

  if (!$user_id) {
    $error = "User not logged in.";
  } else {
    // Get the latest request ID made by the current user
    $stmt = $pdo->prepare("SELECT id FROM request WHERE client_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($content && $rating >= 1 && $rating <= 5 && $request) {
      $request_id = $request['id'];

      // Check if feedback already exists for this request
      $checkStmt = $pdo->prepare("SELECT id FROM feedback WHERE request_id = ?");
      $checkStmt->execute([$request_id]);
      $existingFeedback = $checkStmt->fetch();

      if ($existingFeedback) {
        $error = "Feedback already submitted for your latest request.";
      } else {
        // Insert feedback (created_at will auto populate if set as DEFAULT CURRENT_TIMESTAMP)
        $stmt = $pdo->prepare("INSERT INTO feedback (content, rating, user_id, request_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$content, $rating, $user_id, $request_id]);

        $message = "Feedback submitted successfully!";
      }
    } else {
      $error = "All fields are required and rating must be between 1 and 5.";
    }
  }
}
?>
    

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Client Dashboard - Feedback</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .feedback-section {
      background: #fff;
      padding: 20px;
      margin: 30px auto;
      border-radius: 10px;
      max-width: 600px;
    }
    .feedback-section input,
    .feedback-section textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .feedback-section button {
      background-color: #3498db;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .feedback-section button:hover {
      background-color: #2980b9;
    }
    .message {
      color: green;
    }
    .error {
      color: red;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <div class="sidebar-header">
        <i class="fas fa-project-diagram logo-icon"></i>
        <h2>ContactFlow</h2>
      </div>
      <div class="sidebar-content">
        <ul class="sidebar-menu">
          <li class=""><a href="client-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="request-service.html"><i class="fas fa-plus-circle"></i> New Request</a></li>
          <li><a href="view-requests.php"><i class="fas fa-list-alt"></i> My Requests</a></li>
          <li class="active"><a href="client-feedback.php><i class="fas fa-comment-dots"></i> Feedback</a></li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <a class="btn-logout" href="login.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left">
          <h1>Leave Feedback</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User" />
            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
          </div>*
        </div>
      </header>

      <section class="feedback-section">
        <?php if (!empty($message)): ?>
          <p class="message"><?= $message ?></p>
        <?php elseif (!empty($error)): ?>
          <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
          <label for="rating">Rating (1 to 5)</label>
          <input type="number" name="rating" id="rating" min="1" max="5" required />

          <label for="content">Feedback</label>
          <textarea name="content" id="content" rows="4" required></textarea>

          <button type="submit" name="feedback_submit">Submit Feedback</button>
        </form>
      </section>
    </main>
  </div>
</body>

</html>
