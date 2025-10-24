<?php
require_once __DIR__ . '/../db(database)/includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['ticket_id'])) {
    die("Bilet ID belirtilmedi.");
}

$ticket_id = $_GET['ticket_id'];

$stmt = $db->prepare("
    SELECT 
        t.id AS ticket_id,
        t.status,
        u.full_name,
        t.total_price,
        t.created_at,
        tr.departure_time,
        tr.departure_city,
        tr.destination_city,
        c.name AS company_name,
        GROUP_CONCAT(bs.seat_number) AS seat_numbers
    FROM Tickets t
    JOIN Trips tr ON t.trip_id = tr.id
    JOIN Bus_Company c ON tr.company_id = c.id
    JOIN Booked_Seats bs ON bs.ticket_id = t.id
    JOIN User u ON t.user_id = u.id
    WHERE t.user_id = ? AND t.id = ?
    GROUP BY t.id
");


$stmt->execute([$user_id, $ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Bilet bulunamadı.");
}

// DOMPDF ayarları
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// HTML içeriği
$html = '<meta charset="UTF-8">';
$html .= '
<style>
    body { font-family: "DejaVu Sans", sans-serif; }
    .ticket-container {
        width: 600px;
        margin: 0 auto;
        border: 2px dashed #333;
        padding: 20px;
        border-radius: 10px;
        background-color: #fefefe;
    }
    .ticket-header {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
        color: #2c3e50;
    }
    .ticket-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 16px;
    }
    .label {
        font-weight: bold;
        color: #555;
    }
    .value {
        color: #000;
    }
</style>

<div class="ticket-container">
    <div class="ticket-header">Otobüs Bilet Bilgisi</div>

    <div class="ticket-row"><span class="label">Bilet ID:</span><span class="value">' . htmlspecialchars($ticket['ticket_id']) . '</span></div>
    <div class="ticket-row"><span class="label">Firma:</span><span class="value">' . htmlspecialchars($ticket['company_name']) . '</span></div>
    <div class="ticket-row"><span class="label">AD-SOYAD:</span><span class="value">' . htmlspecialchars($ticket['full_name']) . '</span></div>
    <div class="ticket-row"><span class="label">Nereden:</span><span class="value">' . htmlspecialchars($ticket['departure_city']) . '</span></div>
    <div class="ticket-row"><span class="label">Nereye:</span><span class="value">' . htmlspecialchars($ticket['destination_city']) . '</span></div>
    <div class="ticket-row"><span class="label">Sefer Zamanı:</span><span class="value">' . htmlspecialchars($ticket['departure_time']) . '</span></div>
    <div class="ticket-row"><span class="label">Koltuk No:</span><span class="value">' . htmlspecialchars($ticket['seat_numbers']) . '</span></div>
    <div class="ticket-row"><span class="label">Statü:</span><span class="value">' . htmlspecialchars($ticket['status']) . '</span></div>
    <div class="ticket-row"><span class="label">Ücret:</span><span class="value">' . htmlspecialchars($ticket['total_price']) . '₺</span></div>
    <div class="ticket-row"><span class="label">Oluşturulma:</span><span class="value">' . htmlspecialchars($ticket['created_at']) . '</span></div>
</div>
';

// PDF oluştur
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("bilet_" . $ticket['ticket_id'] . ".pdf", ["Attachment" => true]);
