<?php
session_start();     


$departure_city = isset($_GET['departure_city']) ? $_GET['departure_city'] : '';
$destination_city = isset($_GET['destination_city']) ? $_GET['destination_city'] : '';
$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['departure_city'], $_GET['destination_city'])) {
    if (empty($departure_city) || empty($destination_city)) {
        $errors[] = "Kalkış ve varış şehirleri seçilmelidir!";
    } elseif ($departure_city === $destination_city) {
        $errors[] = "Kalkış ve varış şehirleri aynı olamaz!";
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Siber Vatan Otobüs Bilet Satış Sistemi</title>
  <style>
    header {
      display: flex;
      gap: 20px;
      padding: 10px;
      background-image: url('Background(4).png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 1680px;
      position: relative;
    }

    a {
      text-decoration: none;
      color: black;
      font-weight: bold;
    }

    a:hover {
      color: #007bff;
    }

    .search-area {
      position: absolute;
      top: 570px;
      left: 1249px;
      width: 300px;
      height: 70px;
      background: transparent;
      cursor: pointer;
    }

    .login-area {
      position: absolute;
      top: 80px;
      left: 1290px;
      width: 200px;
      height: 60px;
      background: transparent;
      cursor: pointer;
    }

    .register-area {
      position: absolute;
      top: 80px;
      left: 1528px;
      width: 275px;
      height: 50px;
      background: transparent;
      cursor: pointer;
    }

    .select1 {
      position: absolute;
      top: 600px;
      left: 24%;
      transform: translateX(-50%);
      width: 160px;
      height: 40px;
      font-size: 16px;
      padding: 5px;
      border-radius: 5px;
    }

    .select2 {
      position: absolute;
      top: 600px;
      left: 43%;
      transform: translateX(-50%);
      width: 160px;
      height: 40px;
      font-size: 16px;
      padding: 5px;
      border-radius: 5px;
    }
    
    .date {
      position: absolute;
      top: 600px;
      left: 59%; 
      transform: translateX(-50%);
      width: 160px;
      height: 40px;
      font-size: 16px;
      padding: 5px;
      border-radius: 5px;
    }
    
    .date-label {
      position: absolute;
      top: 570px;
      left: 59%;
      transform: translateX(-50%);
      font-weight: bold;
    }
    

  .search-button {
    position: absolute;
    top: 600px;
    left: 73%;
    transform: translateX(-50%);
    width: 260px;
    height: 60px;
    background-color: transparent;
    color: white;
    border: 2px solid #007bff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.4); 
  }

  .search-button:hover {
    background-color: transparent;
  }
  .top-buttons {
  position: fixed;
  top: 20px;
  right: 30px;
  z-index: 1000;
}

.top-buttons a {
  display: inline-block;
  margin-left: 10px;
  padding: 30px 50px; 
  width: 220px; 
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  border: 2px #08d1ca;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  font-size: 27px;
  backdrop-filter: blur(2px);
  box-shadow: 0 0 10px rgba(5, 218, 214, 0.4);
  transition: background-color 0.3s ease;
  text-align: center; 
}


.top-buttons a:hover {
  background-color: rgba(50, 50, 50, 0.8); 
}


.top-buttons a:hover {
  background-color: rgba(0, 123, 255, 0.5);
}

  </style>
</head>
<body>
    <div class="top-buttons">
  <a href="login.php">Giriş Yap</a>
  <a href="register.php">Kayıt Ol</a>
</div>

  <header>
    <a href="login.php" class="login-area"></a>
    <a href="register.php" class="register-area"></a>
  </header>
  
 
  <form action="trips.php" method="GET">
    <label for="date" class="date-label">Tarih Seçin:</label>
    <input type="date" id="date" name="date" class="date" required>
    
    <select id="select1" name="departure_city" class="select1" required>
      <option value="" selected disabled>Lütfen bir il seçiniz</option>
      <option value="Adana">Adana</option>
      <option value="Adıyaman">Adıyaman</option>
      <option value="Afyonkarahisar">Afyonkarahisar</option>
      <option value="Ağrı">Ağrı</option>
      <option value="Aksaray">Aksaray</option>
      <option value="Amasya">Amasya</option>
      <option value="Ankara">Ankara</option>
      <option value="Antalya">Antalya</option>
      <option value="Ardahan">Ardahan</option>
      <option value="Artvin">Artvin</option>
      <option value="Aydın">Aydın</option>
      <option value="Balıkesir">Balıkesir</option>
      <option value="Bartın">Bartın</option>
      <option value="Batman">Batman</option>
      <option value="Bayburt">Bayburt</option>
      <option value="Bilecik">Bilecik</option>
      <option value="Bingöl">Bingöl</option>
      <option value="Bitlis">Bitlis</option>
      <option value="Bolu">Bolu</option>
      <option value="Burdur">Burdur</option>
      <option value="Bursa">Bursa</option>
      <option value="Çanakkale">Çanakkale</option>
      <option value="Çankırı">Çankırı</option>
      <option value="Çorum">Çorum</option>
      <option value="Denizli">Denizli</option>
      <option value="Diyarbakır">Diyarbakır</option>
      <option value="Düzce">Düzce</option>
      <option value="Edirne">Edirne</option>
      <option value="Elazığ">Elazığ</option>
      <option value="Erzincan">Erzincan</option>
      <option value="Erzurum">Erzurum</option>
      <option value="Eskişehir">Eskişehir</option>
      <option value="Gaziantep">Gaziantep</option>
      <option value="Giresun">Giresun</option>
      <option value="Gümüşhane">Gümüşhane</option>
      <option value="Hakkari">Hakkari</option>
      <option value="Hatay">Hatay</option>
      <option value="Iğdır">Iğdır</option>
      <option value="Isparta">Isparta</option>
      <option value="İstanbul">İstanbul</option>
      <option value="İzmir">İzmir</option>
      <option value="Kahramanmaraş">Kahramanmaraş</option>
      <option value="Karabük">Karabük</option>
      <option value="Karaman">Karaman</option>
      <option value="Kars">Kars</option>
      <option value="Kastamonu">Kastamonu</option>
      <option value="Kayseri">Kayseri</option>
      <option value="Kilis">Kilis</option>
      <option value="Kırıkkale">Kırıkkale</option>
      <option value="Kırklareli">Kırklareli</option>
      <option value="Kırşehir">Kırşehir</option>
      <option value="Kocaeli">Kocaeli</option>
      <option value="Konya">Konya</option>
      <option value="Kütahya">Kütahya</option>
      <option value="Malatya">Malatya</option>
      <option value="Manisa">Manisa</option>
      <option value="Mardin">Mardin</option>
      <option value="Mersin">Mersin</option>
      <option value="Muğla">Muğla</option>
      <option value="Muş">Muş</option>
      <option value="Nevşehir">Nevşehir</option>
      <option value="Niğde">Niğde</option>
      <option value="Ordu">Ordu</option>
      <option value="Osmaniye">Osmaniye</option>
      <option value="Rize">Rize</option>
      <option value="Sakarya">Sakarya</option>
      <option value="Samsun">Samsun</option>
      <option value="Şanlıurfa">Şanlıurfa</option>
      <option value="Siirt">Siirt</option>
      <option value="Sinop">Sinop</option>
      <option value="Şırnak">Şırnak</option>
      <option value="Sivas">Sivas</option>
      <option value="Tekirdağ">Tekirdağ</option>
      <option value="Tokat">Tokat</option>
      <option value="Trabzon">Trabzon</option>
      <option value="Tunceli">Tunceli</option>
      <option value="Uşak">Uşak</option>
      <option value="Van">Van</option>
      <option value="Yalova">Yalova</option>
      <option value="Yozgat">Yozgat</option>
      <option value="Zonguldak">Zonguldak</option>
    </select>

    <select id="select2" name="destination_city" class="select2" required>
      <option value="" selected disabled>Lütfen bir il seçiniz</option>
      <option value="Adana">Adana</option>
      <option value="Adıyaman">Adıyaman</option>
      <option value="Afyonkarahisar">Afyonkarahisar</option>
      <option value="Ağrı">Ağrı</option>
      <option value="Aksaray">Aksaray</option>
      <option value="Amasya">Amasya</option>
      <option value="Ankara">Ankara</option>
      <option value="Antalya">Antalya</option>
      <option value="Ardahan">Ardahan</option>
      <option value="Artvin">Artvin</option>
      <option value="Aydın">Aydın</option>
      <option value="Balıkesir">Balıkesir</option>
      <option value="Bartın">Bartın</option>
      <option value="Batman">Batman</option>
      <option value="Bayburt">Bayburt</option>
      <option value="Bilecik">Bilecik</option>
      <option value="Bingöl">Bingöl</option>
      <option value="Bitlis">Bitlis</option>
      <option value="Bolu">Bolu</option>
      <option value="Burdur">Burdur</option>
      <option value="Bursa">Bursa</option>
      <option value="Çanakkale">Çanakkale</option>
      <option value="Çankırı">Çankırı</option>
      <option value="Çorum">Çorum</option>
      <option value="Denizli">Denizli</option>
      <option value="Diyarbakır">Diyarbakır</option>
      <option value="Düzce">Düzce</option>
      <option value="Edirne">Edirne</option>
      <option value="Elazığ">Elazığ</option>
      <option value="Erzincan">Erzincan</option>
      <option value="Erzurum">Erzurum</option>
      <option value="Eskişehir">Eskişehir</option>
      <option value="Gaziantep">Gaziantep</option>
      <option value="Giresun">Giresun</option>
      <option value="Gümüşhane">Gümüşhane</option>
      <option value="Hakkari">Hakkari</option>
      <option value="Hatay">Hatay</option>
      <option value="Iğdır">Iğdır</option>
      <option value="Isparta">Isparta</option>
      <option value="İstanbul">İstanbul</option>
      <option value="İzmir">İzmir</option>
      <option value="Kahramanmaraş">Kahramanmaraş</option>
      <option value="Karabük">Karabük</option>
      <option value="Karaman">Karaman</option>
      <option value="Kars">Kars</option>
      <option value="Kastamonu">Kastamonu</option>
      <option value="Kayseri">Kayseri</option>
      <option value="Kilis">Kilis</option>
      <option value="Kırıkkale">Kırıkkale</option>
      <option value="Kırklareli">Kırklareli</option>
      <option value="Kırşehir">Kırşehir</option>
      <option value="Kocaeli">Kocaeli</option>
      <option value="Konya">Konya</option>
      <option value="Kütahya">Kütahya</option>
      <option value="Malatya">Malatya</option>
      <option value="Manisa">Manisa</option>
      <option value="Mardin">Mardin</option>
      <option value="Mersin">Mersin</option>
      <option value="Muğla">Muğla</option>
      <option value="Muş">Muş</option>
      <option value="Nevşehir">Nevşehir</option>
      <option value="Niğde">Niğde</option>
      <option value="Ordu">Ordu</option>
      <option value="Osmaniye">Osmaniye</option>
      <option value="Rize">Rize</option>
      <option value="Sakarya">Sakarya</option>
      <option value="Samsun">Samsun</option>
      <option value="Şanlıurfa">Şanlıurfa</option>
      <option value="Siirt">Siirt</option>
      <option value="Sinop">Sinop</option>
      <option value="Şırnak">Şırnak</option>
      <option value="Sivas">Sivas</option>
      <option value="Tekirdağ">Tekirdağ</option>
      <option value="Tokat">Tokat</option>
      <option value="Trabzon">Trabzon</option>
      <option value="Tunceli">Tunceli</option>
      <option value="Uşak">Uşak</option>
      <option value="Van">Van</option>
      <option value="Yalova">Yalova</option>
      <option value="Yozgat">Yozgat</option>
      <option value="Zonguldak">Zonguldak</option>
    </select>
    
    <button type="submit" class="search-button"></button>
  </form>

</body>
</html>
