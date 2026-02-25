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

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>


    <?php include 'header.php' ?>
    <h2>จัดการกิจกรรม</h2>
    <a href="/templates/create_event.php"> + สร้างกิจกรรมใหม่</a>


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
                            <a href="/templates/edit_event.php?id=<?php echo $event['event_id']; ?>">แก้ไข</a>
                            <a href="/routes/Event.php?action=delete&id=<?php echo $event['event_id']; ?>" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?');">ลบ</a>
                            <a href="/templates/event_registrations.php?event_id=<?php echo $event['event_id']; ?>">ดูผู้สมัคร</a>
                            | <a href="/templates/event_checkin.php?event_id=<?php echo $event['event_id']; ?>">เช็คชื่อหน้างาน</a>
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