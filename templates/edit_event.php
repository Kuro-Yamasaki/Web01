<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Events.php';

$id = $_GET['id'] ?? 0;
$event = getEventById($id);

if (!$event) {
    die("ไม่พบข้อมูลกิจกรรมที่คุณต้องการแก้ไข");
}


$start_date_formatted = date('Y-m-d\TH:i', strtotime($event['start_date']));
$end_date_formatted = date('Y-m-d\TH:i', strtotime($event['end_date']));
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>แก้ไขกิจกรรม</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <h2>แก้ไขกิจกรรม: <?php echo htmlspecialchars($event['event_name']); ?></h2>
    
    <form action="/routes/Event.php" method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
        
        <label>ชื่อกิจกรรม:</label><br>
        <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required><br><br>
        
        <label>รายละเอียด:</label><br>
        <textarea name="description"><?php echo htmlspecialchars($event['description']); ?></textarea><br><br>
        
        <label>วันที่เริ่ม:</label><br>
        <input type="datetime-local" name="start_date" value="<?php echo $start_date_formatted; ?>" required><br><br>
        
        <label>วันที่สิ้นสุด:</label><br>
        <input type="datetime-local" name="end_date" value="<?php echo $end_date_formatted; ?>" required><br><br>
        
        <label>จำนวนที่รับ (คน):</label><br>
        <input type="number" name="max_participants" value="<?php echo $event['max_participants']; ?>"><br><br>
        
        <label>สถานที่:</label><br>
        <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>"><br><br>
        
        <button type="submit">บันทึกการแก้ไข</button>
        <a href="/templates/home.php">ยกเลิก</a>
    </form>
</body>
</html>