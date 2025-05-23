<?php
require_once 'config.php';
$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "");
$stmt = $pdo->query("SELECT id, title from task where status='completed'");
$stmt2 = $pdo->query("SELECT id, title from task where status='in progress'");

$stmtinprogress = $pdo->query("SELECT * FROM task WHERE status='in progress'");
$tasksinprogress = $stmtinprogress->rowCount();

$stmticompleted = $pdo->query("SELECT * FROM task WHERE status='completed'");
$taskscompleted = $stmticompleted->rowCount();
?>

<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Task Board - ContactFlow CRM</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .modal-content {
            background: white;
            padding: 20px;
            width: 400px;
            border-radius: 6px;
            position: relative;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            position: absolute;
            right: 15px;
            top: 10px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            color: white;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 4px;
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
                        <a href="admin-dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                 
                    <li class="active">
                        <a href="admin-tasks.php">
                            <i class="fas fa-tasks"></i>
                            <span>Task Board</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin-client.php">
                            <i class="fas fa-users"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                              <li><a href="admin-members.php"><i class="fas fa-users"></i><span>Members</span></a></li>

                    <li>
                        <a href="admin-analytics.php">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
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
                    <h1>Task Board</h1>
                </div>
                <div class="header-right" style="display:flex; align-items:center; gap: 15px;">
                    <button id="addTaskBtn" class="btn-primary">Ajouter Task</button>

                    <div class="user-profile" style="display:flex; align-items:center; gap:10px;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s"
                            alt="User Avatar" id="userAvatar"
                            style="width:40px; height:40px; border-radius:50%; object-fit:cover;" />
                        <span id="userName"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    </div>
                </div>
            </header>

            <div class="content-body">
                <div class="kanban-board" id="kanbanBoard">
                    <div class="kanban-column" data-status="in-progress">
                        <div class="column-header">
                            <h3>In Progress</h3>
                            <span class="task-count" id="inProgressCount"><?= $tasksinprogress ?></span>
                        </div>
                        <div class="column-content" id="inProgressTasks">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="kanban-column" data-status="completed">
                        <div class="column-header">
                            <h3>Completed</h3>

                            <span class="task-count" id="completedCount"><?= $taskscompleted ?></span>
                        </div>
                        <div class="column-content" id="completedTasks">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                    </tr>
                                </thead>
                                <tbody id="requestsTableBody">
                                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>



                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajouter Nouvelle Task</h2>
                <button id="closeAddTaskModal" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm" method="post" action="ajouter_task.php">
                    <div class="form-group">
                        <label for="taskTitle">Title</label>
                        <input type="text" id="taskTitle" name="title" required />
                    </div>
                    <div class="form-group">
                        <label for="taskDescription">Description</label>
                        <textarea id="taskDescription" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="taskAssignee">Assigned To</label>
                        <select id="taskAssignee" name="assigned_to" required>
                            <option value="">Select User</option>
                            <?php
                            $usersStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'member'");
                            $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($users as $user) {
                                echo '<option value="' . htmlspecialchars($user['id']) . '">' . htmlspecialchars($user['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelAddTask" class="btn-secondary">Cancel</button>
                <button id="submitAddTask" class="btn-primary">Save Task</button>
            </div>
        </div>
    </div>

    <script>
        const addTaskBtn = document.getElementById('addTaskBtn');
        const addTaskModal = document.getElementById('addTaskModal');
        const closeAddTaskModalBtn = document.getElementById('closeAddTaskModal');
        const cancelAddTaskBtn = document.getElementById('cancelAddTask');
        const submitAddTaskBtn = document.getElementById('submitAddTask');
        const addTaskForm = document.getElementById('addTaskForm');

        function openModal() {
            addTaskModal.style.display = 'flex';
        }

        function closeModal() {
            addTaskModal.style.display = 'none';
            addTaskForm.reset();
        }

        addTaskBtn.addEventListener('click', openModal);
        closeAddTaskModalBtn.addEventListener('click', closeModal);
        cancelAddTaskBtn.addEventListener('click', closeModal);

        submitAddTaskBtn.addEventListener('click', () => {
            if (!addTaskForm.reportValidity()) return;

            const formData = new FormData(addTaskForm);

            fetch('save_task.php', {
                method: 'POST',
                body: formData
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        alert('Task saved successfully!');
                        closeModal();
                        // You can add code here to update the task board dynamically
                    } else {
                        alert('Failed to save task: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(() => alert('Error saving task.'));
        });
    </script>
</body>

</html>