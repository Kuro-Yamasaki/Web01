<?php
session_start();
require_once '../Include/database.php';

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'] ?? 0;

// ค้นหาข้อมูลการลงทะเบียนและรหัส OTP ของคนๆ นี้
$stmt = $conn->prepare("SELECT r.*, e.event_name FROM Registrations r JOIN Events e ON r.event_id = e.event_id WHERE r.user_id = ? AND r.event_id = ? AND r.status = 'approved'");
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$reg_data = $stmt->get_result()->fetch_assoc();

if (!$reg_data) {
    die("คุณยังไม่ได้รับการอนุมัติให้เข้างาน หรือไม่มีข้อมูล");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>ยืนยันการเข้างาน</title>
</head>
<body style="font-family: sans-serif; background-color: #f4f6f9; text-align: center; padding: 50px;">

    <div style="max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2>🎟️ เข้างาน: <?php echo htmlspecialchars($reg_data['event_name']); ?></h2>
        
        <?php if ($reg_data['is_checked_in'] == 1): ?>
            <h3 style="color: #27ae60;">✅ คุณยืนยันการเข้างานเรียบร้อยแล้ว</h3>
        <?php else: ?>
            
            <form action="/routes/Registration.php" method="POST" style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <input type="hidden" name="action" value="verify_otp">
                <input type="hidden" name="registration_id" value="<?php echo $reg_data['registration_id']; ?>">
                <label>กรอกรหัส 6 หลักที่ได้รับทางอีเมล:</label><br>
                <input type="text" name="otp_input" maxlength="6" required style="font-size: 24px; width: 150px; text-align: center; letter-spacing: 5px; margin: 10px 0;"><br>
                <button type="submit" style="background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">ยืนยันเข้างาน</button>
            </form>

            <?php if (!empty($reg_data['otp_code'])): ?>
                <p>หรือแสดง QR Code นี้ให้เจ้าหน้าที่สแกนหน้างาน</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?php echo urlencode($reg_data['otp_code']); ?>" alt="QR Code" style="border: 2px solid #ddd; padding: 10px; border-radius: 10px;">
                <h1 style="color: #e74c3c; letter-spacing: 5px;"><?php echo $reg_data['otp_code']; ?></h1>
            <?php else: ?>
                <p style="color: #f39c12;">รหัสเข้างานยังไม่ถูกสร้าง (ผู้จัดยังไม่เริ่มงาน)</p>
            <?php endif; ?>

        <?php endif; ?>
        
        <br>
        <a href="history.php" style="color: #888;">กลับหน้าประวัติ</a>
    </div>

</body>
</html>