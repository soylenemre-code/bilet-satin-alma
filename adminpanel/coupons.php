<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {         
    header("Location: /index.php");
    exit();
}

$error = '';
$success = '';

// Kupon ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $discount = floatval($_POST['discount'] ?? 0);
    $usage_limit = intval($_POST['usage_limit'] ?? 0);
    $expire_date = $_POST['expire_date'] ?? '';

    if ($code && $discount > 0 && $usage_limit > 0 && $expire_date) {
        try {
            $stmt = $db->prepare("INSERT INTO Coupons (code, discount, usage_limit, expire_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $discount, $usage_limit, $expire_date]);
            $success = "Kupon başarıyla eklendi.";
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $error = "Lütfen tüm alanları doğru şekilde doldurun.";
    }
}


if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    try {
        $stmt = $db->prepare("DELETE FROM Coupons WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: coupons.php");
        exit();
    } catch (PDOException $e) {
        $error = "Silme işlemi sırasında hata oluştu.";
    }
}

// Kuponları listele
try {
    $stmt = $db->query("SELECT * FROM Coupons ORDER BY created_at DESC");
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veri çekme hatası: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kuponlar</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 30px;
    }

    h1 {
      text-align: center;
      color: #333;
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

    input[type="text"],
    input[type="number"],
    input[type="date"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      background-color: #4caf50;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    button:hover {
      background-color: #388e3c;
    }

    .message {
      text-align: center;
      font-size: 16px;
      margin: 10px 0;
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

    .edit-btn {
      background-color: #2196F3;
    }

    .delete-btn {
      background-color: red;
    }
  </style>
</head>
<body>

  <h1>İndirim Kuponları</h1>

  <?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="code">Kupon Kodu:</label>
    <input type="text" id="code" name="code" required>

    <label for="discount">İndirim Oranı (%):</label>
    <input type="number" id="discount" name="discount" step="0.01" required>

    <label for="usage_limit">Kullanım Limiti:</label>
    <input type="number" id="usage_limit" name="usage_limit" required>

    <label for="expire_date">Son Kullanma Tarihi:</label>
    <input type="date" id="expire_date" name="expire_date" required>

    <button type="submit">Kupon Oluştur</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Kod</th>
        <th>İndirim (%)</th>
        <th>Kullanım Limiti</th>
        <th>Son Kullanma</th>
        <th>Oluşturulma</th>
        <th>İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($coupons)): ?>
        <?php foreach ($coupons as $coupon): ?>
          <tr>
            <td><?= htmlspecialchars($coupon['code']) ?></td>
            <td><?= htmlspecialchars($coupon['discount']) ?></td>
            <td><?= htmlspecialchars($coupon['usage_limit']) ?></td>
            <td><?= htmlspecialchars($coupon['expire_date']) ?></td>
            <td><?= htmlspecialchars($coupon['created_at']) ?></td>
            <td>
              <a href="couponedit.php?id=<?= $coupon['id'] ?>" class="action-btn edit-btn">Düzenle</a>
              <a href="?action=delete&id=<?= $coupon['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Bu kuponu silmek istediğinize emin misiniz?')">Sil</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6">Henüz kupon bulunmamaktadır.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>
