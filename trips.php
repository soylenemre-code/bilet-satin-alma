<?php
session_start();
require_once __DIR__ . '/db(database)/includes/db.php';

$error = "";
$success = "";
$trips = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['departure_city']) && isset($_GET['destination_city']) && isset($_GET['date'])) {
        $departure_city = trim($_GET['departure_city']);
        $destination_city = trim($_GET['destination_city']);
        $date = trim($_GET['date']);

        try {
            $stmt = $db->prepare("
                SELECT Trips.id, Trips.company_id, Bus_Company.name AS company_name,
                       Trips.destination_city, Trips.arrival_time, Trips.departure_time,
                       Trips.departure_city, Trips.price, Trips.capacity, Trips.created_date
                FROM Trips
                JOIN Bus_Company ON Trips.company_id = Bus_Company.id
                WHERE Trips.departure_city = ? AND Trips.destination_city = ? AND DATE(Trips.departure_time) = ?
            ");
            $stmt->execute([$departure_city, $destination_city, $date]);
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Database hatası: " . $e->getMessage();
        }
    } elseif (isset($_GET['company_id']) && isset($_GET['date'])) {
        $company_id = trim($_GET['company_id']);
        $date = trim($_GET['date']);

        try {
            $stmt = $db->prepare("
                SELECT Trips.id, Trips.company_id, Bus_Company.name AS company_name,
                       Trips.destination_city, Trips.arrival_time, Trips.departure_time,
                       Trips.departure_city, Trips.price, Trips.capacity, Trips.created_date
                FROM Trips
                JOIN Bus_Company ON Trips.company_id = Bus_Company.id
                WHERE Trips.company_id = ? AND DATE(Trips.departure_time) = ?
            ");
            $stmt->execute([$company_id, $date]);
            $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Database hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>OTOBÜS SEFERLERİ</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; border: 1px solid #ddd; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; font-weight: bold; }
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
    </style>
</head>
<body>

<h1>OTOBÜS SEFERLERİ</h1>

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
                <th>KALKIŞ NOKTASI</th>
                <th>VARIŞ NOKTASI</th>
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
                    <td><?= htmlspecialchars($trip['company_name']) ?></td>
                    <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                    <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                    <td><?= htmlspecialchars(date('H:i', strtotime($trip['departure_time']))) ?></td>
                    <td><?= htmlspecialchars(date('H:i', strtotime($trip['arrival_time']))) ?></td>
                    <td><?= htmlspecialchars($trip['price']) ?> TL</td>
                    <td><?= htmlspecialchars($trip['capacity']) ?></td>
                    <td>
                        <a href="user/booking.php?trip_id=<?= $trip['id'] ?>" class="back-button">Bilet Al</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a href="index.php" class="back-button">Ana Sayfaya Dön</a>

</body>
</html>
