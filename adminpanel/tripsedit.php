<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$trip_id = $_GET['id'] ?? null;
if (!$trip_id) {
    echo "Geçersiz sefer ID.";
    exit();
}

$stmt = $db->prepare("SELECT * FROM Trips WHERE id = ?");
$stmt->execute([$trip_id]);
$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trip) {
    echo "Sefer bulunamadı.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departure_city = $_POST['departure_city'];
    $destination_city = $_POST['destination_city'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];

    $update = $db->prepare("UPDATE Trips SET departure_city=?, destination_city=?, departure_time=?, arrival_time=?, price=?, capacity=? WHERE id=?");
    $update->execute([$departure_city, $destination_city, $departure_time, $arrival_time, $price, $capacity, $trip_id]);

    echo "<p style='color:green;'>Sefer bilgileri güncellendi.</p>";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Seferi Düzenle</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background-color: #f4f4f4;
      padding: 30px;
    }

    #toggleBtn {
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1001;
      background-color: #2c3e50;
      color: white;
      border: none;
      padding: 10px 15px;
      cursor: pointer;
      border-radius: 4px;
    }

    #sidebar {
      position: fixed;
      top: 0;
      left: -220px;
      width: 220px;
      height: 100%;
      background-color: #2c3e50;
      padding: 30px 20px;
      box-sizing: border-box;
      transition: left 0.3s ease;
      z-index: 1000;
    }

    #sidebar.active {
      left: 0;
    }

    #sidebar h2 {
      color: white;
      font-size: 20px;
      margin-bottom: 30px;
    }

    #sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      margin-bottom: 15px;
      font-weight: bold;
    }

    #sidebar a:hover {
      text-decoration: underline;
    }

    .main {
      margin-left: 0;
      transition: margin-left 0.3s ease;
    }

    #sidebar.active ~ .main {
      margin-left: 220px;
    }

    h2 { color: #2c3e50; }
    form { margin-bottom: 40px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input[type="text"], input[type="number"], input[type="datetime-local"] {
      width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;
    }
    button {
      margin-top: 15px; padding: 10px 20px;
      background-color: #4caf50; color: white;
      border: none; cursor: pointer; border-radius: 4px;
    }
    table {
      width: 100%; border-collapse: collapse;
    }
    th, td {
      border: 1px solid #ccc; padding: 8px; text-align: left;
    }
    th {
      background-color: #2196F3; color: white;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

<button id="toggleBtn">☰ Menü</button>

<nav id="sidebar">
  <h2>Admin Paneli</h2>
  <a href="dashboard.php">Ana Sayfa</a>
  <a href="trips.php">Seferler</a>
  <a href="tripsadd.php">Yeni Sefer Ekle</a>
  <a href="users.php">Kullanıcılar</a>
  <a href="firmsadd.php">Firma Ekle</a>
  <a href="logout.php">Çıkış Yap</a>
</nav>

<div class="main">
  <h2>Sefer Bilgilerini Güncelle</h2>
  <form method="POST">
    <label>Kalkış Şehri:</label>
    <input type="text" name="departure_city" value="<?= htmlspecialchars($trip['departure_city']) ?>">

    <label>Varış Şehri:</label>
    <input type="text" name="destination_city" value="<?= htmlspecialchars($trip['destination_city']) ?>">

    <label>Kalkış Zamanı:</label>
    <input type="datetime-local" name="departure_time" value="<?= date('Y-m-d\TH:i', strtotime($trip['departure_time'])) ?>">

    <label>Varış Zamanı:</label>
    <input type="datetime-local" name="arrival_time" value="<?= date('Y-m-d\TH:i', strtotime($trip['arrival_time'])) ?>">

    <label>Fiyat:</label>
    <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($trip['price']) ?>">

    <label>Koltuk Sayısı:</label>
    <input type="number" name="capacity" value="<?= htmlspecialchars($trip['capacity']) ?>">

    <button type="submit">Güncelle</button>
  </form>

  <h2>Bu Seferi Alan Kullanıcılar</h2>
  <table>
    <thead>
      <tr>
        <th>Kullanıcı Adı</th>
        <th>Koltuk No</th>
        <th>Bilet Tarihi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $db->prepare("SELECT User.full_name, Booked_Seats.seat_number, Tickets.created_at
                            FROM Tickets
                            JOIN User ON Tickets.user_id = User.id
                            JOIN Booked_Seats ON Booked_Seats.ticket_id = Tickets.id
                            WHERE Tickets.trip_id = ?");
      $stmt->execute([$trip_id]);
      $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($bookings) {
        foreach ($bookings as $booking) {
          echo "<tr>
                  <td>" . htmlspecialchars($booking['full_name']) . "</td>
                  <td>" . htmlspecialchars($booking['seat_number']) . "</td>
                  <td>" . htmlspecialchars($booking['created_at']) . "</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='3'>Henüz bilet alınmamış.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<script>
  const toggleBtn = document.getElementById('toggleBtn');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });
</script>

</body>
</html>
