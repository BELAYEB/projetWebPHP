<?php
session_start();

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
$editing = isset($_GET['edit']);
$edit_data = null;

if ($editing) {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='member'");
  $stmt->execute([$_GET['edit']]);
  $edit_data = $stmt->fetch();
  if (!$edit_data) exit("Membre introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ContactFlow CRM - Membres</title>
    <link rel="stylesheet" href="../assets/css/style.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f2f3f7;
      color: #333;
    }

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }



    .main-content {
      flex: 1;
      padding: 20px;
    }

    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-profile img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
    }

    .admin-dashboard-widgets {
      display: grid;
      grid-template-columns: 1fr;
      gap: 30px;
    }

    .widget {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .widget h3 {
      margin-top: 0;
      font-size: 20px;
      margin-bottom: 15px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .btn-primary {
      background: #202a44;
      color: white;
      border: none;
      padding: 10px 16px;
      cursor: pointer;
      border-radius: 4px;
    }

    .btn-danger {
      background: #d9534f;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
    }

    .btn-secondary {
      background: #6c757d;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }

    .form-group input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    @media (min-width: 768px) {
      .admin-dashboard-widgets {
        grid-template-columns: 2fr 1fr;
      }
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
      <ul class="sidebar-menu">
        <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="admin-tasks.php"><i class="fas fa-tasks"></i> Task Board</a></li>
        <li><a href="admin-clients.php"><i class="fas fa-users"></i> Clients</a></li>
        <li class="active"><a href="admin-members.php"><i class="fas fa-users"></i> Membres</a></li>
        <li><a href="admin-analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
      </ul>
    </aside>

    <main class="main-content">
      <header class="content-header">
        <h1>👥 Gestion des membres</h1>
        <div class="user-profile">
          <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzXq5qGKw0V-doQphkM0sAEemGQG0SU6l6ww&s" alt="Avatar">
          <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
        </div>
      </header>

      <div class="admin-dashboard-widgets">

        <div class="widget">
          <h3>Liste des membres</h3>
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
                  <td>
                    <a href="?edit=<?= $m['id'] ?>" class="btn-secondary">Modifier</a>
                    <a href="?delete=<?= $m['id'] ?>" class="btn-danger" onclick="return confirm('Supprimer ce membre ?')">Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="widget">
          <h3><?= $editing ? 'Modifier le membre' : 'Ajouter un membre' ?></h3>
          <form method="post">
            <?php if ($editing): ?>
              <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            <div class="form-group">
              <label>Nom</label>
              <input type="text" name="name" value="<?= $editing ? htmlspecialchars($edit_data['name']) : '' ?>" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" value="<?= $editing ? htmlspecialchars($edit_data['email']) : '' ?>" required>
            </div>
            <?php if (!$editing): ?>
              <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
              </div>
            <?php endif; ?>
            <button type="submit" name="<?= $editing ? 'update' : 'add' ?>" class="btn-primary">
              <?= $editing ? 'Mettre à jour' : 'Ajouter' ?>
            </button>
            <?php if ($editing): ?>
              <a href="admin-members.php" class="btn-secondary">Annuler</a>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
