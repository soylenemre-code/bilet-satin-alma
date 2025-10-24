<?php
session_start();
require_once __DIR__ . '/../db(database)/includes/db.php';


$company_id = $departure_city = $destination_city = $arrival_time = $departure_time = $price = $capacity = $created_date = '';
$error = '';
$success = '';
$companies=[];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

try {
    $stmt = $db->query("SELECT id, name FROM Bus_Company");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Firma listesi alınamadı: " . $e->getMessage();
}
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    //$id=$_POST['id'];
    $company_id=$_POST['company_id'] ?? null;
    $departure_city=$_POST['departure_city'] ?? null;
    $destination_city=$_POST['destination_city'] ?? null;
    $arrival_time=$_POST['arrival_time'] ?? null;
    $departure_time=$_POST['departure_time'] ?? null;
    $price=$_POST['price'] ?? null;
    $capacity=$_POST['capacity'] ?? null;
    $created_date = date('Y-m-d H:i:s');
    

    
} if (!$company_id || !$destination_city || !$arrival_time || !$departure_time || !$departure_city || !$price || !$capacity || !$created_date) {
        $error = "Lütfen tüm alanları doldurunuz.";
    }else{ $created_timestamp=strtotime($created_date);
        $current_timestamp=time();
        $arrival_timestamp=strtotime($arrival_time);
        $departure_timestamp=strtotime($departure_time);
        
        if($created_timestamp < $current_timestamp || $arrival_timestamp < $current_timestamp || $departure_timestamp < $current_timestamp){
            $error="Geçmiş zaman için sefer oluşturulamaz";
        }else {
                try {
                    $sql_insert = "INSERT INTO Trips 
                        (company_id, departure_city, destination_city, arrival_time, departure_time, price, capacity, created_date)
                        VALUES (:company_id, :departure_city, :destination_city, :arrival_time, :departure_time, :price, :capacity, :created_date)";
                    $stmt = $db->prepare($sql_insert);
                    $stmt->execute([
                        ':company_id' => $company_id,
                        ':departure_city' => $departure_city,
                        ':destination_city' => $destination_city,
                        ':arrival_time' => $arrival_time,
                        ':departure_time' => $departure_time,
                        ':price' => $price,
                        ':capacity' => $capacity,
                        ':created_date' => $created_date
                    ]);
                    $trip_id=$db->lastInsertId();
                    for($i=1; $i <= $capacity; $i++){
                       $seat_stmt = $db->prepare("INSERT INTO Seats (trip_id, seat_number) VALUES (?, ?)");
                       $seat_stmt->execute([$trip_id, $i]);
                    }
                    $success = "Sefer başarıyla oluşturuldu!";
                } catch(PDOException $e) {
                    $error = "Bilinmeyen bir sorun oluştu: ". $e->getMessage();
                }
            }
        }
    

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Sefer Ekle</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], input[type="datetime-local"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        input[type="submit"] { padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Yeni Sefer Oluştur</h2>

    <?php
   
    if (!empty($error)) {
        echo '<p class="error">' . $error . '</p>';
    }
    if (!empty($success)) {
        echo '<p class="success">' . $success . '</p>';
    }
    ?>

    
    <form action="" method="POST">
        <div class="form-group">
             <label for="company_id">Firma Seçiniz:</label>
    <select id="company_id" name="company_id" required>
        <option selected disabled>Firma seçiniz</option>
        <?php foreach ($companies as $company): ?>
            <option value="<?php echo htmlspecialchars($company['id']); ?>">
                <?php echo htmlspecialchars($company['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
            
        </div>
        <label for="departure_city">Kalkış Noktası:</label>
     <select id="departure_city" name="departure_city" required>
    <option selected disabled>Lütfen bir il seçiniz</option>
    <option>Adana</option>
    <option>Adıyaman</option>
    <option>Afyonkarahisar</option>
    <option>Ağrı</option>
    <option>Aksaray</option>
    <option>Amasya</option>
    <option>Ankara</option>
    <option>Antalya</option>
    <option>Ardahan</option>
    <option>Artvin</option>
    <option>Aydın</option>
    <option>Balıkesir</option>
    <option>Bartın</option>
    <option>Batman</option>
    <option>Bayburt</option>
    <option>Bilecik</option>
    <option>Bingöl</option>
    <option>Bitlis</option>
    <option>Bolu</option>
    <option>Burdur</option>
    <option>Bursa</option>
    <option>Çanakkale</option>
    <option>Çankırı</option>
    <option>Çorum</option>
    <option>Denizli</option>
    <option>Diyarbakır</option>
    <option>Düzce</option>
    <option>Edirne</option>
    <option>Elazığ</option>
    <option>Erzincan</option>
    <option>Erzurum</option>
    <option>Eskişehir</option>
    <option>Gaziantep</option>
    <option>Giresun</option>
    <option>Gümüşhane</option>
    <option>Hakkari</option>
    <option>Hatay</option>
    <option>Iğdır</option>
    <option>Isparta</option>
    <option>İstanbul</option>
    <option>İzmir</option>
    <option>Kahramanmaraş</option>
    <option>Karabük</option>
    <option>Karaman</option>
    <option>Kars</option>
    <option>Kastamonu</option>
    <option>Kayseri</option>
    <option>Kilis</option>
    <option>Kırıkkale</option>
    <option>Kırklareli</option>
    <option>Kırşehir</option>
    <option>Kocaeli</option>
    <option>Konya</option>
    <option>Kütahya</option>
    <option>Malatya</option>
    <option>Manisa</option>
    <option>Mardin</option>
    <option>Mersin</option>
    <option>Muğla</option>
    <option>Muş</option>
    <option>Nevşehir</option>
    <option>Niğde</option>
    <option>Ordu</option>
    <option>Osmaniye</option>
    <option>Rize</option>
    <option>Sakarya</option>
    <option>Samsun</option>
    <option>Şanlıurfa</option>
    <option>Siirt</option>
    <option>Sinop</option>
    <option>Şırnak</option>
    <option>Sivas</option>
    <option>Tekirdağ</option>
    <option>Tokat</option>
    <option>Trabzon</option>
    <option>Tunceli</option>
    <option>Uşak</option>
    <option>Van</option>
    <option>Yalova</option>
    <option>Yozgat</option>
    <option>Zonguldak</option>
  </select>
        
  <label for="destination_city">Varış Noktası:</label>
  <select id="destination_city" name="destination_city" required>
    <option selected disabled>Lütfen bir il seçiniz</option>
    <option>Adana</option>
    <option>Adıyaman</option>
    <option>Afyonkarahisar</option>
    <option>Ağrı</option>
    <option>Aksaray</option>
    <option>Amasya</option>
    <option>Ankara</option>
    <option>Antalya</option>
    <option>Ardahan</option>
    <option>Artvin</option>
    <option>Aydın</option>
    <option>Balıkesir</option>
    <option>Bartın</option>
    <option>Batman</option>
    <option>Bayburt</option>
    <option>Bilecik</option>
    <option>Bingöl</option>
    <option>Bitlis</option>
    <option>Bolu</option>
    <option>Burdur</option>
    <option>Bursa</option>
    <option>Çanakkale</option>
    <option>Çankırı</option>
    <option>Çorum</option>
    <option>Denizli</option>
    <option>Diyarbakır</option>
    <option>Düzce</option>
    <option>Edirne</option>
    <option>Elazığ</option>
    <option>Erzincan</option>
    <option>Erzurum</option>
    <option>Eskişehir</option>
    <option>Gaziantep</option>
    <option>Giresun</option>
    <option>Gümüşhane</option>
    <option>Hakkari</option>
    <option>Hatay</option>
    <option>Iğdır</option>
    <option>Isparta</option>
    <option>İstanbul</option>
    <option>İzmir</option>
    <option>Kahramanmaraş</option>
    <option>Karabük</option>
    <option>Karaman</option>
    <option>Kars</option>
    <option>Kastamonu</option>
    <option>Kayseri</option>
    <option>Kilis</option>
    <option>Kırıkkale</option>
    <option>Kırklareli</option>
    <option>Kırşehir</option>
    <option>Kocaeli</option>
    <option>Konya</option>
    <option>Kütahya</option>
    <option>Malatya</option>
    <option>Manisa</option>
    <option>Mardin</option>
    <option>Mersin</option>
    <option>Muğla</option>
    <option>Muş</option>
    <option>Nevşehir</option>
    <option>Niğde</option>
    <option>Ordu</option>
    <option>Osmaniye</option>
    <option>Rize</option>
    <option>Sakarya</option>
    <option>Samsun</option>
    <option>Şanlıurfa</option>
    <option>Siirt</option>
    <option>Sinop</option>
    <option>Şırnak</option>
    <option>Sivas</option>
    <option>Tekirdağ</option>
    <option>Tokat</option>
    <option>Trabzon</option>
    <option>Tunceli</option>
    <option>Uşak</option>
    <option>Van</option>
    <option>Yalova</option>
    <option>Yozgat</option>
    <option>Zonguldak</option>
  </select>

        <div class="form-group">
            <label for="arrival_time">Varış Zamanı:</label>
            <input type="datetime-local" id="arrival_time" name="arrival_time" required>
        </div>
        </div>
        <div class="form-group">
            <label for="departure_time">Kalkış Zamanı:</label>
            <input type="datetime-local" id="departure_time" name="departure_time" required>
        </div>
        <div class="form-group">
            <label for="price">Fiyat:</label>
            <input type="number" id="price" name="price" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="capacity">Kapasite:</label>
            <input type="number" id="capacity" name="capacity" required>
        </div>
        <input type="submit" value="Seferi Oluştur">
    </form>

</body>
</html>