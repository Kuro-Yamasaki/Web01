
<?php

// ฟังก์ชันดึงรายชื่อคนที่ลงทะเบียนในกิจกรรมนั้นๆ 
function getRegistrationsByEvent($event_id)
{
    global $conn;
    $sql = "SELECT r.*, u.name, u.gender, u.province, u.email 
            FROM Registrations r 
            JOIN Users u ON r.user_id = u.user_id 
            WHERE r.event_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $registrations = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
    }
    return $registrations;
}

// ฟังก์ชันอัปเดตสถานะ 
function updateRegistrationStatus($registration_id, $status)
{
    global $conn;
    $sql = "UPDATE Registrations SET status = ? WHERE registration_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $registration_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
// ฟังก์ชันสำหรับอัปเดตสถานะการเช็คชื่อ 
function updateCheckInStatus($registration_id, $is_checked_in)
{
    global $conn;
    $sql = "UPDATE Registrations SET is_checked_in = ? WHERE registration_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $is_checked_in, $registration_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function createRegistration($user_id, $event_id)
{
    global $conn;

    // 1. ตรวจสอบก่อนว่าเคยลงทะเบียนกิจกรรมนี้ไปแล้วหรือยัง
    $check_sql = "SELECT registration_id FROM Registrations WHERE user_id = ? AND event_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $event_id);
    $check_stmt->execute();

    if ($check_stmt->get_result()->num_rows > 0) {
        $check_stmt->close();
        return false; 
    }
    $check_stmt->close();

    // 2. ถ้ายังไม่เคย ให้บันทึกข้อมูลใหม่ โดยกำหนดสถานะเริ่มต้นเป็น pending รออนุมัติ
    $sql = "INSERT INTO Registrations (user_id, event_id, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $event_id);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

function getUserHistory($user_id) {
    global $conn;
    // เชื่อมตาราง Registrations กับ Events เพื่อเอาชื่อกิจกรรมมาโชว์
    $sql = "SELECT r.*, e.event_name, e.start_date, e.location 
            FROM Registrations r 
            JOIN Events e ON r.event_id = e.event_id 
            WHERE r.user_id = ? 
            ORDER BY r.registration_id DESC"; // เรียงจากล่าสุดไปเก่าสุด
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
    }
    return $history;
}

?>