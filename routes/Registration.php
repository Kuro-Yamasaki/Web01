<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Registrations.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // --- 1. ส่วนขอเข้าร่วมกิจกรรม (ผู้เข้าร่วมกด) ---
    if ($action == 'request_join') {
        
        if (empty($_SESSION['user_id'])) {
            echo "<script>alert('กรุณาเข้าสู่ระบบก่อนลงทะเบียนเข้าร่วมกิจกรรม!'); window.location.href='/templates/sign_in.php';</script>";
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $event_id = $_POST['event_id'] ?? 0;

        if ($event_id > 0) {
            if (createRegistration($user_id, $event_id)) {
                echo "<script>alert('ส่งคำขอเข้าร่วมกิจกรรมแล้ว! กรุณารอผู้จัดงานอนุมัติ'); window.location.href='/templates/home.php';</script>";
            } else {
                echo "<script>alert('คุณได้ขอเข้าร่วมกิจกรรมนี้ไปแล้ว หรือเกิดข้อผิดพลาด'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('ไม่พบข้อมูลกิจกรรม'); window.history.back();</script>";
        }
        exit();
    }
    
    // --- 2. ส่วนจัดการการอนุมัติ/ปฏิเสธ (ผู้จัดงานกด) --- 
    elseif ($action == 'update_status') {
        
        $registration_id = $_POST['registration_id'] ?? 0;
        $status = $_POST['status'] ?? ''; 
        $event_id = $_POST['event_id'] ?? 0; 

        // จุดที่แก้ไข: เพิ่ม 'pending' เข้าไปใน in_array ตรงนี้ครับ
        if ($registration_id > 0 && in_array($status, ['approved', 'rejected', 'pending'])) {
            
            // เรียกใช้ฟังก์ชันอัปเดตสถานะในฐานข้อมูล
            if (updateRegistrationStatus($registration_id, $status)) {
                
                // กำหนดข้อความ Alert ตามสถานะที่ถูกส่งมา
                if ($status == 'approved') {
                    $msg = 'อนุมัติผู้เข้าร่วมแล้ว';
                } elseif ($status == 'rejected') {
                    $msg = 'ปฏิเสธผู้เข้าร่วมแล้ว';
                } else {
                    $msg = 'ยกเลิกสถานะ กลับไปเป็นรอดำเนินการเรียบร้อยแล้ว';
                }

                echo "<script>
                        alert('$msg'); 
                        window.location.href='/templates/event_registrations.php?event_id=$event_id';
                      </script>";
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสถานะฐานข้อมูล'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('ข้อมูลที่ส่งมาไม่ครบถ้วน! กรุณาตรวจสอบฟอร์ม HTML'); window.history.back();</script>";
        }
        exit();
    }
}
?>