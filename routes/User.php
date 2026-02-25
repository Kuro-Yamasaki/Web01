<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Users.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // --- ส่วนสมัครสมาชิก ---
    if ($action == 'register') {
        $userData = [
            'name'      => $_POST['name']?? null,
            'gender'    => $_POST['gender']?? null,
            'birthdate' => $_POST['birthdate']?? null,
            'province'  => $_POST['province']?? null,
            'email'     => $_POST['email']?? null,
            'password'  => $_POST['password']?? null
        ];
        
        

        if (createUser($userData)) {
            echo "<script>
                    alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); 
                    window.location.href='/templates/sign_in.php';
                  </script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด! อีเมลนี้อาจมีผู้ใช้งานแล้ว'); window.history.back();</script>";
        }
        exit();
    
    // --- ส่วนเข้าสู่ระบบ ---
    } elseif ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $user = getUserByEmail($email);

        if ($user) {
            // จุดนี้แหละครับที่ระบบจะจำ ID และ ชื่อ (ต้องใช้ชื่อคอลัมน์ 'name' ตามฐานข้อมูลคุณ)
            $_SESSION['user_id'] = $user['user_id']; 
            $_SESSION['name'] = $user['name'];
            
            $show_name = htmlspecialchars($user['name']);
            echo "<script>
                    alert('เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ $show_name'); 
                    window.location.href='/templates/home.php';
                  </script>";
        } else {
            echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง'); window.history.back();</script>";
        }
        exit();
    }
}

// --- ส่วนออกจากระบบ (Logout) แบบ GET ---
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $get_action = $_GET['action'] ?? '';

    if ($get_action == 'logout') {
        session_unset(); 
        session_destroy(); 
        echo "<script>
                alert('ออกจากระบบเรียบร้อยแล้ว ไว้พบกันใหม่ครับ!');
                window.location.href='/templates/home.php';
              </script>";
        exit();
    }
}
?>