<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

$full_name = $email = $password = $role = '';
$error = '';
$success = '';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!$full_name || !$email || !$password || !$role) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $id = strtolower(bin2hex(random_bytes(16)));

            $stmt = $db->prepare("INSERT INTO User (id, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $full_name, $email, $hashed_password, $role]);

            $success = "Kayıt başarılı!";
        } catch (PDOException $e) {
            $error = "Kayıt sırasında hata oluştu: Bu kullanıcı adı veya parola kayıtlı.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Yeni Kullanıcı Ekle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
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
      transition: margin-left 0.3s ease;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      flex-direction: column;
    }

    #sidebar.active ~ .main {
      margin-left: 220px;
    }

    form {
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    h1 {
      margin-bottom: 20px;
      color: #2c3e50;
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    input[type="text"], input[type="email"], input[type="password"], select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      box-sizing: border-box;
    }

    button {
      margin-top: 15px;
      padding: 10px 20px;
      background-color: #4caf50;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 4px;
      width: 100%;
    }

    .message {
      margin-bottom: 15px;
      font-weight: bold;
    }

    .error { color: red; }
    .success { color: green; }

    @media (max-width: 480px) {
      form { padding: 20px; }
      h1 { font-size: 20px; }
      #toggleBtn { padding: 8px 12px; font-size: 14px; }
      #sidebar h2 { font-size: 18px; }
      #sidebar a { font-size: 14px; }
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
    <a href="profile.php">Profili Düzenle</a>
    <a href="logout.php">Çıkış Yap</a>
</nav>
<div class="main">
  <h1>Kayıt Ol</h1>

  <?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Ad Soyad (Firma ise Firma Adı Giriniz!)</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>">

    <label>E-posta</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">

    <label>Şifre</label>
    <input type="password" name="password">

    <label>Rol</label>
    <select name="role">
      <option value="user">Yolcu</option>
      <option value="firmadmin">Firma Admin</option>
      <option value="admin">Admin</option>
    </select>

    <button type="submit">Kayıt Ol</button>
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
