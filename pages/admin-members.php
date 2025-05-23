<?php
session_start();

// Redirect if not admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "", [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Add member
if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'member')");
  $stmt->execute([$name, $email, $password]);
  header("Location: admin-members.php");
  exit;
}

// Update member
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $stmt = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=? AND role='member'");
  $stmt->execute([$name, $email, $id]);
  header("Location: admin-members.php");
  exit;
}

// Delete member
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='member'");
  $stmt->execute([$id]);
  header("Location: admin-members.php");
  exit;
}

// Fetch members
$members = $pdo->query("SELECT * FROM users WHERE role = 'member' ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

  <meta charset="UTF-8">
  <title>Gestion des membres</title>
  <style>
    body {
      font-family: Arial;
      padding: 20px;
      background: #f4f4f4;
    }

    h2 {
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    th,
    td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }

    form {
      background: white;
      padding: 15px;
      border: 1px solid #ccc;
    }

    input {
      width: 100%;
      padding: 8px;
      margin: 5px 0;
    }

    button {
      padding: 8px 12px;
    }

    .actions a {
      margin-right: 10px;
      text-decoration: none;
    }

    .edit {
      color: blue;
    }

    .delete {
      color: red;
    }
  </style>

  <meta charset="UTF-8" />
  <title>Membres - ContactFlow CRM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
          <li><a href="admin-requests.php"><i class="fas fa-ticket-alt"></i><span>Service Requests</span></a></li>
          <li><a href="admin-tasks.php"><i class="fas fa-tasks"></i><span>Task Board</span></a></li>
          <li><a href="admin-clients.php"><i class="fas fa-users"></i><span>Clients</span></a></li>
          <li class="active"><a href="admin-members.php"><i class="fas fa-users"></i><span>Members</span></a></li>
          <li><a href="admin-analytics.php"><i class="fas fa-chart-line"></i><span>Analytics</span></a></li>
        </ul>
      </div>
      <div class="sidebar-footer">
        <form action="logout.php" method="post">
          <button class="btn-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></button>
        </form>
      </div>
    </aside>


    <h2>👥 Gestion des membres — Admin : <?= htmlspecialchars($_SESSION['user_name']) ?></h2>

    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Créé le</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($members as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td><?= $m['created_at'] ?? 'N/A' ?></td>
            <td class="actions">
              <a href="?edit=<?= $m['id'] ?>" class="edit">Modifier</a>
              <a href="?delete=<?= $m['id'] ?>" class="delete"
                onclick="return confirm('Supprimer ce membre ?')">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php
    if (isset($_GET['edit'])):
      $edit_id = $_GET['edit'];
      $stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='member'");
      $stmt->execute([$edit_id]);
      $m = $stmt->fetch();
      if (!$m)
        exit("Membre introuvable.");
      ?>
      <h3>Modifier le membre</h3>
      <form method="post">
        <input type="hidden" name="id" value="<?= $m['id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($m['email']) ?>" required>
        <button type="submit" name="update">Mettre à jour</button>
        <a href="admin-members.php">Annuler</a>
      </form>
    <?php else: ?>
      <h3>Ajouter un nouveau membre</h3>
      <form method="post">
        <input type="text" name="name" placeholder="Nom complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="add">Ajouter</button>
      </form>
    <?php endif; ?>

    <main class="main-content">
      <header class="content-header">
        <div class="header-left">
          <h1>Gestion des membres</h1>
        </div>
        <div class="header-right">
          <div class="user-profile">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s"
              alt="User Avatar" />
            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
          </div>
        </div>
      </header>

      <div class="dashboard-content">
        <div class="dashboard-header">
          <h2>Membres</h2>
        </div>

        <div class="admin-dashboard-widgets">
          <div class="widget full-width">
            <div class="widget-header">
              <h3>Liste des membres</h3>
            </div>
            <div class="widget-content">
              <table class="styled-table">
                <thead>
                  <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($members as $m): ?>
                    <tr>
                      <td><?= htmlspecialchars($m['name']) ?></td>
                      <td><?= htmlspecialchars($m['email']) ?></td>
                      <td><?= $m['created_at'] ?? 'N/A' ?></td>
                      <td>
                        <a href="?edit=<?= $m['id'] ?>" class="btn-secondary">Update</a>
                        <a href="?delete=<?= $m['id'] ?>" class="btn-danger"
                          onclick="return confirm('Supprimer ce membre ?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>


          <div class="widget full-width">
            <div class="widget-header">
              <h3><?= isset($_GET['edit']) ? 'Modifier le membre' : 'Ajouter un membre' ?></h3>
            </div>
            <div class="widget-content">
              <?php if (isset($_GET['edit'])):
                $edit_id = $_GET['edit'];
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='member'");
                $stmt->execute([$edit_id]);
                $m = $stmt->fetch();
                if (!$m)
                  exit("Membre introuvable.");
                ?>
                <form method="post">
                  <input type="hidden" name="id" value="<?= $m['id'] ?>">
                  <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>" required>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($m['email']) ?>" required>
                  </div>
                  <button type="submit" name="update" class="btn-primary">Mettre à jour</button>
                  <a href="admin-members.php" class="btn-secondary">Annuler</a>
                </form>
              <?php else: ?>
                <form method="post">
                  <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="name" placeholder="Nom complet" required>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email" required>
                  </div>
                  <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Mot de passe" required>
                  </div>
                  <button type="submit" name="add" class="btn-primary">Ajouter</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/localStorage.js"></script>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/domUtils.js"></script>
</body>

</html>