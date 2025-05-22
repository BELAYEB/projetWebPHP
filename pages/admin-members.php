<?php
session_start();

// Vérification admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=support_system", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Créer un membre
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'member')");
    $stmt->execute([$name, $email, $password]);
    header("Location: admin-members.php");
    exit;
}

// Mettre à jour
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=? AND role='member'");
    $stmt->execute([$name, $email, $id]);
    header("Location: admin-members.php");
    exit;
}

// Supprimer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='member'");
    $stmt->execute([$id]);
    header("Location: admin-members.php");
    exit;
}

// Liste des membres
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
</head>

<body>

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

</body>

</html>