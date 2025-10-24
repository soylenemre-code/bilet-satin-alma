<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Firma listesi
$companies = $db->query("SELECT id, name FROM Bus_Company")->fetchAll(PDO::FETCH_ASSOC);

// Firma Admin listesi
$users = $db->query("SELECT id, full_name, email, company_id FROM User WHERE role = 'firmadmin'")->fetchAll(PDO::FETCH_ASSOC);

// Firma atama işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $company_id = $_POST['company_id'] ?? null;

    if ($user_id && $company_id) {
        $stmt = $db->prepare("UPDATE User SET company_id = ? WHERE id = ?");
        $stmt->execute([$company_id, $user_id]);
        $success = "Firma başarıyla atandı.";
    } else {
        $error = "Lütfen kullanıcı ve firma seçiniz.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Firma Admini Tanımlama</title>
  
  <style>
    body {
      font-family: Arial;
      margin: 0;
      padding: 0;
      background: #f4f4f4;
    }

    #toggleBtn {
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1000;
      background: #333;
      color: white;
      border: none;
      padding: 10px 15px;
      font-size: 18px;
      cursor: pointer;
      border-radius: 4px;
    }

    #sidebar {
      position: fixed;
      top: 0;
      left: -250px;
      width: 250px;
      height: 100%;
      background: #2c3e50;
      color: white;
      padding: 50px;
      transition: left 0.3s ease;
    }

    #sidebar.active {
      left: 0;
    }

    #sidebar h2 {
      margin-top: 0;
      font-size: 20px;
      margin-bottom: 20px;
    }

    #sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      margin-bottom: 10px;
      font-size: 16px;
    }

    .main {
      margin-left: 0;
      padding: 30px;
    }

    h2 {
      margin-top: 60px;
      text-align: center;
    }

    form {
      max-width: 500px;
      margin: 20px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    select, input[type="text"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button[type="submit"] {
      background: #4caf50;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .message {
      text-align: center;
      font-size: 16px;
      margin: 10px 0;
    }

    .success { color: green; }
    .error { color: red; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
      font-size: 14px;
    }

    th {
      background-color: yellowgreen;
    }

    .action-btn {
      padding: 5px 10px;
      text-decoration: none;
      color: white;
      border-radius: 4px;
      font-weight: bold;
    }

    .edit-btn { background-color: #2196F3; }
    .delete-btn { background-color: red; }
  </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
  <h2>Admin Paneli</h2>
  <a href="dashboard.php"> Ana Sayfa</a>
  <a href="firmsadd.php"> Firma Ekle</a>
  <a href="assign_company.php"> Firma Admini Ata</a>
  <a href="admins.php">Adminleri Görüntüle</a>
  <a href="coupons.php">Kuponlar</a>
  <a href="trips.php"> Seferler</a>
  <a href="users.php"> Kullanıcıları Listele</a>
  <a href="profile.php">Profili Düzenle</a>
  <a href="logout.php"> Çıkış Yap</a>
</nav>

<div class="main">
  <h2>Firma Admini Tanımlama</h2>

  <?php if ($error): ?><p class="message error"><?= $error ?></p><?php endif; ?>
  <?php if ($success): ?><p class="message success"><?= $success ?></p><?php endif; ?>

  <form method="POST">
    <label for="user_id">Firma Admin Seç:</label>
    <select name="user_id" required>
      <option disabled selected>Kullanıcı seçiniz</option>
      <?php foreach ($users as $user): ?>
        <option value="<?= $user['id'] ?>">
          <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['email']) ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <label for="company_id">Firma Seç:</label>
    <select name="company_id" required>
      <option disabled selected>Firma seçiniz</option>
      <?php foreach ($companies as $company): ?>
        <option value="<?= $company['id'] ?>">
          <?= htmlspecialchars($company['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit">Atamayı Kaydet</button>
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


