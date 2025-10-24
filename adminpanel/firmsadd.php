<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$error = '';
$success = '';
try {
    $stmt = $db->prepare('SELECT * FROM Bus_Company');
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}


if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM bus_company WHERE id=?");
        $stmt->execute([$id]);
        header("Location: firmsadd.php");
        exit();
    } catch (PDOException $e) {
        echo "Veritabanından silinirken bilinmeyen bir hata oluştu";
        exit();
    }
}

// Firma ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    if (!empty($name)) {
        try {
            $id = strtolower(bin2hex(random_bytes(16))); // Benzersiz firma ID
            $stmt = $db->prepare("INSERT INTO Bus_Company (id, name) VALUES (:id, :name)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $success = "Firma başarıyla veritabanına eklendi.";
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $error = "Firma adı boş bırakılamaz.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Firma Ekle</title>
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

    h1 {
      text-align: center;
      color: #333;
    }

    form {
      max-width: 400px;
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

    input[type="text"] {
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

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 40px;
    }

    th, td {
      border: 1px solid #ad0a0aff;
      padding: 6px 8px;
      text-align: left;
      font-size: 14px;
      white-space: nowrap;
    }

    th {
      background-color: yellowgreen;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .action-btn {
      padding: 5px 10px;
      text-decoration: none;
      color: black;
      border-radius: 3px;
    }

    .delete-btn {
      background-color: red;
    }

    .edit-btn {
      background-color: #2196F3;
    }
  </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
  <h2>Admin Paneli</h2>
  <a href="dashboard.php"> Ana Sayfa</a>
  <a href="firmsadd.php"> Firma Ekle</a>
  <a href="assign_company.php"> Firma Admini Ata</a>
  <a href="trips.php">Seferler</a>
  <a href="users.php"> Kullanıcıları Listele</a>
  <a href="logout.php"> Çıkış Yap</a>
</nav>

<div class="main">
  <h1>Yeni Firma Ekle</h1>

  <?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="name">Firma Adı:</label>
    <input type="text" id="name" name="name" required>
    <button type="submit">Ekle</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Firma Adı</th>
        <th>Oluşturulma Zamanı</th>
        <th>DÜZENLE / SİL</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($companies)): ?>
        <?php foreach ($companies as $company): ?>
          <tr>
            <td><?= htmlspecialchars($company['id']) ?></td>
            <td><?= htmlspecialchars($company['name']) ?></td>
            <td><?= htmlspecialchars($company['created_at']) ?></td>
            <td>
              <a href="firmsedit.php?id=<?= $company['id'] ?>" class="action-btn edit-btn">Düzenle</a>
              <a href="?action=delete&id=<?= $company['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Bu firmayı silmek istediğinize emin misiniz?')">Sil</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4">Sistemde firma bulunamadı.</td></tr>
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