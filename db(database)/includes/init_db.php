<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_file = __DIR__ . '/../database.db';
$db = new PDO("sqlite:$db_file");



try {
    $conn = new PDO("sqlite:$db_file");


    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Veritabanına başarıyla bağlanıldı.<br>";

    $sql_User = "CREATE TABLE IF NOT EXISTS User (
        id TEXT PRIMARY KEY DEFAULT (lower(hex(randomblob(16)))),
        full_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        company_id TEXT NULL,
        balance DEFAULT 800,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES Bus_Company(id)
    )";

    $sql_Bus_Company = "CREATE TABLE IF NOT EXISTS Bus_Company (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    logo_path TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";


    $sql_Trips = "CREATE TABLE IF NOT EXISTS Trips (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        company_id INTEGER NOT NULL,
        destination_city TEXT NOT NULL,
        arrival_time DATETIME NOT NULL,
        departure_time DATETIME NOT NULL,
        departure_city TEXT NOT NULL,
        price INTEGER NOT NULL,
        capacity INTEGER NOT NULL,
        created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES Bus_Company(id)
    )";

    $sql_Tickets = "CREATE TABLE IF NOT EXISTS Tickets (
        id TEXT PRIMARY KEY,
        trip_id INTEGER NOT NULL,
        user_id TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'ACTIVE',
        total_price INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (trip_id) REFERENCES Trips(id),
        FOREIGN KEY(user_id) REFERENCES User(id)
    )";

   $sql_Coupons = "CREATE TABLE IF NOT EXISTS Coupons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL,
    discount REAL NOT NULL,
    usage_limit INTEGER NOT NULL,
    expire_date DATE NOT NULL,
    company_id TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Bus_Company(id)
)";


     $sql_Booked_Seats = "CREATE TABLE IF NOT EXISTS Booked_Seats (
        id TEXT PRIMARY KEY,
        ticket_id TEXT NOT NULL,
        seat_number INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES Tickets(id)
    )";

    $sql_Seats = "CREATE TABLE IF NOT EXISTS Seats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    trip_id INTEGER NOT NULL,
    seat_number INTEGER NOT NULL,
    is_booked BOOLEAN DEFAULT 0,
    booked_by TEXT,
    FOREIGN KEY (trip_id) REFERENCES Trips(id),
    FOREIGN KEY (booked_by) REFERENCES User(id)
)";

    $sql_User_Coupons = "CREATE TABLE IF NOT EXISTS user_coupons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        coupon_id TEXT NOT NULL,
        user_id INTEGER NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES User(id),
        FOREIGN KEY(coupon_id) REFERENCES Coupons(id)

    )";


    $conn->exec($sql_User);
    echo "User tablosu oluşturuldu veya zaten mevcut.<br>";
     $conn->exec($sql_Booked_Seats);
    echo "User tablosu oluşturuldu veya zaten mevcut.<br>";
    $conn->exec($sql_Seats);
    echo "Sats tablosu oluşturuldu veya zaten mevcut.<br>";
    $conn->exec($sql_Trips);
    echo "trips tablosu oluşturuldu veya zaten mevcut.<br>";
      $conn->exec($sql_Tickets);
    echo "tickets tablosu oluşturuldu veya zaten mevcut.<br>";

    $conn->exec($sql_Coupons);
    echo "Coupons tablosu oluşturuldu veya zaten mevcut.<br>";

    // Coupons tablosunda company_id sütunu var mı kontrol et, yoksa ekle
$columns = $db->query("PRAGMA table_info(Coupons)")->fetchAll(PDO::FETCH_ASSOC);
$hasCompanyId = false;
foreach ($columns as $column) {
    if ($column['name'] === 'company_id') {
        $hasCompanyId = true;
        break;
    }
}

if (!$hasCompanyId) {
    try {
        $db->exec("ALTER TABLE Coupons ADD COLUMN company_id TEXT");
        echo "Coupons tablosuna company_id sütunu eklendi.<br>";
    } catch (PDOException $e) {
        echo "company_id sütunu eklenemedi: " . $e->getMessage() . "<br>";
    }
}


                                                  
                                                  
    $conn->exec($sql_Bus_Company);
    echo "Bus_company tablosu oluşturuldu veya zaten mevcut.<br>";
    $conn->exec($sql_User_Coupons);
    echo "user_coupons tablosu oluşturuldu veya zaten mevcut.<br>";

    echo "<br><strong>Tüm tablolar başarıyla oluşturuldu!</strong>";

} catch (PDOException $e) {
   
    die("Veritabanina Baglanilamadi Hatası: " . $e->getMessage());
}


 $conn = null;
?>