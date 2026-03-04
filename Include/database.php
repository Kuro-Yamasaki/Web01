<?php
$hostname = 'localhost';
$dbName = 'event_web';
$username = 'tester';
$password = '123abc';
$conn = new mysqli($hostname, $username, $password, $dbName);

function getConnection(): mysqli
{
    global $conn;
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

/// ฟังก์ชันสร้างรหัส OTP ตามเวลาสดๆ (ไม่ลง DB)
function getDynamicOTP($user_id, $event_id, $time_offset = 0) {
    $secret_key = "MyEventSecret2026"; 
    $time_window = floor(time() / 1800) + $time_offset; 
    $hash = md5($secret_key . $user_id . $event_id . $time_window);
    $numbers = preg_replace("/[^0-9]/", "", $hash);
    return str_pad(substr($numbers, 0, 6), 6, '0', STR_PAD_RIGHT);
}

// ฟังก์ชันตรวจสอบความถูกต้อง
function verifyDynamicOTP($user_id, $event_id, $input_otp) {
    return ($input_otp === getDynamicOTP($user_id, $event_id, 0) || 
            $input_otp === getDynamicOTP($user_id, $event_id, -1));
}