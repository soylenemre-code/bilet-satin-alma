<?php
session_start();
//require_once __DIR__ . '/db(database)/includes/db.php';
require_once __DIR__ . '/../db(database)/includes/db.php';

if(!isset( $_SESSION['role']) ||  $_SESSION['role']!=='admin'){
     header("HTTP/1.1 301 Moved Permanently");
     header("Location: /index.php");
     exit();
}

try{
     $stmt = $db->prepare("SELECT * FROM User WHERE role = 'firmadmin'");
     $stmt->execute();
     $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

}catch (PDOException $e) { 
 $error = "Veritabanı hatası: " . $e->getMessage();
}
if($_SERVER ['REQUEST_METHOD']=='POST'){
            header("Location:useradd.php");
            exit();
         }

if(isset($_GET['action'])&& $_GET['action']=='delete' && isset($_GET['id'])){
    $id=($_GET['id']);
    if($id){
        try{
            $stmt=$db->prepare("DELETE FROM User WHERE id=?");
            $stmt->execute([$id]);
            header("Location: users.php");
            exit();
        }catch (PDOException $e){
            echo "Veritabanından silinirken bilinmeyen bir hata oluştu";
            exit();
        }
   }

}



?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Kullanıcı Listesi</title>
     <style>
        table{
           width:100%;
           border-collapse:collapse;
           margin-top:20px;}
        th, td{
            border: 1px solid #ad0a0aff;
            padding:8px;
            text-align:left;
        }th{
            background-color: yellowgreen;
        }tr:nth-child(even){
            background-color:#f9f9f9;
        }.action-btn{
            padding: 5px 10px;
            text-decoration:none;
            color:black;
            border-radius:3px;
        }delete-btn{
            background-color: red;
        }.edit-btn{
            background-color:#2196F3;
        }.new-trip-btn{
            background-color: #4caf50;
            color:white;
            padding: 10px 15px;
            border:none;
            cursor:pointer;
            border-radius: 4px;
        }
        </style>

        
     </head>

     <body>
        
     <h1> OTOBÜS SEFERLERİ</h1>
     <?php if(isset($error)): ?>
        <div style="color: red;"><?php echo $error; ?></div> <?php endif; ?>
            
    <form method="POST" action="">
        <button type="submit" class="new-trip-btn">Yeni Kullanıcı ekle</button>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>İSİM-SOYİSİM</th>
                <th>Mail Adresi</th>
                <th>Şifre(hashli)</th>
                <th>Kullanıcı Rolü</th>
                <th>FirmaId</th>
                <th>BAKİYE</th>
                <th>Eklenme Tarihi</th>  
                <th>DÜZENLE/SİL</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($trips)): ?>
                <?php foreach($trips as $trip): ?>
                    <tr>    
                        <td><?php echo htmlspecialchars($trip['id']); ?></td>
                        <td><?php echo htmlspecialchars($trip['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($trip['email']); ?></td>
                        <td><?php echo htmlspecialchars($trip['password']); ?></td>
                        <td><?php echo htmlspecialchars($trip['role']); ?></td>
                       <td><?php echo isset($trip['company_id']) ? htmlspecialchars($trip['company_id']) : '—'; ?></td>
                        <td><?php echo htmlspecialchars($trip['balance']); ?></td>
                        <td><?php echo htmlspecialchars($trip['created_at']); ?></td>

                        
                            <td><a href="useredit.php?id=<?php echo $trip['id']; ?>" class="action-btn edit-btn">Düzenle</a>
                            <a href="?action=delete&id=<?php echo $trip['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">Veritabanında Kullanıcı Bulunamadı!!!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
</body>
</html>
