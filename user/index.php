<?php
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Siber Vatan Otobüs Bilet Satış Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
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

        .search-block {
            max-width: 460px;
            margin: 60px auto;
            padding: 35px;
            background: rgba(255,255,255,0.96);
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
            text-align: center;
        }

        .search-block h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .search-block form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .search-block label {
            font-weight: 600;
            font-size: 17px;
            text-align: left;
        }

        input[type="date"],
        select,
        .search-button {
            height: 44px;
            padding: 0 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border 0.3s;
        }

        input[type="date"]:focus,
        select:focus {
            border-color: #007bff;
        }

        .search-button {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            border: none;
            margin-top: 10px;
        }

        .search-button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 480px) {
            nav { padding: 0 15px; }
            label.profile { font-size: 24px; }
            label.balance { font-size: 18px; }
            nav ul li a { font-size: 16px; padding: 6px 10px; }
            .search-block { margin: 30px 15px; padding: 25px; }
        }
    </style>
</head>
<body>


    <nav>
        <label class="profile">
            HOŞ GELDİNİZ: <?= htmlspecialchars($_SESSION['full_name'] ?? 'Kullanıcı') ?>
        </label>

        <ul>
            <li><a class="home" href="index.php">ANA SAYFA</a></li>
            <li><a href="tickets.php">BİLETLERİM</a></li>
            <li><a href="profile.php">PROFİLİM</a></li>
            <li><a href="logout.php">ÇIKIŞ YAP</a></li>
        </ul>

        <label class="balance">
            BAKİYENİZ: <?= htmlspecialchars($_SESSION['balance'] ?? '0') ?> ₺
        </label>
    </nav>

    <div class="search-block">
        <h2>Sefer Ara</h2>
        <form action="trips.php" method="GET">
            <label for="date">Tarih Seçin:</label>
            <input type="date" id="date" name="date" required>

            <label for="departure_city">Nereden:</label>
            <select id="departure_city" name="departure_city" required>
                <option value="" selected disabled>İl seçiniz</option>
                <?php
                $cities = [
                    "Adana","Adıyaman","Afyonkarahisar","Ağrı","Aksaray","Amasya","Ankara","Antalya","Ardahan","Artvin",
                    "Aydın","Balıkesir","Bartın","Batman","Bayburt","Bilecik","Bingöl","Bitlis","Bolu","Burdur",
                    "Bursa","Çanakkale","Çankırı","Çorum","Denizli","Diyarbakır","Düzce","Edirne","Elazığ","Erzincan",
                    "Erzurum","Eskişehir","Gaziantep","Giresun","Gümüşhane","Hakkari","Hatay","Iğdır","Isparta","İstanbul",
                    "İzmir","Kahramanmaraş","Karabük","Karaman","Kars","Kastamonu","Kayseri","Kilis","Kırıkkale","Kırklareli",
                    "Kırşehir","Kocaeli","Konya","Kütahya","Malatya","Manisa","Mardin","Mersin","Muğla","Muş","Nevşehir",
                    "Niğde","Ordu","Osmaniye","Rize","Sakarya","Samsun","Şanlıurfa","Siirt","Sinop","Şırnak","Sivas",
                    "Tekirdağ","Tokat","Trabzon","Tunceli","Uşak","Van","Yalova","Yozgat","Zonguldak"
                ];
                foreach ($cities as $city) {
                    echo "<option value=\"$city\">$city</option>";
                }
                ?>
            </select>

            <label for="destination_city">Nereye:</label>
            <select id="destination_city" name="destination_city" required>
                <option value="" selected disabled>İl seçiniz</option>
                <?php foreach ($cities as $city) {
                    echo "<option value=\"$city\">$city</option>";
                } ?>
            </select>

            <button type="submit" class="search-button">ARA</button>
        </form>
    </div>

</body>
</html>