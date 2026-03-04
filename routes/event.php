<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Events.php';
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// --- จัดการคำสั่งแบบ GET (สำหรับปุ่ม "ลบ") ---
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? 0;

    if ($action == 'delete' && $id > 0) {
        if (deleteEvent($id)) {
            echo "<script>alert('ลบกิจกรรมเรียบร้อยแล้ว'); window.location.href='/templates/home.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการลบ'); window.history.back();</script>";
        }
    }
}

// --- จัดการคำสั่งแบบ POST (สำหรับ "อัปเดต" และ "สร้างใหม่") ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. เช็คการเข้าสู่ระบบก่อน! ถ้ายังไม่ล็อกอินห้ามทำรายการ
    if (empty($_SESSION['user_id'])) {
        echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำรายการ!'); window.location.href='/templates/sign_in.php';</script>";
        exit();
    }

    // 2. เตรียมข้อมูลพื้นฐาน
    $data = [
        'organizer_id'     => $_SESSION['user_id'],
        'event_name'       => $_POST['event_name'] ?? '',
        'description'      => $_POST['description'] ?? '',
        'start_date'       => $_POST['start_date'] ?? '',
        'end_date'         => $_POST['end_date'] ?? '',
        'max_participants' => !empty($_POST['max_participants']) ? $_POST['max_participants'] : null,
        'location'         => $_POST['location'] ?? ''
    ];

    // --- แก้ไขกิจกรรม (Update) ---
    if ($action == 'update') {
        $id = $_POST['event_id'];
        if (updateEvent($id, $data)) {
            echo "<script>alert('แก้ไขข้อมูลสำเร็จ!'); window.location.href='/templates/home.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการแก้ไข'); window.history.back();</script>";
        }
        exit();
    }

    // --- สร้างกิจกรรมใหม่ (Create) ---
    elseif ($action == 'create') {

        // 1. บันทึกข้อมูลกิจกรรมหลักก่อน เพื่อให้ได้ ID ของกิจกรรมที่เพิ่งสร้าง
        $new_event_id = createEvent($data);

        // ถ้าสร้างกิจกรรมหลักสำเร็จ และได้ ID กลับมา (ป้องกันค่าว่างหรือ false)
        if ($new_event_id) {

            // 2. จัดการอัปโหลดรูปภาพหลายไฟล์ (Multiple Uploads)
            $upload_dir = __DIR__ . '/../uploads/';

            // สร้างโฟลเดอร์ uploads อัตโนมัติถ้ายังไม่มี
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // เช็คว่ามีการแนบไฟล์จากฟอร์มมาหรือไม่ (ต้องใช้ช่อง input name="event_images[]")
            if (isset($_FILES['event_images']) && !empty($_FILES['event_images']['name'][0])) {

                $fileCount = count($_FILES['event_images']['name']);

                // วนลูปอัปโหลดทีละไฟล์
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['event_images']['error'][$i] === UPLOAD_ERR_OK) {

                        // ตั้งชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำ
                        $file_name = time() . '_' . uniqid() . '_' . basename($_FILES['event_images']['name'][$i]);
                        $target_file = $upload_dir . $file_name;

                        // สั่งย้ายไฟล์จากไฟล์ชั่วคราว ไปลงโฟลเดอร์
                        if (move_uploaded_file($_FILES['event_images']['tmp_name'][$i], $target_file)) {
                            // บันทึกที่อยู่ไฟล์รูปภาพลงฐานข้อมูล
                            $image_path = '/uploads/' . $file_name;
                            addEventImage($new_event_id, $image_path);
                        }
                    }
                }
            }

            echo "<script>
                    alert('บันทึกกิจกรรมและอัปโหลดรูปภาพเรียบร้อยแล้ว!'); 
                    window.location.href='/templates/home.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('เกิดข้อผิดพลาดในการบันทึกข้อมูลกิจกรรมลงฐานข้อมูล'); 
                    window.history.back();
                  </script>";
            exit();
        }
    }
}

if ($_POST['action'] == 'start_event_send_otp') {
    $event_id = $_POST['event_id'];

    // 1. เช็คว่าเคยส่งไปหรือยัง ป้องกันกดเบิ้ล
    $update_evt = $conn->prepare("UPDATE Events SET is_otp_sent = 1 WHERE event_id = ?");
    $update_evt->bind_param("i", $event_id);
    $update_evt->execute();

    if ($event_data['is_otp_sent'] == 1) {
        echo "<script>alert('ส่งรหัสไปแล้ว ไม่สามารถส่งซ้ำได้!'); window.history.back();</script>";
        exit();
    }

    // 2. ดึงเฉพาะคนที่สถานะ 'approved' (อนุมัติแล้ว)
    $stmt = $conn->prepare("SELECT r.registration_id, u.email, u.name FROM Registrations r JOIN Users u ON r.user_id = u.user_id WHERE r.event_id = ? AND r.status = 'approved'");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $participants = $stmt->get_result();

    // 3. วนลูปสร้างรหัสและส่งอีเมล
    while ($user = $participants->fetch_assoc()) {
        $otp_code = rand(100000, 999999); // สุ่มเลข 6 หลัก
        $reg_id = $user['registration_id'];
        $email = $user['email'];


        $update_otp = $conn->prepare("UPDATE Registrations SET otp_code = ? WHERE registration_id = ?");
        $update_otp->bind_param("si", $otp_code, $reg_id);
        $update_otp->execute();

        // ================= โค้ดส่งอีเมลด้วย PHPMailer =================
        $mail = new PHPMailer(true);
        try {
            // ตั้งค่าเซิร์ฟเวอร์
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'webproject.ajm.noreply@gmail.com'; // 🛑 เปลี่ยนตรงนี้
            $mail->Password   = 'vhjqrfxpvswtdkal';  // 🛑 เปลี่ยนตรงนี้ (เอาช่องว่างออกด้วย)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8'; // ให้รองรับภาษาไทย

            // ตั้งค่าผู้ส่งและผู้รับ
            $mail->setFrom('webproject.ajm.noreply@gmail.com', 'ระบบจัดการกิจกรรม (My Event)'); // 🛑 เปลี่ยนตรงนี้
            $mail->addAddress($email, $name); // ส่งไปหาผู้เข้าร่วมทีละคนตามรอบ Loop

            // เนื้อหาอีเมล
            $mail->isHTML(true);
            $mail->Subject = 'รหัสเข้างาน: ' . $event_data['event_name'];
            $mail->Body    = "สวัสดีคุณ $name,<br><br>รหัสเข้างานของคุณคือ: <b><span style='font-size:20px; color:green;'>$otp_code</span></b><br><br>กรุณานำรหัสนี้ไปกรอกในเว็บ หรือแสดง QR Code ต่อเจ้าหน้าที่หน้างานครับ";

            $mail->send();
        } catch (Exception $e) {
            // ถ้าส่งไม่ผ่าน จะข้ามไปทำคนต่อไป (ไม่ให้ระบบพัง)
            error_log("ส่งอีเมลไม่สำเร็จ: {$mail->ErrorInfo}");
        }
        // =========================================================


    }

    // 4. เปลี่ยนสถานะกิจกรรมว่าส่งอีเมลแล้ว
    $conn->query("UPDATE Events SET is_otp_sent = 1 WHERE event_id = $event_id");

    echo "<script>alert('สร้างและส่งรหัสเข้างานให้ผู้เข้าร่วมทุกคนเรียบร้อยแล้ว!'); window.location.href='../templates/manage_event.php';</script>";
    exit();
}
