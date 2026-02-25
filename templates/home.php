<?php
session_start();

require_once '../Include/database.php';
require_once '../databases/Events.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /templates/sign_in.php");
    exit();
}

$events = getEventsForHome($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการกิจกรรมทั้งหมด</title>
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
                <th>การเข้าร่วม</th>
                
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
                            <form action="/routes/Registration.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="request_join">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" onclick="return confirm('ต้องการขอเข้าร่วมกิจกรรมนี้ใช่หรือไม่?');" style="cursor: pointer; padding: 5px 10px; background-color: #4CAF50; color: white; border: none; border-radius: 3px;">
                                    ขอเข้าร่วม
                                </button>
                            </form>
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