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
<<<<<<< HEAD

        .modal {
            display: none; /* ซ่อนไว้เป็นค่าเริ่มต้น */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5); /* พื้นหลังสีดำโปร่งแสง */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 350px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
        }

        .user-link {
            color: #3498db;
            text-decoration: underline;
            cursor: pointer;
        }
=======
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
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
<<<<<<< HEAD
=======
                <th>เพศ</th>
                <th>จังหวัด</th>
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
                <th>สถานะปัจจุบัน</th>
                <th>จัดการอนุมัติ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $reg): ?>
                    <tr>
                        <td><?php echo $reg['registration_id']; ?></td>
<<<<<<< HEAD
                        <td style="text-align: left;">
                            <a class="user-link" 
                               data-name="<?php echo htmlspecialchars($reg['name']); ?>"
                               data-email="<?php echo htmlspecialchars($reg['email']); ?>"
                               data-gender="<?php echo htmlspecialchars($reg['gender']); ?>"
                               data-province="<?php echo htmlspecialchars($reg['province']); ?>">
                               <?php echo htmlspecialchars($reg['name']); ?>
                            </a>
                        </td>
=======
                        <td style="text-align: left;"><?php echo htmlspecialchars($reg['name']); ?></td>
                        <td><?php echo htmlspecialchars($reg['gender']); ?></td>
                        <td><?php echo htmlspecialchars($reg['province']); ?></td>
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d

                        <?php
                        $status = empty($reg['status']) ? 'Pending' : $reg['status'];
                        $class_name = "text-" . strtolower($status);
                        ?>
                        <td class="<?php echo $class_name; ?>"><?php echo $status; ?></td>

                        <td>
<<<<<<< HEAD
                            <?php if (strtolower($status) == 'pending'): ?>
=======
                            <?php if ($status != 'Approved'): ?>
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
                                <form action="/routes/Registration.php" method="POST" style="display:inline-block; margin: 0;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn" style="cursor: pointer; color: #27ae60;" onclick="return confirm('ยืนยันการอนุมัติ?');">✅ อนุมัติ</button>
                                </form>
<<<<<<< HEAD

=======
                            <?php endif; ?>

                            <?php if ($status != 'Rejected'): ?>
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
                                <form action="/routes/Registration.php" method="POST" style="display:inline-block; margin: 0;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn" style="cursor: pointer; color: #e74c3c;" onclick="return confirm('ยืนยันการปฏิเสธ?');">❌ ปฏิเสธ</button>
                                </form>
<<<<<<< HEAD
                            <?php else: ?>
                                <form action="/routes/Registration.php" method="POST" style="display:inline-block; margin: 0;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="btn" style="cursor: pointer; color: #f39c12;" onclick="return confirm('ต้องการยกเลิกและนำกลับไปพิจารณาใหม่ใช่หรือไม่?');">🔄 ยกเลิก</button>
                                </form>
=======
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
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

<<<<<<< HEAD
    <div id="userInfoModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3 id="modalName" style="margin-top: 0; color: #2c3e50;">ชื่อ-นามสกุล</h3>
            <hr>
            <p>📧 <b>อีเมล:</b> <span id="modalEmail"></span></p>
            <p>🚻 <b>เพศ:</b> <span id="modalGender"></span></p>
            <p>📍 <b>จังหวัด:</b> <span id="modalProvince"></span></p>
        </div>
    </div>

    <script>
        // ดึงตัวแปรที่เกี่ยวข้องกับ Modal
        var modal = document.getElementById("userInfoModal");
        var closeBtn = document.getElementsByClassName("close-btn")[0];

        // ดึงปุ่มชื่อทุกคนมาใส่ Event Listener
        var userLinks = document.querySelectorAll(".user-link");
        
        userLinks.forEach(function(link) {
            link.addEventListener("click", function() {
                // เอาข้อมูลจาก data-* มาใส่ใน Modal
                document.getElementById("modalName").innerText = this.getAttribute("data-name");
                document.getElementById("modalEmail").innerText = this.getAttribute("data-email") || 'ไม่มีข้อมูล';
                document.getElementById("modalGender").innerText = this.getAttribute("data-gender") || 'ไม่มีข้อมูล';
                document.getElementById("modalProvince").innerText = this.getAttribute("data-province") || 'ไม่มีข้อมูล';
                
                // แสดง Modal
                modal.style.display = "block";
            });
        });

        // เมื่อกดปุ่มกากบาท (x) ให้ปิด Modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // เมื่อกดคลิกพื้นที่นอกกล่อง Modal ให้ปิด
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
=======
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
</body>

</html>