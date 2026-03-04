<?php
session_start();
$user_id = $_SESSION['user_id'];
require_once '../Include/database.php';
require_once '../databases/Events.php';

$events = getEventsByOrganizer($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการกิจกรรม</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
        
        .action-links a {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
        }
        
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <?php include 'header.php' ?>
    <h2>จัดการกิจกรรม</h2>
    <a href="/templates/create_event.php" style="display: inline-block; background: #3498db; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">+ สร้างกิจกรรมใหม่</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่อกิจกรรม</th>
                <th>ผู้จัดงาน</th>
                <th>วันที่เริ่ม</th>
                <th>สถานที่</th>
                <th>ผู้เข้าร่วม (สูงสุด)</th>
                <th>จัดการ (แก้ไข/ลบ)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo $event['event_id']; ?></td>
                        <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($event['organizer_name']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($event['start_date'])); ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td><?php echo $event['max_participants']; ?> คน</td>
                        <td>
                            <div class="action-links">
                                <a href="/templates/edit_event.php?id=<?php echo $event['event_id']; ?>">✏️ แก้ไข</a> 
                                <a href="/routes/Event.php?action=delete&id=<?php echo $event['event_id']; ?>" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?');" style="color: #e74c3c;">🗑️ ลบ</a>
                                <br>
                                <a href="/templates/event_registrations.php?event_id=<?php echo $event['event_id']; ?>" style="display: inline-block; margin-top: 5px;">👥 ดูผู้สมัคร</a>
                            </div>

                            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">

                            <?php 
                            // ตั้งค่าโซนเวลาให้เป็นเวลาไทย
                            date_default_timezone_set('Asia/Bangkok');
                            $current_time = date('Y-m-d H:i:s');
                            
                            // เช็คว่างานเริ่มหรือยัง? (เช็คจาก is_otp_sent ว่าเป็น 1 หรือ เวลาปัจจุบันถึงเวลาเริ่มงานแล้ว)
                            $is_started = (!empty($event['is_otp_sent']) && $event['is_otp_sent'] == 1) || ($current_time >= $event['start_date']);
                            ?>

                            <?php if ($is_started): ?>
                                <a href="/templates/event_checkin.php?event_id=<?php echo $event['event_id']; ?>" 
                                   style="display: inline-block; background-color: #27ae60; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; text-align: center;">
                                   ✅ ตรวจคนเข้างาน
                                </a>
                            <?php else: ?>
                                <form action="/routes/event.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="start_event_generate_otp">
                                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                    <button type="submit" onclick="return confirm('ยืนยันเริ่มงาน? ระบบจะเปิดให้ผู้เข้าร่วมดูรหัสเข้างานได้ทันที');" 
                                            style="background-color: #f39c12; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                                        🚀 เริ่มงาน & แจกรหัส
                                    </button>
                                </form>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">ยังไม่มีกิจกรรมในระบบ</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>