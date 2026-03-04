<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Events.php';

if (empty($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}

$events = getEventsByOrganizer($_SESSION['user_id']);

// ตั้งค่าเวลาไทย
date_default_timezone_set('Asia/Bangkok');
$current_time = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard จัดการกิจกรรม</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .btn-create { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; transition: 0.3s; }
        .btn-create:hover { background: #0056b3; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f3f5; color: #495057; padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }

        /* Status Badges */
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 12px; font-weight: bold; display: inline-block; }
        .badge-upcoming { background: #e7f1ff; color: #007bff; } /* ยังไม่เริ่ม */
        .badge-ongoing { background: #e6ffed; color: #28a745; }  /* เริ่มแล้ว */
        .badge-ended { background: #fff5f5; color: #dc3545; }    /* จบแล้ว */

        .action-btns a { text-decoration: none; font-size: 14px; margin-right: 10px; font-weight: 500; }
        .edit-link { color: #f39c12; }
        .delete-link { color: #e74c3c; }
        .view-link { color: #3498db; }
        
        .checkin-btn { background: #27ae60; color: white !important; padding: 5px 10px; border-radius: 4px; font-size: 12px !important; }
        .otp-btn { background: #f39c12; color: white !important; padding: 5px 10px; border-radius: 4px; font-size: 12px !important; border:none; cursor:pointer;}
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="header-flex">
            <h2>🛠️ จัดการกิจกรรมของคุณ</h2>
            <a href="create_event.php" class="btn-create">+ สร้างกิจกรรมใหม่</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ชื่อกิจกรรม</th>
                    <th>วันที่เริ่ม - สิ้นสุด</th>
                    <th>สถานะ</th>
                    <th>ผู้สมัคร</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): foreach ($events as $event): 
                    // ตรรกะเช็คสถานะ
                    if ($current_time < $event['start_date']) {
                        $status_label = "ยังไม่เริ่ม";
                        $status_class = "badge-upcoming";
                    } elseif ($current_time >= $event['start_date'] && $current_time <= $event['end_date']) {
                        $status_label = "กำลังดำเนินงาน";
                        $status_class = "badge-ongoing";
                    } else {
                        $status_label = "จบกิจกรรมแล้ว";
                        $status_class = "badge-ended";
                    }
                ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($event['event_name']); ?></strong><br>
                        <small style="color: #888;">📍 <?php echo htmlspecialchars($event['location']); ?></small>
                    </td>
                    <td>
                        <small>เริ่ม: <?php echo date('d/m/Y H:i', strtotime($event['start_date'])); ?></small><br>
                        <small>จบ: <?php echo date('d/m/Y H:i', strtotime($event['end_date'])); ?></small>
                    </td>
                    <td>
                        <span class="badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                    </td>
                    <td>
                        <a href="event_registrations.php?event_id=<?php echo $event['event_id']; ?>" class="view-link">
                            👥 ดูผู้สมัคร (<?php echo $event['max_participants']; ?>)
                        </a>
                    </td>
                    <td class="action-btns">
                        <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" class="edit-link">แก้ไข</a>
                        <a href="/routes/Event.php?action=delete&id=<?php echo $event['event_id']; ?>" class="delete-link" onclick="return confirm('ลบกิจกรรมนี้?');">ลบ</a>
                        
                        <div style="margin-top: 10px;">
                            <?php if ($status_label != "จบกิจกรรมแล้ว"): ?>
                                <?php if ($event['is_otp_sent'] == 1 || $current_time >= $event['start_date']): ?>
                                    <a href="event_checkin.php?event_id=<?php echo $event['event_id']; ?>" class="checkin-btn">✅ ตรวจคนเข้างาน</a>
                                <?php else: ?>
                                    <form action="/routes/event.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="start_event_generate_otp">
                                        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                        <button type="submit" class="otp-btn">🚀 เริ่มงาน/แจกรหัส</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" style="text-align:center; padding:50px; color:#999;">ยังไม่มีกิจกรรมที่คุณสร้าง</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>