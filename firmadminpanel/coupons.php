<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'firmadmin') {
    header("Location: /index.php");
    exit();
}

$company_id = $_SESSION['company_id'] ?? null;
if (!$company_id) {
    echo "Firma bilgisi eksik.";
    exit();
}

$error = '';
$success = '';
$coupons = [];

// Kupon ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = trim($_POST['code']);
    $discount = floatval($_POST['discount']);
    $usage_limit = intval($_POST['usage_limit']);
    $expire_date = $_POST['expire_date'];

    if ($code && $discount > 0 && $usage_limit > 0 && $expire_date) {
        try {
            $stmt = $db->prepare("INSERT INTO Coupons (code, discount, usage_limit, expire_date, company_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$code, $discount, $usage_limit, $expire_date, $company_id]);
            $success = "Kupon başarıyla eklendi.";
        } catch (PDOException $e) {
            $error = "Kupon eklenemedi: " . $e->getMessage();
        }
    } else {
        $error = "Lütfen tüm alanları doğru şekilde doldurun.";
    }
}

// Kupon silme işlemi
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM Coupons WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $company_id]);
        header("Location: coupons.php");
        exit();
    } catch (PDOException $e) {
        $error = "Silme işlemi sırasında hata oluştu.";
    }
}

// Kuponları listele
try {
    $stmt = $db->prepare("SELECT * FROM Coupons WHERE company_id = ? ORDER BY created_at DESC");
    $stmt->execute([$company_id]);
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Firma Kuponları</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { margin: 0; font-family: sans-serif; background-color: #f4f4f4; }
    #toggleBtn {
      position: fixed; top: 10px; left: 10px; z-index: 1001;
      background-color: #2c3e50; color: white; border: none;
      padding: 10px 15px; cursor: pointer; border-radius: 4px;
    }
    #sidebar {
      position: fixed; top: 0; left: -220px; width: 220px; height: 100%;
      background-color: #2c3e50; padding: 30px 20px; box-sizing: border-box;
      transition: left 0.3s ease; z-index: 1000;
    }
    #sidebar.active { left: 0; }
    #sidebar h2 { color: white; font-size: 20px; margin-bottom: 30px; }
    #sidebar a {
      display: block; color: white; text-decoration: none;
      margin-bottom: 15px; font-weight: bold;
    }
    #sidebar a:hover { text-decoration: underline; }
    .main {
      margin-left: 0; padding: 30px; transition: margin-left 0.3s ease;
    }
    #sidebar.active ~ .main { margin-left: 220px; }
    table {
      width: 100%; border-collapse: collapse; margin-top: 20px;
      background-color: white;
    }
    th, td {
      border: 1px solid #ccc; padding: 8px; text-align: left;
    }
    th { background-color: #8bc34a; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .action-btn {
      padding: 5px 10px; text-decoration: none; color: white;
      border-radius: 3px; font-size: 14px;
    }
    .delete-btn { background-color: red; }
    .new-btn {
      background-color: #4caf50; color: white; padding: 10px 15px;
      border: none; cursor: pointer; border-radius: 4px;
      font-size: 15px; margin-bottom: 15px;
    }
    .error { color: red; font-weight: bold; }
    .success { color: green; font-weight: bold; }
    form.coupon-form input {
      padding: 8px; margin-bottom: 10px; width: 100%;
    }
  </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
  <h2>Firma Paneli</h2>
  <a href="index.php">Ana Sayfa</a>
  <a href="trips.php">Seferlerim</a>
  <a href="tripadd.php">Yeni Sefer Ekle</a>
  <a href="coupons.php">Kuponlarım</a>
  <a href="logout.php">Çıkış Yap</a>
</nav>

<div class="main">
  <h1>Firma Kuponları</h1>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" class="coupon-form">
    <h3>Yeni Kupon Ekle</h3>
    <input type="text" name="code" placeholder="Kupon Kodu" required>
    <input type="number" name="discount" placeholder="İndirim (%)" min="1" max="100" required>
    <input type="number" name="usage_limit" placeholder="Kullanım Limiti" min="1" required>
    <input type="date" name="expire_date" required>
    <button type="submit" class="new-btn">Kuponu Kaydet</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Kod</th>
        <th>İndirim (%)</th>
        <th>Kullanım Limiti</th>
        <th>Son Tarih</th>
        <th>Oluşturulma</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($coupons)): ?>
        <?php foreach ($coupons as $coupon): ?>
          <tr>
            <td><?= htmlspecialchars($coupon['id']) ?></td>
            <td><?= htmlspecialchars($coupon['code']) ?></td>
            <td>%<?= htmlspecialchars($coupon['discount']) ?></td>
            <td><?= htmlspecialchars($coupon['usage_limit']) ?></td>
            <td><?= htmlspecialchars($coupon['expire_date']) ?></td>
            <td><?= htmlspecialchars($coupon['created_at']) ?></td>
            <td>
              <a href="?action=delete&id=<?= $coupon['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Bu kuponu silmek istediğinizden emin misiniz?')">Sil</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">Henüz kupon bulunmamaktadır.</td></tr>
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
