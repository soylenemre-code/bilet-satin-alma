<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

$error = "";
$trips = [];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['departure_city'], $_GET['destination_city'], $_GET['date'])) {
        $departure_city = trim($_GET['departure_city']);
        $destination_city = trim($_GET['destination_city']);
        $date = trim($_GET['date']);

        try {
            $stmt = $db->prepare('
                SELECT Trips.id, Trips.company_id, Bus_Company.name AS company_name,
                       Trips.destination_city, Trips.arrival_time, Trips.departure_time,
                       Trips.departure_city, Trips.price, Trips.capacity, Trips.created_date
                FROM Trips
                LEFT JOIN Bus_Company ON Trips.company_id = Bus_Company.id
                WHERE Trips.departure_city = ? AND Trips.destination_city = ? AND DATE(Trips.departure_time) = ?
            ');
            $stmt->execute([$departure_city, $destination_city, $date]);
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    } elseif (isset($_GET['company_id'], $_GET['date'])) {
        $company_id = trim($_GET['company_id']);
        $date = trim($_GET['date']);

        try {
            $stmt = $db->prepare('
                SELECT Trips.id, Trips.company_id, Bus_Company.name AS company_name,
                       Trips.destination_city, Trips.arrival_time, Trips.departure_time,
                       Trips.departure_city, Trips.price, Trips.capacity, Trips.created_date
                FROM Trips
                LEFT JOIN Bus_Company ON Trips.company_id = Bus_Company.id
                WHERE Trips.company_id = ? AND DATE(Trips.departure_time) = ?
            ');
            $stmt->execute([$company_id, $date]);
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background-color: #f2f2f2;
            padding-top: 110px;
        }
        nav {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 90px;
            background-color: #09e2f2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        label.profile {
            color: white;
            font-size: 28px;
            font-weight: bold;
        }
        label.balance {
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        nav ul {
            display: flex;
            list-style: none;
        }
        nav ul li {
            margin: 0 12px;
        }
        nav ul li a {
            color: #000;
            font-size: 18px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 4px;
            text-transform: uppercase;
            transition: background 0.3s;
            text-decoration: none;
        }
        nav ul li a:hover,
        nav ul li a.home {
            background: #1b9bff;
            color: #fff;
        }
        .content {
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 15px;
        }
        th {
            background-color: #8bc34a;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
        @media (max-width: 480px) {
            nav { padding: 0 15px; }
            label.profile { font-size: 20px; }
            label.balance { font-size: 16px; }
            nav ul li a { font-size: 14px; padding: 6px 10px; }
            table, th, td { font-size: 13px; }
        }
    </style>
</head>
<body>

<nav>
    <label class="profile">HOŞ GELDİNİZ: <?= htmlspecialchars($_SESSION['full_name'] ?? 'Kullanıcı') ?></label>
    <ul>
        <li><a class="home" href="index.php">ANA SAYFA</a></li>
        <li><a href="tickets.php">BİLETLERİM</a></li>
        <li><a href="profile.php">PROFİLİM</a></li>
        <li><a href="logout.php">ÇIKIŞ YAP</a></li>
    </ul>
    <label class="balance">BAKİYENİZ: <?= htmlspecialchars($_SESSION['balance'] ?? '0') ?> ₺</label>
</nav>

<div class="content">
    <h1>Otobüs Seferleri</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (count($trips) === 0): ?>
        <p>Uygun sefer bulunamadı.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>FİRMA</th>
                    <th>KALKIŞ</th>
                    <th>VARIŞ</th>
                    <th>KALKIŞ SAATİ</th>
                    <th>VARIŞ SAATİ</th>
                    <th>FİYAT</th>
                    <th>KAPASİTE</th>
                    <th>İŞLEM</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= htmlspecialchars($trip['company_name'] ?? 'Bilinmiyor') ?></td>
                        <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                        <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                        <td><?= date('H:i', strtotime($trip['departure_time'])) ?></td>
                        <td><?= date('H:i', strtotime($trip['arrival_time'])) ?></td>
                        <td><?= number_format($trip['price'], 2, ',', '.') ?> TL</td>
                        <td><?= htmlspecialchars($trip['capacity']) ?></td>
                        <td>
                            <a href="booking.php?trip_id=<?= $trip['id'] ?>" class="back-button">Bilet Al</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="index.php" class="back-button">Ana Sayfaya Dön</a>
</div>

</body>
</html>
