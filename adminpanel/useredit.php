<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    echo "Geçersiz kullanıcı ID.";
    exit();
}

$stmt = $db->prepare("SELECT * FROM User WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Kullanıcı bulunamadı.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $balance = $_POST['balance'];

    $update = $db->prepare("UPDATE User SET full_name=?, email=?, balance=? WHERE id=?");
    $update->execute([$full_name, $email, $balance, $user_id]);

    echo "<p style='color:green;'>Kullanıcı bilgileri güncellendi.</p>";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kullanıcıyı Düzenle</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background-color: #f4f4f4;
      padding: 30px;
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
      transition: margin-left 0.3s ease;
    }

    #sidebar.active ~ .main {
      margin-left: 220px;
    }

    h2 { color: #2c3e50; }
    form { margin-bottom: 40px; max-width: 500px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input[type="text"], input[type="number"], input[type="email"] {
      width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;
    }
    button {
      margin-top: 15px; padding: 10px 20px;
      background-color: #4caf50; color: white;
      border: none; cursor: pointer; border-radius: 4px;
    }
  </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
  <h2>Admin Paneli</h2>
  <a href="dashboard.php">Ana Sayfa</a>
  <a href="users.php">Kullanıcılar</a>
  <a href="useradd.php">Yeni Kullanıcı Ekle</a>
  <a href="trips.php">Seferler</a>
  <a href="firmsadd.php">Firma Ekle</a>
  <a href="logout.php">Çıkış Yap</a>
</nav>

<div class="main">
  <h2>Kullanıcı Bilgilerini Güncelle</h2>
  <form method="POST">
    <label>İsim Soyisim:</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>">

    <label>Mail Adresi:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

    <label>Bakiye:</label>
    <input type="number" name="balance" value="<?= htmlspecialchars($user['balance']) ?>">

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
