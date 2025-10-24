<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

$error = '';
$success = '';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'firmadmin') {
    header("Location: /index.php");
    exit();
}


if (!isset($_SESSION['company_id'])) {
    echo "<p style='color:red;'>Firma oturumu bulunamadı. Lütfen giriş yapınız.</p>";
    exit();
}

$company_id = $_SESSION['company_id'];


try {
    $stmt = $db->prepare('SELECT * FROM trips WHERE company_id = ?');
    $stmt->execute([$company_id]);
    $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}


if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $check = $db->prepare("SELECT company_id FROM trips WHERE id = ?");
    $check->execute([$id]);
    $trip = $check->fetch();

    if ($trip && $trip['company_id'] == $company_id) {
        try {
            $stmt = $db->prepare("DELETE FROM trips WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: trips.php");
            exit();
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Sefer silinirken hata oluştu.</p>";
            exit();
        }
    } else {
        echo "<p style='color:red;'>Bu seferi silme yetkiniz yok.</p>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: tripadd.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Otobüs Seferleri</title>
  <style>
    body {
      font-family: sans-serif;
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
    }

    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #4caf50;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .action-btn {
      padding: 5px 10px;
      text-decoration: none;
      border-radius: 3px;
    }

    .edit-btn {
      background-color: #2196F3;
      color: white;
    }

    .delete-btn {
      background-color: red;
      color: white;
    }

    .new-trip-btn {
      background-color: #4caf50;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <button id="toggleBtn">☰ Menü</button>

  <nav id="sidebar">
    <h2>Admin Paneli</h2>
    <a href="index.php">Ana Sayfa</a>
    <a href="trips.php"> SEFERLER</a>
    <a href="coupons.php">Kuponlar</a>
    <a href="users.php"> Kullanıcıları Listele</a>
    <a href="logout.php"> Çıkış Yap</a>
  </nav>

  <div class="main">
    <h1>OTOBÜS SEFERLERİ</h1>

    <?php if ($error): ?>
      <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
      <button type="submit" class="new-trip-btn">YENİ SEFER OLUŞTUR</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Kalkış Noktası</th>
          <th>Varış Noktası</th>
          <th>Varış Zamanı</th>
          <th>Kalkış Zamanı</th>
          <th>Fiyat</th>
          <th>Koltuk Sayısı</th>
          <th>Oluşturulma Zamanı</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($trips)): ?>
          <?php foreach ($trips as $trip): ?>
            <tr>
              <td><?= htmlspecialchars($trip['id']) ?></td>
              <td><?= htmlspecialchars($trip['departure_city']) ?></td>
              <td><?= htmlspecialchars($trip['destination_city']) ?></td>
              <td><?= htmlspecialchars($trip['arrival_time']) ?></td>
              <td><?= htmlspecialchars($trip['departure_time']) ?></td>
              <td><?= htmlspecialchars($trip['price']) ?></td>
              <td><?= htmlspecialchars($trip['capacity']) ?></td>
              <td><?= htmlspecialchars($trip['created_date']) ?></td>
              <td>
                <a href="tripsedit.php?id=<?= $trip['id'] ?>" class="action-btn edit-btn">Düzenle</a>
                <a href="?action=delete&id=<?= $trip['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Bu seferi silmek istediğinizden emin misiniz?')">Sil</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9">Henüz sefer bulunmamaktadır.</td></tr>
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
