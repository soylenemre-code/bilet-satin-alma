<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$trip_id = $_POST['trip_id'] ?? null;
$seat_number = $_POST['seat_number'] ?? null;
$coupon_code = $_POST['coupon_code'] ?? null;
$price=$_GET['price']?? null;

if (!$user_id || !$trip_id || !$seat_number) {
    die("Eksik bilgi.");
}

// Sefer bilgisi ve fiyatı
$stmt = $db->prepare("SELECT company_id, price FROM Trips WHERE id = ?");
$stmt->execute([$trip_id]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);
$price = $trip['price'] ?? 0;
if (!$trip) {
    die("Sefer bulunamadı.");
}

$company_id = $trip['company_id'];
$price = $trip['price'];

// Kupon kontrolü
if ($coupon_code) {
    $stmt = $db->prepare("
        SELECT id, discount, usage_limit, expire_date 
        FROM Coupons 
        WHERE code = ? AND expire_date >= date('now') AND usage_limit > 0
    ");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM user_coupons 
            WHERE coupon_id = ? AND user_id = ?
        ");
        $stmt->execute([$coupon['id'], $user_id]);
        $coupon_used = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($coupon_used > 0) {
            die("Bu kupon kodunu daha önce kullandınız.");
        }

        // İndirim uygula
        $price -= $coupon['discount'];
        if ($price < 0) $price = 0;

        // Kupon kullanımını güncelle
        $stmt = $db->prepare("UPDATE Coupons SET usage_limit = usage_limit - 1 WHERE id = ?");
        $stmt->execute([$coupon['id']]);

        $stmt = $db->prepare("INSERT INTO user_coupons (coupon_id, user_id) VALUES (?, ?)");
        $stmt->execute([$coupon['id'], $user_id]);
    } else {
        die("Geçersiz, süresi dolmuş veya kullanım limiti bitmiş kupon kodu.");
    }
}

// Kullanıcı bakiyesi kontrolü
$stmt = $db->prepare("SELECT balance FROM User WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Kullanıcı bulunamadı.");
}

if ($user['balance'] < $price) {
    die("Yetersiz bakiye.");
}

// Koltuk durumu kontrolü
$stmt = $db->prepare("SELECT is_booked FROM Seats WHERE trip_id = ? AND seat_number = ?");
$stmt->execute([$trip_id, $seat_number]);
$seat = $stmt->fetch(PDO::FETCH_ASSOC);

if ($seat && $seat['is_booked']) {
    die("Bu koltuk zaten alınmış.");
}

// Koltuğu rezerve et
$stmt = $db->prepare("UPDATE Seats SET is_booked = 1, booked_by = ? WHERE trip_id = ? AND seat_number = ?");
$stmt->execute([$user_id, $trip_id, $seat_number]);

// Kullanıcının bakiyesini düşür
$stmt = $db->prepare("UPDATE User SET balance = balance - ? WHERE id = ?");
$stmt->execute([$price, $user_id]);

// Firma yöneticisine ödeme aktar
$stmt = $db->prepare("UPDATE User SET balance = balance + ? WHERE role = 'firmadmin' AND company_id = ?");
$stmt->execute([$price, $company_id]);

// Bilet oluştur
$ticket_id = strtolower(bin2hex(random_bytes(8)));
$stmt = $db->prepare("INSERT INTO Tickets (id, trip_id, user_id, total_price) VALUES (?, ?, ?, ?)");
$stmt->execute([$ticket_id, $trip_id, $user_id, $price]);

// Koltuk kaydını oluştur
$seat_id = strtolower(bin2hex(random_bytes(8)));
$stmt = $db->prepare("INSERT INTO Booked_Seats (id, ticket_id, seat_number) VALUES (?, ?, ?)");
$stmt->execute([$seat_id, $ticket_id, $seat_number]);

// Session bakiyesini güncelle
$_SESSION['balance'] = $user['balance'] - $price;

header("Refresh: 3; url=tickets.php");
echo "🎉 <Bilet başarıyla alındı! 3 saniye içinde yönlendiriliyorsunuz...";
exit();

?>

