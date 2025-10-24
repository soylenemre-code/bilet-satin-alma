<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /index.php");
    exit();
}

try {
    $stmt = $db->prepare("SELECT * FROM User WHERE role = 'user'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: useradd.php");
    exit();
}

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM User WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        echo "Veritabanından silinirken bilinmeyen bir hata oluştu";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Listesi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        #toggleBtn {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: -220px;
            width: 220px;
            height: 100%;
            background-color: #2c3e50;
            padding: 30px 20px;
            box-sizing: border-box;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        #sidebar.active {
            left: 0;
        }

        #sidebar h2 {
            color: white;
            font-size: 20px;
            margin-bottom: 30px;
        }

        #sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: bold;
        }

        #sidebar a:hover {
            text-decoration: underline;
        }

        .main {
            margin-left: 0;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        #sidebar.active ~ .main {
            margin-left: 220px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        th, td {
            border: 1px solid #ad0a0aff;
            padding: 8px;
            text-align: left;
            font-size: 15px;
        }

        th {
            background-color: yellowgreen;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .action-btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            font-size: 14px;
        }

        .delete-btn {
            background-color: red;
        }

        .edit-btn {
            background-color: #2196F3;
        }

        .new-trip-btn {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 15px;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        @media (max-width: 480px) {
            th, td {
                font-size: 13px;
                padding: 6px;
            }

            .new-trip-btn {
                font-size: 13px;
                padding: 8px 12px;
            }

            #toggleBtn {
                padding: 8px 12px;
                font-size: 14px;
            }

            #sidebar h2 {
                font-size: 18px;
            }

            #sidebar a {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
    <h2>Admin Paneli</h2>
    <a href="dashboard.php">Ana Sayfa</a>
    <a href="users.php">Kullanıcılar</a>
    <a href="admins.php">Adminler</a>
    <a href="useradd.php">Yeni Kullanıcı Ekle</a>
    <a href="trips.php">Seferler</a>
    <a href="firmsadd.php">Firma Ekle</a>
    <a href="logout.php">Çıkış Yap</a>
</nav>

<div class="main">
    <h1>KULLANICI LİSTESİ</h1>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <button type="submit" class="new-trip-btn">Yeni Kullanıcı Ekle</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>İSİM-SOYİSİM</th>
                <th>Mail Adresi</th>
                <th>Şifre (hash)</th>
                <th>Rol</th>
                <th>Bakiyesi</th>
                <th>Eklenme Tarihi</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['password']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['balance']) ?> ₺</td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
                            <a href="useredit.php?id=<?= $user['id'] ?>" class="action-btn edit-btn">Düzenle</a>
                            <a href="?action=delete&id=<?= $user['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Veritabanında kullanıcı bulunamadı.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    const toggleBtn = document.getElementById('toggleBtn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
</script>

</body>
</html>
