<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../Include/database.php';
require_once '../databases/Events.php'; 
require_once '../databases/Registrations.php';

$event_id = $_GET['event_id'] ?? 0;

if ($event_id == 0) {
    die("ไม่พบรหัสกิจกรรม กรุณากลับไปเลือกกิจกรรมใหม่");
}

$event = getEventById($event_id);
$registrations = getRegistrationsByEvent($event_id);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ลงทะเบียน</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-pending {
            color: #f39c12;
            font-weight: bold;
        }

      
        .text-approved {
            color: #27ae60;
            font-weight: bold;
        }

        
        .text-rejected {
            color: #e74c3c;
            font-weight: bold;
        }

       
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 3px;
            background: #eee;
            color: #333;
        }

        .btn:hover {
            background: #ddd;
        }
    </style>
</head>

<body>

    <h2>รายชื่อผู้ลงทะเบียนขอเข้าร่วมกิจกรรม: <?php echo htmlspecialchars($event['event_name']); ?></h2>
    <a href="/templates/home.php">⬅ กลับหน้ารายการกิจกรรม</a>

    <table>
        <thead>
            <tr>
                <th>รหัสสมัคร</th>
                <th>ชื่อ-นามสกุล</th>
                <th>เพศ</th>
                <th>จังหวัด</th>
                <th>สถานะปัจจุบัน</th>
                <th>จัดการอนุมัติ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $reg): ?>
                    <tr>
                        <td><?php echo $reg['registration_id']; ?></td>
                        <td style="text-align: left;"><?php echo htmlspecialchars($reg['name']); ?></td>
                        <td><?php echo htmlspecialchars($reg['gender']); ?></td>
                        <td><?php echo htmlspecialchars($reg['province']); ?></td>

                        <?php
                        $status = empty($reg['status']) ? 'Pending' : $reg['status'];
                        $class_name = "text-" . strtolower($status);
                        ?>
                        <td class="<?php echo $class_name; ?>"><?php echo $status; ?></td>

                        <td>
                            <?php if ($status != 'Approved'): ?>
                                <form action="/routes/Registration.php" method="POST" style="display:inline-block; margin: 0;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn" style="cursor: pointer; color: #27ae60;" onclick="return confirm('ยืนยันการอนุมัติ?');">✅ อนุมัติ</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($status != 'Rejected'): ?>
                                <form action="/routes/Registration.php" method="POST" style="display:inline-block; margin: 0;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn" style="cursor: pointer; color: #e74c3c;" onclick="return confirm('ยืนยันการปฏิเสธ?');">❌ ปฏิเสธ</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">ยังไม่มีผู้ลงทะเบียนในกิจกรรมนี้</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>