

<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Registrations.php'; // เรียกใช้ฟังก์ชันที่เพิ่งเพิ่ม

// ตรวจสอบว่าล็อกอินหรือยัง
if (empty($_SESSION['user_id'])) {
    header("Location: /templates/sign_in.php");
    exit();
}

// ดึงประวัติการลงทะเบียนของคนที่ล็อกอินอยู่
$user_id = $_SESSION['user_id'];
$history = getUserHistory($user_id);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการเข้าร่วมกิจกรรม</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-pending { color: #f39c12; font-weight: bold; } /* สีส้ม */
        .text-approved { color: #27ae60; font-weight: bold; } /* สีเขียว */
        .text-rejected { color: #e74c3c; font-weight: bold; } /* สีแดง */
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?> 

    <h2>📜 ประวัติการขอเข้าร่วมกิจกรรมของคุณ</h2>
    <a href="/templates/home.php" style="text-decoration: none;">⬅ กลับหน้ารายการกิจกรรม</a>

    <table>
        <thead>
            <tr>
                <th>ชื่อกิจกรรม</th>
                <th>วันที่เริ่ม</th>
                <th>สถานที่</th>
                <th>สถานะการเข้าร่วม</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['start_date'])); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <?php 
                        // ตรวจสอบสถานะและใส่สีให้ข้อความ
                        $status = empty($row['status']) ? 'pending' : strtolower($row['status']); 
                        $class_name = "text-" . $status;
                    ?>
                    <td class="<?php echo $class_name; ?>">
                        <?php 
                            if ($status == 'approved') echo '✅ อนุมัติแล้ว';
                            elseif ($status == 'rejected') echo '❌ ปฏิเสธ';
                            else echo '⏳ รออนุมัติ';
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">คุณยังไม่มีประวัติการลงทะเบียนกิจกรรม</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>