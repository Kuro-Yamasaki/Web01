<?php
session_start();
require_once '../Include/database.php';

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'] ?? 0;

// สร้างรหัส OTP สำหรับคนนี้ ณ เวลานี้
$my_otp = getDynamicOTP($user_id, $event_id);

// คำนวณเวลาที่เหลือในรอบ 30 นาทีนี้ (เพื่อทำตัวนับถอยหลัง)
$seconds_left = 1800 - (time() % 1800); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>รหัสเข้างาน</title>
</head>
<body style="font-family: sans-serif; text-align: center; padding: 50px; background: #f4f6f9;">
    
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 400px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2>🎟️ รหัสเข้างานของคุณ</h2>
        <p>กรุณาแสดงรหัสนี้ให้ผู้จัดงาน</p>
        
        <h1 style="font-size: 48px; color: #3498db; letter-spacing: 5px; margin: 10px 0;">
            <?php echo $my_otp; ?>
        </h1>
        
        <p style="color: #e74c3c; font-weight: bold;">
            ⏳ รหัสจะเปลี่ยนใหม่ใน: <span id="timer"></span> นาที
        </p>
        
        <br>
        <a href="history.php" style="color: #888;">กลับหน้าประวัติ</a>
    </div>

    <script>
        let timeLeft = <?php echo $seconds_left; ?>;
        
        setInterval(function() {
            if (timeLeft <= 0) {
                location.reload(); // ถ้านับครบ 0 ให้รีเฟรชหน้าเพื่อเปลี่ยนรหัสใหม่
            }
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            document.getElementById("timer").innerText = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            timeLeft--;
        }, 1000);
    </script>
</body>
</html>