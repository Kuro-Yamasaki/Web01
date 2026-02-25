<?php
session_start();

require_once '../Include/database.php';
require_once '../databases/Events.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /templates/sign_in.php");
    exit();
}

// รับค่าจากฟอร์มค้นหา
$search_name = $_GET['search_name'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// เรียกใช้ฟังก์ชันค้นหา
$events = searchEventsForHome($_SESSION['user_id'], $search_name, $start_date, $end_date);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการกิจกรรมสุดสนุก</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* กำหนดชุดสีจากภาพของคุณ */
        :root {
            --color-navy: #2B325C;       /* สีน้ำเงินเข้ม */
            --color-sky: #BBD4E3;        /* สีฟ้าอ่อน */
            --color-cream: #F8F9E3;      /* สีครีม */
            --color-watermelon: #E6485E; /* สีแดงแตงโม */
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--color-cream);
            color: var(--color-navy);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2.page-title {
            text-align: center;
            font-size: 2.5em;
            font-weight: 600;
            color: var(--color-navy);
            margin-bottom: 30px;
            text-shadow: 2px 2px 0px var(--color-sky); /* เพิ่มลูกเล่นเงาสีฟ้า */
        }

        /* ตกแต่งฟอร์มค้นหา */
        .search-container {
            background-color: #ffffff;
            padding: 25px;
            border: 3px solid var(--color-sky);
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 8px 15px rgba(43, 50, 92, 0.05);
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 200px;
        }

        .input-group label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--color-navy);
        }

        .search-container input {
            padding: 10px 15px;
            border: 2px solid var(--color-sky);
            border-radius: 8px;
            font-family: 'Kanit', sans-serif;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-container input:focus {
            border-color: var(--color-navy);
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .btn-search {
            background-color: var(--color-navy);
            color: var(--color-cream);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-family: 'Kanit', sans-serif;
            font-size: 1em;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(43, 50, 92, 0.2);
        }

        .btn-clear {
            background-color: var(--color-sky);
            color: var(--color-navy);
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: filter 0.2s;
        }

        .btn-clear:hover {
            filter: brightness(0.9);
        }

        /* Grid การ์ดกิจกรรม */
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .event-card {
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-bottom: 5px solid var(--color-watermelon);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* เด้งๆ เวลานำเมาส์ไปชี้ */
            display: flex;
            flex-direction: column;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(230, 72, 94, 0.15);
        }

        .event-header {
            background-color: var(--color-navy);
            color: var(--color-cream);
            padding: 20px;
            position: relative;
        }

        .event-header h3 {
            margin: 0;
            font-size: 1.4em;
            line-height: 1.3;
        }

        .event-body {
            padding: 20px;
            flex-grow: 1;
        }

        .event-info {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95em;
        }

        .icon {
            color: var(--color-watermelon);
            font-weight: bold;
        }

        .event-footer {
            padding: 15px 20px 20px;
            background-color: #fcfcfc;
        }

        .btn-join {
            width: 100%;
            background-color: var(--color-watermelon);
            color: #ffffff;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Kanit', sans-serif;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-join:hover {
            background-color: #d13a50;
            transform: scale(1.02);
        }

        .empty-state {
            text-align: center;
            grid-column: 1 / -1;
            padding: 50px 20px;
            background-color: #ffffff;
            border-radius: 15px;
            border: 2px dashed var(--color-sky);
        }

        .empty-state h3 {
            color: var(--color-watermelon);
        }

    </style>
</head>

<body>

    <?php include 'header.php' ?>
    
    <div class="container">
        <h2 class="page-title">✨ ลุยกันเลย! หากิจกรรมที่ใช่สำหรับคุณ</h2>

        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <div class="input-group">
                    <label>ค้นหาชื่อกิจกรรม</label>
                    <input type="text" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="เช่น แคมป์ปิ้ง, เวิร์คช็อป...">
                </div>
                
                <div class="input-group">
                    <label>ตั้งแต่วันที่</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                
                <div class="input-group">
                    <label>ถึงวันที่</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-search">🔍 ค้นหา</button>
                    <a href="/templates/home.php" class="btn-clear">ล้างค่า</a>
                </div>
            </form>
        </div>

        <div class="event-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                        </div>
                        <div class="event-body">
                            <div class="event-info">
                                <span class="icon">👤</span> 
                                <span><strong>ผู้จัด:</strong> <?php echo htmlspecialchars($event['organizer_name']); ?></span>
                            </div>
                            <div class="event-info">
                                <span class="icon">📅</span> 
                                <span><strong>เวลา:</strong> <?php echo date('d/m/Y H:i', strtotime($event['start_date'])); ?></span>
                            </div>
                            <div class="event-info">
                                <span class="icon">📍</span> 
                                <span><strong>สถานที่:</strong> <?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <div class="event-info">
                                <span class="icon">👥</span> 
                                <span><strong>รับจำนวน:</strong> <?php echo $event['max_participants']; ?> คน</span>
                            </div>
                        </div>
                        <div class="event-footer">
                            <form action="/routes/Registration.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="request_join">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" class="btn-join" onclick="return confirm('พร้อมลุยกิจกรรมนี้แล้วใช่มั้ย? ยืนยันเลย!');">
                                    🚀 ขอเข้าร่วมกิจกรรม
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>อ๊ะ! ยังไม่มีกิจกรรมในตอนนี้ 😢</h3>
                    <p>ลองปรับการค้นหาใหม่ หรือแวะมาดูใหม่รอบหน้านะครับ</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>