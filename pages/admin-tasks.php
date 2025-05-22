<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");


$inprogressStmt = $pdo->query("SELECT * FROM task WHERE status = 'in progress'");
$totaltasksinprogress = $inprogressStmt->rowCount();

$todoStmt = $pdo->query("SELECT * FROM task WHERE status = 'to do'");
$totaltaskstodo = $todoStmt->rowCount();

$reviewStmt = $pdo->query("SELECT * FROM task WHERE status = 'review'");
$reviw = $reviewStmt->rowCount();

$completedStmt = $pdo->query("SELECT * FROM task WHERE status = 'completed'");
$completed = $completedStmt->rowCount();
session_start()

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Board - ContactFlow CRM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <a href="admin-dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin-requests.html">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Service Requests</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="admin-tasks.html">
                            <i class="fas fa-tasks"></i>
                            <span>Task Board</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin-clients.html">
                            <i class="fas fa-users"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin-analytics.html">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="settings.html">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="sidebar-footer">
                <button id="logoutBtn" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <div class="header-left">
                    <h1>Task Board</h1>
                </div>
              <div class="header-right">
             <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="User Avatar" id="userAvatar" />
            <span id="userName"><?= $_SESSION['user_name'] ?> </span>
          </div>
            </header>

            <div class="content-body">
                <div class="task-board-header">
                    <div class="task-board-filters">
                        <div class="filter-group">
                            <label for="taskFilter">Filter:</label>
                            <select id="taskFilter">
                                <option value="all">All Tasks</option>
                                <option value="my-tasks">My Tasks</option>
                                <option value="high-priority">High Priority</option>
                                <option value="due-soon">Due Soon</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="taskSearch">Search:</label>
                            <div class="search-container">
                                <input type="text" id="taskSearch" placeholder="Search tasks...">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="kanban-board" id="kanbanBoard">
                    <div class="kanban-column" data-status="todo">
                        <div class="column-header">
                            <h3>To Do</h3>
                            <span class="task-count" id="todoCount"><?= $totaltaskstodo ?></span>
                        </div>
                        <div class="column-content" id="todoTasks">
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <p>No tasks to do</p>
                            </div>
                        </div>
                    </div>

                    <div class="kanban-column" data-status="in-progress">
                        <div class="column-header">
                            <h3>In Progress</h3>
                            <span class="task-count" id="inProgressCount"><?= $totaltasksinprogress ?></span>
                        </div>
                        <div class="column-content" id="inProgressTasks">
                            <div class="empty-state">
                                <i class="fas fa-spinner"></i>
                                <p>No tasks in progress</p>
                            </div>
                        </div>
                    </div>

                    <div class="kanban-column" data-status="review">
                        <div class="column-header">
                            <h3>Review</h3>
                            <span class="task-count" id="reviewCount"><?= $reviw ?></span>
                        </div>
                        <div class="column-content" id="reviewTasks">
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <p>No tasks in review</p>
                            </div>
                        </div>
                    </div>

                    <div class="kanban-column" data-status="completed">
                        <div class="column-header">
                            <h3>Completed</h3>
                            <span class="task-count" id="completedCount">0</span>
                        </div>
                        <div class="column-content" id="completedTasks">
                            <div class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <p><?= $completed ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    &lt;!-- Create Task Modal -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="taskModalTitle">Create New Task</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <input type="hidden" id="taskId">
                    <div class="form-group">
                        <label for="taskTitle">Task Title</label>
                        <input type="text" id="taskTitle" name="taskTitle" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="taskStatus">Status</label>
                            <select id="taskStatus" name="taskStatus" required>
                                <option value="todo">To Do</option>
                                <option value="in-progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="taskPriority">Priority</label>
                            <select id="taskPriority" name="taskPriority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="taskAssignee">Assignee</label>
                            <select id="taskAssignee" name="taskAssignee">
                                <option value="">Unassigned</option>
                                &lt;!-- Team members will be added dynamically -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="taskDueDate">Due Date</label>
                            <input type="date" id="taskDueDate" name="taskDueDate">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="relatedRequest">Related Service Request</label>
                        <select id="relatedRequest" name="relatedRequest">
                            <option value="">None</option>
                            &lt;!-- Service requests will be added dynamically -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="taskDescription">Description</label>
                        <textarea id="taskDescription" name="taskDescription" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary close-modal">Cancel</button>
                <button id="saveTaskBtn" class="btn-primary">Save Task</button>
            </div>
        </div>
    </div>

    &lt;!-- Task Details Modal -->
    <div id="taskDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Task Details</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="task-details">
                    <div class="task-header">
                        <h3 id="detailsTaskTitle">Task Title</h3>
                        <div class="task-meta">
                            <span class="task-id">ID: <span id="detailsTaskId">TASK-001</span></span>
                            <span class="task-status" id="detailsTaskStatus">To Do</span>
                            <span class="task-priority" id="detailsTaskPriority">Medium</span>
                        </div>
                    </div>

                    <div class="task-info">
                        <div class="info-group">
                            <label>Assignee:</label>
                            <span id="detailsTaskAssignee">Unassigned</span>
                        </div>
                        <div class="info-group">
                            <label>Due Date:</label>
                            <span id="detailsTaskDueDate">Not set</span>
                        </div>
                        <div class="info-group">
                            <label>Created:</label>
                            <span id="detailsTaskCreated">May 8, 2025</span>
                        </div>
                        <div class="info-group">
                            <label>Related Request:</label>
                            <span id="detailsRelatedRequest">None</span>
                        </div>
                    </div>

                    <div class="task-description">
                        <h4>Description</h4>
                        <p id="detailsTaskDescription">Task description will appear here.</p>
                    </div>

                    <div class="task-activity">
                        <h4>Activity</h4>
                        <div class="activity-list" id="taskActivityList">
                            &lt;!-- Activity items will be added dynamically -->
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                <p>No activity recorded</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="editTaskBtn" class="btn-secondary">Edit Task</button>
                <button id="deleteTaskBtn" class="btn-danger">Delete Task</button>
            </div>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <script src="../assets/js/localStorage.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/domUtils.js"></script>
    <script src="../assets/js/admin-tasks.js"></script>
</body>

</html>