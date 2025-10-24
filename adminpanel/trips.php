<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /index.php");
    exit();
}

try {
    $stmt = $db->prepare('
        SELECT trips.*, bus_company.name AS company_name 
        FROM trips 
        LEFT JOIN bus_company ON trips.company_id = bus_company.id
    ');
    $stmt->execute();
    $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header("Location: tripsadd.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM trips WHERE id=?");
        $stmt->execute([$id]);
        header("Location: trips.php");
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
  <title>Otobüs Seferleri</title>
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
    }

    th, td {
      border: 1px solid #ad0a0aff;
      padding: 6px 8px;
      text-align: left;
      font-size: 16px;
      font-weight: bold;
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
  <a href="dashboard.php"> Ana Sayfa</a>
  <a href="firmsadd.php"> Firma Ekle</a>
 <a href="coupons.php"> Kuponlar</a>
  <a href="trips.php">Seferler</a>
  <a href="assign_company.php"> Firma Admini Ata</a>
  <a href="users.php"> Kullanıcıları Listele</a>
  <a href="profile.php">Profili Düzenle</a>
  <a href="logout.php"> Çıkış Yap</a>
</nav>

<div class="main">
  <h1>OTOBÜS SEFERLERİ</h1>

  <?php if (isset($error)): ?>
    <div style="color: red;"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <button type="submit" class="new-trip-btn">YENİ SEFER OLUŞTUR</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Firma Adı</th> 
        <th>KALKIŞ NOKTASI</th>
        <th>VARİŞ NOKTASI</th>
        <th>Varış Zamanı</th>
        <th>Kalkış Zamanı</th>
        <th>Fiyat</th>
        <th>Koltuk Sayısı</th>
        <th>Oluşturulma Zamanı</th>
        <th>DÜZENLE/SİL</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($trips)): ?>
        <?php foreach ($trips as $trip): ?>
          <tr>
            <td><?php echo htmlspecialchars($trip['id']); ?></td>
            <td><?php echo htmlspecialchars($trip['company_name']); ?></td> 
            <td><?php echo htmlspecialchars($trip['departure_city']); ?></td>
            <td><?php echo htmlspecialchars($trip['destination_city']); ?></td>
            <td><?php echo htmlspecialchars($trip['arrival_time']); ?></td>
            <td><?php echo htmlspecialchars($trip['departure_time']); ?></td>
            <td><?php echo htmlspecialchars($trip['price']); ?></td>
            <td><?php echo htmlspecialchars($trip['capacity']); ?></td>
            <td><?php echo htmlspecialchars($trip['created_date']); ?></td>
            <td>
              <a href="tripsedit.php?id=<?php echo $trip['id']; ?>" class="action-btn edit-btn">Düzenle</a>
              <a href="?action=delete&id=<?php echo $trip['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Bu seferi silmek istediğinizden emin misiniz?')">Sil</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="10">Henüz sefer bulunmamaktadır.</td>
        </tr>
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
