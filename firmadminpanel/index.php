<?php
session_start();
require_once '../db(database)/includes/db.php';
date_default_timezone_set('Europe/Istanbul');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'firmadmin') {
    header("Location: /index.php");
    exit();
}
$user_id = $_SESSION['user_id'];


// Firma yöneticisi bilgileri
$stmt = $db->prepare("SELECT full_name, company_id, balance FROM User WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$firma_adi = strtoupper($user['full_name'] ?? 'Firma Yöneticisi');
$company_id = $user['company_id'];
$firma_bakiyesi = $user['balance'] ?? 0;

// Bugünkü satış
$tarih = date('Y-m-d');
$stmt = $db->prepare("
    SELECT SUM(total_price) AS gunluk_satis 
    FROM Tickets 
    WHERE DATE(created_at) = ? 
    AND trip_id IN (SELECT id FROM Trips WHERE company_id = ?)
");
$stmt->execute([$tarih, $company_id]);
$satis = $stmt->fetch(PDO::FETCH_ASSOC);
$bugunku_satis = $satis['gunluk_satis'] ?? 0;

// Toplam sefer sayısı
$stmt = $db->prepare("SELECT COUNT(*) AS sefer_sayisi FROM Trips WHERE company_id = ?");
$stmt->execute([$company_id]);
$sefer = $stmt->fetch(PDO::FETCH_ASSOC);
$toplam_sefer = $sefer['sefer_sayisi'] ?? 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    .main {
      margin-left: 220px;
      padding: 30px;
    }

    .welcome {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .stats-box {
      display: flex;
      gap: 20px;
      margin-top: 20px;
    }

    .card {
      background-color: #fff;
      padding: 20px;
      flex: 1;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .card h3 {
      margin-top: 0;
      font-size: 18px;
      color: #2c3e50;
    }

    .card p {
      font-size: 20px;
      font-weight: bold;
      color: #27ae60;
    }

    .card .small {
      font-size: 14px;
      color: #7f8c8d;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
  <div class="welcome">Hoş geldiniz <?= htmlspecialchars($firma_adi) ?>!</div>
  <div class="small">Bugün sistemde yeni rezervasyonlar yapıldı.</div>

  <div class="stats-box">
    <div class="card">
      <h3>Firma Bakiyesi</h3>
      <p>₺<?= number_format($firma_bakiyesi, 2, ',', '.') ?></p>
      <div class="small">Toplam kullanılabilir bakiye</div>
    </div>

    <div class="card">
      <h3>Bugünkü Satış</h3>
      <p>₺<?= number_format($bugunku_satis, 2, ',', '.') ?></p>
      <div class="small">Bugün yapılan bilet satışları</div>
    </div>

    <div class="card">
      <h3>Toplam Sefer Sayısı</h3>
      <p><?= $toplam_sefer ?></p>
      <div class="small">Firmanıza ait aktif seferler</div>
    </div>
  </div>
</div>

</body>
</html>
