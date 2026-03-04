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
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- 1. สร้างกิจกรรมใหม่ ---
if ($action == 'create') {
    $organizer_id = $_SESSION['user_id'];
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];

    $stmt = $conn->prepare("INSERT INTO Events (organizer_id, event_name, description, start_date, end_date, location, max_participants) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $organizer_id, $event_name, $description, $start_date, $end_date, $location, $max_participants);
    
    if ($stmt->execute()) {
        $new_event_id = $stmt->insert_id; // ดึง ID ของกิจกรรมที่เพิ่งสร้าง

        // --- ระบบอัปโหลดรูปภาพ ---
        if (!empty($_FILES['event_images']['name'][0])) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            foreach ($_FILES['event_images']['tmp_name'] as $key => $tmp_name) {
                $file_name = time() . '_' . basename($_FILES['event_images']['name'][$key]);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $db_image_path = '/uploads/' . $file_name;
                    $img_stmt = $conn->prepare("INSERT INTO Event_Images (event_id, image_path) VALUES (?, ?)");
                    $img_stmt->bind_param("is", $new_event_id, $db_image_path);
                    $img_stmt->execute();
                }
            }
        }
        echo "<script>alert('สร้างกิจกรรมเรียบร้อยแล้ว'); window.location.href='../templates/manage_event.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด'); window.history.back();</script>";
    }
    exit();
}

// --- 2. อัปเดต/แก้ไขกิจกรรม ---
elseif ($action == 'update') {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];

    $stmt = $conn->prepare("UPDATE Events SET event_name=?, description=?, start_date=?, end_date=?, location=?, max_participants=? WHERE event_id=?");
    $stmt->bind_param("sssssii", $event_name, $description, $start_date, $end_date, $location, $max_participants, $event_id);
    
    if ($stmt->execute()) {
        // --- ระบบอัปโหลดรูปภาพเพิ่มเติม ---
        if (!empty($_FILES['event_images']['name'][0])) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            foreach ($_FILES['event_images']['tmp_name'] as $key => $tmp_name) {
                $file_name = time() . '_' . basename($_FILES['event_images']['name'][$key]);
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $db_image_path = '/uploads/' . $file_name;
                    $img_stmt = $conn->prepare("INSERT INTO Event_Images (event_id, image_path) VALUES (?, ?)");
                    $img_stmt->bind_param("is", $event_id, $db_image_path);
                    $img_stmt->execute();
                }
            }
        }
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='../templates/manage_event.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด'); window.history.back();</script>";
    }
    exit();
}

// --- 3. ลบรูปภาพออกจากกิจกรรม (เฉพาะรูปเดียว) ---
elseif ($action == 'delete_image') {
    $image_id = $_GET['image_id'];
    $event_id = $_GET['event_id'];

    $img_stmt = $conn->prepare("SELECT image_path FROM Event_Images WHERE image_id = ?");
    $img_stmt->bind_param("i", $image_id);
    $img_stmt->execute();
    $img_res = $img_stmt->get_result()->fetch_assoc();

    if ($img_res) {
        $file_to_delete = '..' . $img_res['image_path'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
        $del_stmt = $conn->prepare("DELETE FROM Event_Images WHERE image_id = ?");
        $del_stmt->bind_param("i", $image_id);
        $del_stmt->execute();
    }
    echo "<script>window.location.href='/templates/edit_event.php?id=$event_id';</script>";
    exit();
}

// --- 4. ลบกิจกรรมทั้งหมด ---
elseif ($action == 'delete') {
    $event_id = $_GET['id'];
    
    // ลบไฟล์รูปภาพจริงในโฟลเดอร์ก่อนลบกิจกรรม
    $img_stmt = $conn->prepare("SELECT image_path FROM Event_Images WHERE event_id = ?");
    $img_stmt->bind_param("i", $event_id);
    $img_stmt->execute();
    $images = $img_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($images as $img) {
        $file_to_delete = '..' . $img['image_path'];
        if (file_exists($file_to_delete)) unlink($file_to_delete);
    }

    $stmt = $conn->prepare("DELETE FROM Events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    if ($stmt->execute()) {
        echo "<script>alert('ลบกิจกรรมสำเร็จ'); window.location.href='../templates/manage_event.php';</script>";
    } else {
        echo "<script>alert('ไม่สามารถลบกิจกรรมได้'); window.history.back();</script>";
    }
    exit();
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
