<?php
session_start();

// ถ้ายังไม่ล็อกอิน ให้เด้งกลับไปหน้า Login
if (!isset($_SESSION['user_id'])) {
    header("Location: /templates/sign_in.php");
    exit();
}

// ใช้ ?? 'ผู้ใช้งาน' เพื่อป้องกัน Error กรณีหาชื่อไม่เจอ
$user_name = $_SESSION['name'] ?? 'ผู้ใช้งาน';
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ข้อมูลบัญชี</title>
</head>

<body style="font-family: sans-serif; background-color: #f4f6f9; padding: 20px;">

    <?php include 'header.php' ?>

    <div style="max-width: 500px; margin: 40px auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color: #2c3e50; text-align: center;">👤 ข้อมูลบัญชีของคุณ</h2>
        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 20px;">

        <p style="font-size: 16px;">
            <b>ชื่อผู้ใช้:</b> <?php echo htmlspecialchars($user_name); ?> <br><br>
            <b>รหัสประจำตัว (User ID):</b> <span style="color: #e74c3c; font-weight: bold; font-size: 18px;"><?php echo $user_id; ?></span>
        </p>

        <hr style="border: 0; border-top: 1px solid #eee; margin-top: 20px; margin-bottom: 20px;">

        <div style="text-align: center;">
<<<<<<< HEAD
            

            
=======
            <a href="/templates/home.php" style="text-decoration: none; background: #ecf0f1; color: #333; padding: 8px 15px; border-radius: 4px; margin: 5px; display: inline-block;">🏠 กลับหน้าแรก</a>

            <a href="#" style="text-decoration: none; background: #3498db; color: white; padding: 8px 15px; border-radius: 4px; margin: 5px; display: inline-block;">📅 ประวัติการลงทะเบียน</a>
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d

            <a href="/routes/User.php?action=logout" style="text-decoration: none; background: #e74c3c; color: white; padding: 8px 15px; border-radius: 4px; margin: 5px; display: inline-block;" onclick="return confirm('ต้องการออกจากระบบใช่หรือไม่?');">🚪 ออกจากระบบ</a>
        </div>
    </div>

</body>

</html>