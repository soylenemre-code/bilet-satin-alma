<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veri Tabanına Bağlantı Hatası: " . $e->getMessage();
    exit();
}
?>