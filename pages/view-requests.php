<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
session_start();
// Fetch the service requests
$stmt = $pdo->query("SELECT id, title,status, type,  priority, LEFT(created_at, 10) AS created_at FROM request");

?>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Requests - ContactFlow CRM</title>
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
          <li>
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
          <li class="active">
            <a href="view-requests.php">
              <i class="fas fa-list-alt"></i>
              <span>My Requests</span>
            </a>
          </li>
          <li class="active">
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
          <h1>My Service Requests</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s"
              alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= $_SESSION['user_name'] ?> </span>
          </div>
        </div>
      </header>

      <div class="content-body">
        <div class="card">
          <div class="card-header">
            <div class="card-header-left">
              <h2>All Requests</h2>
            </div>
            <div class="card-header-right">
              <div class="search-container">
                <input type="text" id="searchRequests" placeholder="Search requests..." />
                <i class="fas fa-search"></i>
              </div>
              <button id="newRequestBtn" class="btn-primary">
                <i class="fas fa-plus"></i> New Request
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="filter-bar">
              <div class="filter-group">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter">
                  <option value="all">All</option>
                  <option value="pending">Pending</option>
                  <option value="in-progress">In Progress</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div class="filter-group">
                <label for="typeFilter">Type:</label>
                <select id="typeFilter">
                  <option value="all">All</option>
                  <option value="support">Technical Support</option>
                  <option value="inquiry">Product Inquiry</option>
                  <option value="billing">Billing Issue</option>
                  <option value="feature">Feature Request</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="filter-group">
                <label for="priorityFilter">Priority:</label>
                <select id="priorityFilter">
                  <option value="all">All</option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
              <div class="filter-group">
                <label for="dateFilter">Date:</label>
                <select id="dateFilter">
                  <option value="all">All Time</option>
                  <option value="today">Today</option>
                  <option value="week">This Week</option>
                  <option value="month">This Month</option>
                  <option value="year">This Year</option>
                </select>
              </div>
              <button id="resetFiltersBtn" class="btn-text">
                <i class="fas fa-undo"></i> Reset
              </button>
            </div>

            <div class="requests-table-container">
              <table class="requests-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Priority</th>

                  </tr>
                </thead>
                <tbody id="requestsTableBody">
                  <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['id']) ?></td>
                      <td><?= htmlspecialchars($row['title']) ?></td>
                      <td><?= htmlspecialchars($row['type']) ?></td>
                      <td><?= htmlspecialchars($row['status']) ?></td>
                      <td><?= htmlspecialchars($row['priority']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>

            <div class="pagination">
              <button id="prevPageBtn" class="btn-icon" disabled>
                <i class="fas fa-chevron-left"></i>
              </button>
              <span id="paginationInfo">Page 1 of 1</span>
              <button id="nextPageBtn" class="btn-icon" disabled>
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div id="requestDetailsModal" class="modal">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <h2>Request Details</h2>
        <button class="close-modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="request-details">
          <div class="request-header">
            <h3 id="modalRequestTitle">Request Title</h3>
            <div class="request-meta">
              <span class="request-id">ID: <span id="modalRequestId">REQ-001</span></span>
              <span class="request-status" id="modalRequestStatus">Pending</span>
              <span class="request-priority" id="modalRequestPriority">Medium</span>
            </div>
          </div>

          <div class="request-info">
            <div class="info-group">
              <label>Type:</label>
              <span id="modalRequestType">Technical Support</span>
            </div>
            <div class="info-group">
              <label>Created:</label>
              <span id="modalRequestCreated">May 8, 2025</span>
            </div>
            <div class="info-group">
              <label>Last Updated:</label>
              <span id="modalRequestUpdated">May 8, 2025</span>
            </div>
          </div>

          <div class="request-description">
            <h4>Description</h4>
            <p id="modalRequestDescription">
              Request description will appear here.
            </p>
          </div>

          <div class="request-attachments" id="modalRequestAttachments">
            <h4>Attachments</h4>
            <div class="attachment-list" id="modalAttachmentList">
              &lt;!-- Attachments will be added dynamically -->
            </div>
          </div>

          <div class="request-comments">
            <h4>Comments</h4>
            <div class="comment-list" id="modalCommentList">
              &lt;!-- Comments will be added dynamically -->
            </div>
            <div class="add-comment">
              <textarea id="newComment" placeholder="Add a comment..."></textarea>
              <button id="addCommentBtn" class="btn-primary">
                Add Comment
              </button>
            </div>
          </div>

          <div id="requestFeedbackSection" class="request-feedback">
            <h4>Feedback</h4>
            <div id="feedbackForm">
              <div class="rating-container">
                <label>Rate our service:</label>
                <div class="star-rating" id="starRating">
                  <i class="far fa-star" data-rating="1"></i>
                  <i class="far fa-star" data-rating="2"></i>
                  <i class="far fa-star" data-rating="3"></i>
                  <i class="far fa-star" data-rating="4"></i>
                  <i class="far fa-star" data-rating="5"></i>
                </div>
              </div>
              <div class="form-group">
                <label for="feedbackText">Additional feedback:</label>
                <textarea id="feedbackText" placeholder="Tell us about your experience..."></textarea>
              </div>
              <button id="submitFeedbackBtn" class="btn-primary">
                Submit Feedback
              </button>
            </div>
            <div id="feedbackSubmitted" class="feedback-submitted">
              <div class="feedback-rating">
                <span>Your rating:</span>
                <div id="submittedRating" class="submitted-stars"></div>
              </div>
              <div class="feedback-text">
                <p id="submittedFeedbackText"></p>
              </div>
              <p class="feedback-thank-you">Thank you for your feedback!</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="modal-footer-left">
          <button id="generateInvoiceBtn" class="btn-secondary">
            <i class="fas fa-file-invoice"></i> Generate Invoice
          </button>
        </div>
        <div class="modal-footer-right">
          <button class="btn-secondary close-modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div id="notification" class="notification"></div>

  <script src="../assets/js/localStorage.js"></script>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/domUtils.js"></script>
  <script src="../assets/js/view-requests.js"></script>
</body>

</html>