<?php
session_start();
require_once __DIR__ . '/db(database)/includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = 'user';

    if (!$full_name || !$email || !$password || !$role) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $id = strtolower(bin2hex(random_bytes(16)));

            $stmt = $db->prepare("INSERT INTO User (id, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $full_name, $email, $hashed_password, $role]);

            $success = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
            header("Refresh:2; url=login.php");
        } catch (PDOException $e) {
            $error = "Kayıt sırasında hata oluştu: Bu kullanıcı adı veya parola kayıtlı";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input, select, button {
            margin: 10px 0;
            padding: 8px;
            width: 80%;
            max-width: 300px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><b>KAYIT OL</b></h1>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color:green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Ad Soyad:</label>
            <input type="text" name="full_name">

            <label>E-posta:</label>
            <input type="email" name="email">

            <label>Şifre:</label>
            <input type="password" name="password">

            <label>Rol:</label>
            <select name="role">
                <option value="user">Yolcu</option>
            </select>

            <button type="submit">Kayıt Ol</button>
        </form>
    </div>
</body>
</html>