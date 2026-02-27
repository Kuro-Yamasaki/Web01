<?php
session_start();
require_once '../Include/database.php';

// 1. รับค่า id จาก URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$event_id = $_GET['id'];

// 2. ดึงข้อมูล Event และชื่อผู้จัด
$stmt = $conn->prepare("
    SELECT Events.*, Users.name AS organizer_name 
    FROM Events 
    LEFT JOIN Users ON Events.organizer_id = Users.user_id 
    WHERE Events.event_id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<script>alert('ไม่พบกิจกรรมที่คุณต้องการ'); window.location.href='home.php';</script>";
    exit();
}

// 3. ดึงข้อมูลรูปภาพของกิจกรรมนี้
$img_stmt = $conn->prepare("SELECT image_path FROM Event_Images WHERE event_id = ?");
$img_stmt->bind_param("i", $event_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();

$images = [];
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row['image_path'];
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['event_name']); ?></title>
    <style>
        /* สไตล์สำหรับกล่องแสดงรูปภาพ (Carousel) */
        .carousel-container {
            position: relative;
            width: 100%;
            height: 400px;
            /* ความสูงของกล่องรูปภาพ ปรับได้ตามต้องการ */
            background-color: #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .carousel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* ทำให้รูปเต็มกล่องพอดี ไม่เบี้ยว */
            display: none;
            /* ซ่อนรูปทั้งหมดไว้ก่อน */
        }

        .carousel-img.active {
            display: block;
            /* โชว์เฉพาะรูปที่มีคลาส active */
        }

        /* ปุ่มเลื่อนซ้าย-ขวา */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 20px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .nav-btn:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .prev-btn {
            left: 10px;
        }

        .next-btn {
            right: 10px;
        }

        /* ตัวนับจำนวนรูปภาพ */
        .img-counter {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .no-image-text {
            color: #888;
            font-size: 18px;
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f6f8fa; padding: 20px; margin: 0;">

    <?php include 'header.php'; ?>

    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

        <h1 style="color: #333; margin-top: 0; margin-bottom: 5px;"><?php echo htmlspecialchars($event['event_name']); ?></h1>
        <p style="color: #666; margin-top: 0;">จัดโดย: <strong><?php echo htmlspecialchars($event['organizer_name']); ?></strong></p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <div class="carousel-container">
            <?php if (count($images) > 0): ?>

                <?php foreach ($images as $index => $img_path): ?>
                    <img src="<?php echo htmlspecialchars($img_path); ?>"
                        class="carousel-img <?php echo $index === 0 ? 'active' : ''; ?>"
                        alt="ภาพกิจกรรม">
                <?php endforeach; ?>

                <?php if (count($images) > 1): ?>
                    <button class="nav-btn prev-btn" onclick="changeImage(-1)">&#10094;</button>
                    <button class="nav-btn next-btn" onclick="changeImage(1)">&#10095;</button>
                    <div class="img-counter"><span id="current-img-num">1</span> / <?php echo count($images); ?></div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-image-text">🖼️ ไม่มีรูปภาพประกอบ</div>
            <?php endif; ?>
        </div>
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <p style="margin: 5px 0;"><strong>📅 วันที่เริ่ม:</strong> <?php echo date('d/m/Y H:i', strtotime($event['start_date'])); ?></p>
            <p style="margin: 5px 0;"><strong>🏁 วันที่สิ้นสุด:</strong> <?php echo date('d/m/Y H:i', strtotime($event['end_date'])); ?></p>
            <p style="margin: 5px 0; grid-column: span 2;"><strong>📍 สถานที่:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p style="margin: 5px 0; grid-column: span 2;"><strong>👥 จำนวนที่รับ:</strong> <?php echo !empty($event['max_participants']) ? $event['max_participants'] . ' คน' : 'ไม่จำกัดจำนวน'; ?></p>
        </div>

        <h3 style="border-bottom: 2px solid #0969da; padding-bottom: 5px; display: inline-block;">รายละเอียดกิจกรรม</h3>
        <p style="line-height: 1.6; color: #444; font-size: 16px;">
            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
        </p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

        <div style="text-align: center;">
            <?php
            // ตั้งค่าโซนเวลาให้เป็นเวลาไทย
            date_default_timezone_set('Asia/Bangkok');
            $current_time = date('Y-m-d H:i:s');

            // เช็คแค่เวลาปัจจุบัน เลยเวลา end_date หรือยัง?
            $is_ended = ($current_time > $event['end_date']);
            ?>

            <?php if ($is_ended): ?>
                <button disabled style="background-color: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-size: 16px; cursor: not-allowed; font-weight: bold; opacity: 0.8;">
                    ❌ กิจกรรมนี้สิ้นสุดแล้ว
                </button>

            <?php elseif (isset($_SESSION['user_id'])): ?>
                <form action="/routes/Registration.php" method="POST">
                    <input type="hidden" name="action" value="request_join">
                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                    <button type="submit" onclick="return confirm('ยืนยันการขอเข้าร่วมกิจกรรมนี้?');" style="background-color: #2da44e; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: 0.2s;">
                        ลงทะเบียนเข้าร่วมงานนี้
                    </button>
                </form>

            <?php else: ?>
                <a href="sign_in.php" style="background-color: #0969da; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 16px; font-weight: bold;">
                    กรุณาเข้าสู่ระบบเพื่อลงทะเบียน
                </a>
            <?php endif; ?>
        </div>

    </div>

    <script>
        let currentIndex = 0;
        const images = document.querySelectorAll('.carousel-img');
        const counterNum = document.getElementById('current-img-num');

        function changeImage(step) {
            // ซ่อนรูปปัจจุบัน
            images[currentIndex].classList.remove('active');

            // คำนวณ index ใหม่
            currentIndex += step;

            // วนลูปกลับไปรูปแรก หรือรูปสุดท้าย
            if (currentIndex >= images.length) {
                currentIndex = 0;
            } else if (currentIndex < 0) {
                currentIndex = images.length - 1;
            }

            // แสดงรูปใหม่
            images[currentIndex].classList.add('active');

            // อัปเดตตัวเลข
            if (counterNum) {
                counterNum.innerText = currentIndex + 1;
            }
        }
    </script>
</body>

</html>