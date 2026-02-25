<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Users.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = getUserByEmail($email);

    
    if ($user && password_verify($password, $user['password'])) {
       
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        
        echo "เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ " . $_SESSION['user_name'];
      
    } else {
        echo "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>