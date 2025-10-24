<?php
session_start();
require_once __DIR__ . '/db(database)/includes/db.php';



$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    //$id=$_GET['id'];

    if (!$email || !$password) {
        $error = "Lütfen boş alanları doldurun.";
    } 
    else  {
        try {
              $stmt = $db->prepare('SELECT id,email,password,role,full_name,balance,company_id From User WHERE email=?');
              $stmt->execute([$email]);
              $user=$stmt->fetch(PDO::FETCH_ASSOC);
              if($user&&password_verify($password, $user['password'])){
              $_SESSION['user_id']=$user['id'];
              $_SESSION['email']=$user['email'];
              $_SESSION['role']=$user['role'];
              $_SESSION['full_name']=$user['full_name'];
              $_SESSION['balance'] = $user['balance'];
              $_SESSION['company_id'] = $user['company_id'];



              $success="Giriş başarılı.";
              $_SESSION['user_id'] = $user['id'];




               if($user['role']==='admin'){
               header("Refresh:2; url=adminpanel/dashboard.php");
              }else if($user['role']==='user'){
             header("Location: user/index.php");
              exit();
              }else if($user['role']==='firmadmin'){ 
                header("Refresh:3; url=firmadminpanel/index.php");
            } else {
                    $error="eposta veya şifre yanlış.";
                }
                exit();
            } else {
                $error = "E-posta veya şifre yanlış.";
            }
        }  catch(PDOException $e) { 
            $error = "Veritabanı hatası: "; //. $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş</title>
    <style>
        header{
       display: flex;
       gap: 20px;
       padding:10px;
       background-size:cover;
       background-position: center;
       background-repeat: no-repeat;
       min-height: 1680px;
       position:relative;
       
}
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #05c364ff;
            background-color: #044f88ff;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff; 
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>


    </nav>
    <div class="col-md-3"></div>
    <div class="col-md-6 well">
    <h1 class="text-primary">SİBER VATAN BİLET SATIŞ SİSTEMİ
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>E-posta:</label>
        <input type="email" name="email" required><br>
        
        <label>Şifre:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Giriş Yap</button>
    </form>
</body>
</html>