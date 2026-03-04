<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Registrations.php';

// รับค่า action ทั้งแบบ POST (จากฟอร์ม) และ GET (จากการกดลิงก์)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

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

    if ($registration_id > 0 && in_array($status, ['approved', 'rejected', 'pending'])) {
        
        if (updateRegistrationStatus($registration_id, $status)) {
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

// --- 3. ส่วนผู้จัดงานตรวจสอบ OTP จากผู้เข้าร่วม ---
elseif ($action == 'verify_otp_frontdesk') {
    $event_id = $_POST['event_id'] ?? 0;
    $input_otp = trim($_POST['otp_input'] ?? '');
    
    $registrations = getRegistrationsByEvent($event_id);
    $matched_user_name = '';
    $matched_reg_id = 0;

    foreach ($registrations as $reg) {
        if (strtolower($reg['status']) == 'approved' && $reg['is_checked_in'] == 0) {
            // ใช้ฟังก์ชันแบบที่คุณมีในไฟล์อยู่แล้ว
            if (verifyDynamicOTP($reg['user_id'], $event_id, $input_otp)) {
                $matched_reg_id = $reg['registration_id'];
                $matched_user_name = $reg['name'];
                break;
            }
        }
    }

    if ($matched_reg_id > 0) {
        updateCheckInStatus($matched_reg_id, 1);
        echo "<script>alert('✅ เช็คอินสำเร็จ! รหัสนี้เป็นของคุณ: $matched_user_name'); window.location.href='/templates/event_checkin.php?event_id=$event_id';</script>";
    } else {
        echo "<script>alert('❌ รหัส OTP ไม่ถูกต้อง / หมดอายุแล้ว หรือผู้ใช้นี้เช็คอินไปแล้ว'); window.history.back();</script>";
    }
    exit();
}

// --- 4. สำหรับปุ่ม "แมนนวลเช็คชื่อ" (คลิกจากลิงก์) ---
elseif ($action == 'checkin') {
    $registration_id = $_GET['id'] ?? 0;
    $event_id = $_GET['event_id'] ?? 0;
    
    if ($registration_id > 0) {
        updateCheckInStatus($registration_id, 1);
    }
    
    echo "<script>window.location.href='/templates/event_checkin.php?event_id=$event_id';</script>";
    exit();
}

// --- 5. สำหรับปุ่ม "ยกเลิก" (กรณีกดเช็คชื่อผิดคน) ---
elseif ($action == 'undo_checkin') {
    $registration_id = $_GET['id'] ?? 0;
    $event_id = $_GET['event_id'] ?? 0;
    
    if ($registration_id > 0) {
        updateCheckInStatus($registration_id, 0);
    }
    
    echo "<script>window.location.href='/templates/event_checkin.php?event_id=$event_id';</script>";
    exit();
}
?>