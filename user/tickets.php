<?php
session_start();
date_default_timezone_set('Europe/Istanbul'); 
require_once __DIR__ . '/../db(database)/includes/db.php';

$cancel_message = '';
if (isset($_GET['cancel_message'])) {
    $cancel_message = htmlspecialchars($_GET['cancel_message']);
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("
        SELECT 
            t.id AS ticket_id,
            t.status,
            t.total_price,
            t.created_at,
            tr.id AS trip_id,
            tr.departure_time,
            tr.departure_city,
            tr.destination_city,
            c.name AS company_name
        FROM Tickets t
        JOIN Trips tr ON t.trip_id = tr.id
        JOIN Bus_Company c ON tr.company_id = c.id
        WHERE t.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Veritabanı hatası.";
}


if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    try {
        $stmt = $db->prepare("SELECT * FROM Tickets WHERE id = ? AND user_id = ?");
        $stmt->execute([$ticket_id, $user_id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            header("Location: tickets.php?cancel_message=Bilet bulunamadı.");
            exit();
        }

        $trip_id = $ticket['trip_id'];
        $refund_amount = $ticket['total_price'];

        $stmt = $db->prepare("SELECT departure_time FROM Trips WHERE id = ?");
        $stmt->execute([$trip_id]);
        $trip = $stmt->fetch(PDO::FETCH_ASSOC);

        $departure_time = new DateTime(str_replace('T', ' ', $trip['departure_time']));
        $current_time = new DateTime();
        $sure = $departure_time->getTimestamp() - $current_time->getTimestamp();
       //kalkış zamanından şuanki zamanı çıkartarak aradaki farkı saniye cinsinden $sure diye bir süre deiğikenine atadım
        if ($sure < 3600) {
            header("Location: tickets.php?cancel_message=Sefer saatine 1 saatten az kaldı. Bilet iptal edilemez.");
            exit();
        }

        $stmt = $db->prepare("SELECT company_id FROM Trips WHERE id = ?");
       $stmt->execute([$trip_id]);
       $trip_info = $stmt->fetch(PDO::FETCH_ASSOC);
       $company_id = $trip_info['company_id'];

       $stmt = $db->prepare("SELECT id FROM User WHERE company_id = ? AND role = 'firmadmin'");
       $stmt->execute([$company_id]);
       $firm_admin = $stmt->fetch(PDO::FETCH_ASSOC);
       $firm_admin_id = $firm_admin['id'];

       $stmt = $db->prepare("UPDATE User SET balance = balance - ? WHERE id = ?");
       $stmt->execute([$refund_amount, $firm_admin_id]);



        $_SESSION['balance'] += $refund_amount;

        $stmt = $db->prepare("UPDATE Seats SET is_booked = 0, booked_by = NULL WHERE trip_id = ? AND seat_number IN (
            SELECT seat_number FROM Booked_Seats WHERE ticket_id = ?
        )");
        $stmt->execute([$trip_id, $ticket_id]);

        $stmt = $db->prepare("DELETE FROM Booked_Seats WHERE ticket_id = ?");
        $stmt->execute([$ticket_id]);
    
        $stmt = $db->prepare("DELETE FROM Tickets WHERE id = ?");
        $stmt->execute([$ticket_id]);

        header("Location: tickets.php?cancel_message=Bilet başarıyla iptal edildi.");
        exit();
    } catch (PDOException $e) {
        header("Location: tickets.php?cancel_message=İptal işlemi sırasında hata oluştu.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Otobüs Biletlerim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ad0a0aff;
            padding: 8px;
            text-align: left;
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
            margin-right: 5px;
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
        .message {
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
    <meta charset="UTF-8">
    <title>Otobüs Biletlerim</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            text-decoration: none;
        }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background-color: #f2f2f2;
            min-height: 100vh;
            padding-top: 110px; 
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 90px;
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
            font-size: 32px;
            font-weight: 700;
        }

        label.balance {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-left: auto;
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
            font-size: 20px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 4px;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        nav ul li a:hover,
        nav ul li a.home {
            background: #1b9bff;
            color: #fff;
        }

      
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ad0a0aff;
            padding: 8px;
            text-align: left;
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
            margin-right: 5px;
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
        .message {
            font-weight: bold;
            margin-top: 15px;
        }

        @media (max-width: 480px) {
            nav { padding: 0 15px; }
            label.profile { font-size: 24px; }
            label.balance { font-size: 18px; }
            nav ul li a { font-size: 16px; padding: 6px 10px; }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

<h1>OTOBÜS BİLETLERİM</h1>

<?php if (isset($error)): ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" action="index.php">
    <button type="submit" class="new-trip-btn">YENİ BİLET AL</button>
</form>

<?php if (!empty($cancel_message)): ?>
    <div class="message" style="color: <?= strpos($cancel_message, 'başarı') !== false ? 'green' : 'red' ?>;">
        <?= $cancel_message ?>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>FİRMA</th>
            <th>NEREDEN</th>
            <th>NEREYE</th>
            <th>SEFER ZAMANI</th>
            <th>STATÜ</th>
            <th>ÜCRET</th>
            <th>OLUŞTURULMA</th>
            <th>İŞLEM</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $ticket): ?>
                <?php
                    $departure_time = new DateTime(str_replace('T', ' ', $ticket['departure_time']));
                    $now = new DateTime();
                    $status = ($departure_time > $now) ? 'AKTİF' : 'GERÇEKLEŞTİ';
                ?>
                <tr>
                    <td><?= htmlspecialchars($ticket['ticket_id']) ?></td>
                    <td><?= htmlspecialchars($ticket['company_name']) ?></td>
                    <td><?= htmlspecialchars($ticket['departure_city']) ?></td>
                    <td><?= htmlspecialchars($ticket['destination_city']) ?></td>
                    <td><?= htmlspecialchars($ticket['departure_time']) ?></td>
                    <td><?= $status ?></td>
                    <td><?= htmlspecialchars($ticket['total_price']) ?>₺</td>
                    <td><?= htmlspecialchars($ticket['created_at']) ?></td>
                    <td>
                        <?php if ($status === 'AKTİF'): ?>
                            <a href="?action=delete&id=<?= $ticket['ticket_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Bu bileti iptal etmek istediğinizden emin misiniz?')">İPTAL ET</a>
                        <?php else: ?>
                            <span style="color:gray;">İptal Edilemez</span>
                        <?php endif; ?>
                        <a href="biletindir.php?ticket_id=<?= $ticket['ticket_id'] ?>" target="_blank" class="action-btn" style="background-color:#2196F3; color:white;">PDF İndir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9">Henüz biletiniz bulunmamaktadır.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
