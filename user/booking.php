<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /login.php");
    exit();
}

$trip_id = $_GET['trip_id'] ?? $_POST['trip_id'] ?? null;
$coupon_code = trim($_POST['coupon_code'] ?? '');
$discount = 0;
$price_text = '';
$price = 0;

if (!$trip_id) {
    echo "Geçersiz işlem.";
    exit();
}

// Sefer fiyatı
$stmt_price = $db->prepare("SELECT price FROM Trips WHERE id = ?");
$stmt_price->execute([$trip_id]);
$trip = $stmt_price->fetch(PDO::FETCH_ASSOC);
$price = $trip['price'] ?? 0;

// Kupon kontrolü
if (isset($_POST['apply_coupon']) && !empty($coupon_code)) {
    $stmt = $db->prepare("SELECT * FROM Coupons WHERE code = ? AND expire_date >= DATE('now') AND usage_limit > 0");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        $discount = $coupon['discount'];
        $price = $price * (1 - $discount / 100);
        $price_text = "Kupon uygulandı! İndirim: %" . $discount;
        
    } else {
        $price_text = "Geçersiz veya süresi dolmuş kupon.";
    }
}

// Koltukları çek
$stmt = $db->prepare("SELECT seat_number, is_booked FROM Seats WHERE trip_id = ?");
$stmt->execute([$trip_id]);
$seats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Koltuk Seçimi</title>
    <style>
        .seat {
            width: 40px;
            height: 40px;
            margin: 5px;
            display: inline-block;
            text-align: center;
            line-height: 40px;
            border-radius: 5px;
            cursor: pointer;
        }
        .booked { background-color: red; color: white; }
        .available { background-color: green; color: white; }
        .selected { background-color: #ffc107 !important; color: black !important; font-weight: bold; border: 2px solid #333; }
    </style>
</head>
<body>

<h2>Koltuk Seçimi</h2>

<!-- Kuponu Uygula Formu -->
<form method="POST" action="">
    <input type="hidden" name="trip_id" value="<?= $trip_id ?>">
    <label for="coupon_code">Kupon Kodu (varsa):</label>
    <input type="text" id="coupon_code" name="coupon_code" value="<?= htmlspecialchars($coupon_code) ?>">
    <button type="submit" name="apply_coupon">Kuponu Uygula</button>
</form>

<?php if ($price_text): ?>
    <p><strong><?= htmlspecialchars($price_text) ?></strong></p>
<?php endif; ?>

<!-- Bilet Satın Alma Formu -->
<form method="POST" action="confirm_booking.php">
    <input type="hidden" name="trip_id" value="<?= $trip_id ?>">
    <input type="hidden" name="coupon_code" value="<?= htmlspecialchars($coupon_code) ?>">

    <?php foreach ($seats as $seat): ?>
        <?php if ($seat['is_booked']): ?>
            <div class="seat booked"><?= $seat['seat_number'] ?></div>
        <?php else: ?>
            <label>
                <input type="radio" name="seat_number" value="<?= $seat['seat_number'] ?>" hidden>
                <div class="seat available"><?= $seat['seat_number'] ?></div>
            </label>
        <?php endif; ?>
    <?php endforeach; ?>

    <div style="margin-top: 10px; font-weight: bold;">
        Ödemeniz gereken ücret: ₺<?= number_format($price, 2, ',', '.') ?>
    </div>

    <br><br>
    <button type="submit">Bileti Al</button>
</form>

<script>
    document.querySelectorAll('.available').forEach(seat => {
        seat.addEventListener('click', function () {
            document.querySelectorAll('.available').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            this.previousElementSibling.checked = true;
        });
    });
</script>

</body>
</html>
