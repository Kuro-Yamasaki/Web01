<?php
session_start();

require_once '../Include/database.php';
require_once '../databases/Events.php';
require_once '../databases/Registrations.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /templates/sign_in.php");
    exit();
}

// ตั้งค่าโซนเวลา (ประกาศครั้งเดียวไว้บนสุด)
date_default_timezone_set('Asia/Bangkok');
$current_time = date('Y-m-d H:i:s');

// รับค่าจากฟอร์มค้นหา
$search_name = $_GET['search_name'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// ดึงข้อมูลกิจกรรมทั้งหมด
$events = searchEventsForHome($_SESSION['user_id'], $search_name, $start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายการกิจกรรมทั้งหมด</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        /* ตกแต่งฟอร์มค้นหา */
        .search-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-container input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn-search {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-clear {
            background-color: #ecf0f1;
            color: #333;
            text-decoration: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-weight: bold;
        }

        /* โครงสร้าง Grid สำหรับ Card */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        /* ตกแต่ง Card */
        .event-card {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            text-decoration: none; /* เอาขีดเส้นใต้ออก */
            color: inherit;
            border: 1px solid #eaeaea;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }

        /* รูปภาพหน้าปก Card */
        .event-cover {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background-color: #eef2f5;
        }

        /* เนื้อหาใน Card */
        .event-body {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .event-meta {
            font-size: 13px;
            color: #888;
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .event-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .event-desc {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 15px;
            flex-grow: 1;
            /* ตัดคำให้โชว์แค่ 2 บรรทัด */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ส่วนท้ายของ Card (ผู้จัด และ สถานะ) */
        .event-footer {
            border-top: 1px solid #f0f0f0;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .organizer-info {
            font-size: 13px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* สีของสถานะมุมขวาล่าง */
        .status-text {
            font-size: 16px;
            font-weight: 800;
        }
        .status-join { color: #1abc9c; } /* สีเขียวมิ้นต์ เหมือนคำว่า "ฟรี" */
        .status-ended { color: #e74c3c; } /* สีแดง */
        .status-joined { color: #3498db; } /* สีฟ้า */
        .status-full { color: #95a5a6; } /* สีเทา */
        .status-pending { color: #f39c12; } /* สีส้ม */

    </style>
</head>

<body>

    <?php include 'header.php' ?>

    <h2 style="color: #2c3e50; margin-bottom: 20px;">🎉 กิจกรรมที่น่าสนใจ</h2>

    <div class="search-container">
        <form method="GET" action="" style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap;">
            <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="ค้นหาชื่อกิจกรรม..." style="flex-grow: 1;">
            <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            <span style="align-self: center;">ถึง</span>
            <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            <button type="submit" class="btn-search">🔍 ค้นหา</button>
            <a href="/templates/home.php" class="btn-clear">ล้างค่า</a>
        </form>
    </div>

    <div class="events-grid">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <?php
                // 1. ดึงรูปภาพแรกของกิจกรรมมาเป็นหน้าปก (ถ้าไม่มีให้ใช้ภาพ Default)
                $img_stmt = $conn->prepare("SELECT image_path FROM Event_Images WHERE event_id = ? LIMIT 1");
                $img_stmt->bind_param("i", $event['event_id']);
                $img_stmt->execute();
                $img_res = $img_stmt->get_result();
                $img_row = $img_res->fetch_assoc();
                $cover_image = $img_row ? $img_row['image_path'] : 'https://via.placeholder.com/400x200?text=No+Image';

                // 2. เช็คสถานะต่างๆ
                $reg_status = getUserRegistrationStatus($_SESSION['user_id'], $event['event_id']);
                $is_ended = ($current_time > $event['end_date']);
                $is_full = (!empty($event['max_participants']) && $event['current_participants'] >= $event['max_participants']);

                // 3. กำหนดข้อความและสีสถานะมุมขวาล่าง
                $status_text = "เข้าร่วม";
                $status_class = "status-join";

                if ($is_ended) {
                    $status_text = "สิ้นสุดแล้ว";
                    $status_class = "status-ended";
                } elseif ($reg_status == 'approved') {
                    $status_text = "เข้าร่วมแล้ว";
                    $status_class = "status-joined";
                } elseif ($reg_status == 'pending') {
                    $status_text = "รออนุมัติ";
                    $status_class = "status-pending";
                } elseif ($reg_status == 'rejected') {
                    $status_text = "ปฏิเสธ/ส่งใหม่";
                    $status_class = "status-ended";
                } elseif ($is_full) {
                    $status_text = "เต็มแล้ว";
                    $status_class = "status-full";
                }
                ?>

                <a href="/templates/event_details.php?id=<?php echo $event['event_id']; ?>" class="event-card">
                    
                    <img src="<?php echo htmlspecialchars($cover_image); ?>" alt="Cover" class="event-cover">
                    
                    <div class="event-body">
                        <div class="event-meta">
                            <span>ID: <?php echo $event['event_id']; ?></span>
                            <span>⏳ <?php echo date('d M Y', strtotime($event['start_date'])); ?></span>
                        </div>
                        
                        <h3 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        
                        <p class="event-desc">
                            <?php 
                            // แสดงรายละเอียดแบบย่อ ถ้าไม่มีใส่ข้อความว่างไว้
                            echo !empty($event['description']) ? htmlspecialchars($event['description']) : 'ไม่มีคำอธิบายเพิ่มเติม...'; 
                            ?>
                        </p>
                        
                        <div class="event-footer">
                            <div class="organizer-info">
                                👤 <?php echo htmlspecialchars($event['organizer_name']); ?>
                            </div>
                            <div class="status-text <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </div>
                        </div>
                    </div>
                </a>

            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #888; background: #fff; border-radius: 12px;">
                ไม่มีกิจกรรมในระบบที่ค้นหา
            </div>
        <?php endif; ?>
    </div>

</body>
</html>