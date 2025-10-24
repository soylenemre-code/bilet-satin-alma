<?php
session_start();
require_once '../db(database)/includes/db.php';
date_default_timezone_set('Europe/Istanbul');

// Yetki kontrolü
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$admin_adi = strtoupper($_SESSION['full_name'] ?? 'Yönetici');

// Toplam kullanıcı sayısı
$stmt = $db->query("SELECT COUNT(*) AS toplam_kullanici FROM User WHERE role = 'user'");
$toplam_kullanici = $stmt->fetch(PDO::FETCH_ASSOC)['toplam_kullanici'] ?? 0;

// Toplam firma sayısı
$stmt = $db->query("SELECT COUNT(*) AS toplam_firma FROM Bus_Company");
$toplam_firma = $stmt->fetch(PDO::FETCH_ASSOC)['toplam_firma'] ?? 0;

// Bugünkü toplam satış
$tarih = date('Y-m-d');
$stmt = $db->prepare("
    SELECT SUM(total_price) AS gunluk_satis 
    FROM Tickets 
    WHERE DATE(created_at) = ?
");
$stmt->execute([$tarih]);
$satis = $stmt->fetch(PDO::FETCH_ASSOC);
$bugunku_satis = $satis['gunluk_satis'] ?? 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Admin Paneli</title>
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
      flex-wrap: wrap;
    }

    .card {
      background-color: #fff;
      padding: 20px;
      flex: 1 1 250px;
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
      color: #2980b9;
    }

    .card .small {
      font-size: 14px;
      color: #7f8c8d;
    }

    nav {
      position: fixed;
      top: 0;
      left: 0;
      width: 220px;
      height: 100%;
      background-color: #2c3e50;
      padding: 30px 20px;
      box-sizing: border-box;
    }

    nav h2 {
      color: white;
      font-size: 20px;
      margin-bottom: 30px;
    }

    nav a {
      display: block;
      color: white;
      text-decoration: none;
      margin-bottom: 15px;
      font-weight: bold;
    }

    nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<nav>
  <h2>Admin Paneli</h2>
  <a href="firmsadd.php">Firma Ekle</a>
  <a href="assign_company.php"> Firma Admini Ata</a>
  <a href="trips.php">Seferler</a>
  <a href="coupons.php">Kuponlar</a>
  <a href="admins.php">Adminleri Görüntüle</a>
  <a href="users.php">Kullanıcıları Listele</a>
  <a href="logout.php"> Çıkış Yap</a>
</nav>

<div class="main">
  <div class="welcome">Hoş geldiniz <?= htmlspecialchars($admin_adi) ?>!</div>
  <div class="small">Bugünkü sistem istatistikleri aşağıda listelenmiştir.</div>

  <div class="stats-box">
    <div class="card">
      <h3>Toplam Kullanıcı</h3>
      <p><?= $toplam_kullanici ?></p>
      <div class="small">Sistemde kayıtlı normal kullanıcı sayısı</div>
    </div>

    <div class="card">
      <h3>Toplam Firma</h3>
      <p><?= $toplam_firma ?></p>
      <div class="small">Kayıtlı otobüs firması sayısı</div>
    </div>

    <div class="card">
      <h3>Bugünkü Satış</h3>
      <p>₺<?= number_format($bugunku_satis, 2, ',', '.') ?></p>
      <div class="small">Tüm firmalardan yapılan toplam satış</div>
    </div>
  </div>
</div>

</body>
</html>
