<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

$userid = $_SESSION['user_id'];
$error = "";
$success = "";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /index.php");
    exit();
}

try {
    $stmt = $db->prepare("SELECT full_name,email FROM User WHERE id=?");
    $stmt->execute([$userid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanına Bağlanırken beklenmedik hata meydana geldi";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($full_name) || empty($email)) {
        $error = "Ad ve e-posta boş bırakılamaz.";
    } else {
        try {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE User SET full_name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $hashed_password, $userid]);
            } else {
                $stmt = $db->prepare("UPDATE User SET full_name = ?, email = ? WHERE id = ?");
                $stmt->execute([$full_name, $email, $userid]);
            }
            $success = "Bilgiler başarıyla güncellendi.";
            $_SESSION['email'] = $email;
        } catch (PDOException $e) {
            $error = "Güncelleme hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Siber Vatan Otobüs Bilet Satış Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
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
            padding: 40px;
            transition: margin-left 0.3s ease;
        }

        #sidebar.active ~ .main {
            margin-left: 220px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
    <h2>Admin Paneli</h2>
    <a href="dashboard.php">Ana Sayfa</a>
    <a href="firmsadd.php">Firma Ekle</a>
    <a href="coupons.php">Kuponlar</a>
    <a href="trips.php">Seferler</a>
    <a href="assign_company.php">Firma Admini Ata</a>
    <a href="users.php">Kullanıcıları Listele</a>
    <a href="admins.php">Adminleri Görüntüle</a>
    <a href="profile.php">Profili Düzenle</a>
    <a href="logout.php">Çıkış Yap</a>
</nav>

<div class="main">
    <h1>Profil Bilgilerim</h1>

    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="full_name">Ad Soyad:</label>
        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>

        <label for="email">E-posta:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

        <label for="password">Yeni Şifre (değiştirmek istemiyorsanız boş bırakabilirsiniz):</label>
        <input type="password" id="password" name="password">

        <button type="submit">Güncelle</button>
    </form>
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
