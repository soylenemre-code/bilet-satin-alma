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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $discount = $_POST['discount'] ?? 0;
    $usage_limit = $_POST['usage_limit'] ?? 1;
    $expire_date = $_POST['expire_date'] ?? '';

    if ($code && $discount && $usage_limit && $expire_date) {
        try {
            $stmt = $db->prepare("INSERT INTO Coupons (code, discount, usage_limit, expire_date, company_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$code, $discount, $usage_limit, $expire_date, $company_id]);
            header("Location: coupons.php");
            exit();
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    } else {
        $error = "Lütfen tüm alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Yeni Kupon Ekle</title>
</head>
<body>
  <h1>Yeni Kupon Ekle</h1>
  <?php if (!empty($error)): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <form method="POST">
    <label>Kupon Kodu:</label><br>
    <input type="text" name="code" required><br><br>

    <label>İndirim (₺):</label><br>
    <input type="number" name="discount" required><br><br>

    <label>Kullanım Limiti:</label><br>
    <input type="number" name="usage_limit" required><br><br>

    <label>Son Kullanma Tarihi:</label><br>
    <input type="date" name="expire_date" required><br><br>

    <button type="submit">Kuponu Kaydet</button>
  </form>
</body>
</html>
