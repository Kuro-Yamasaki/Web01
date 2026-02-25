<?php
function getUser():mysqli_result|bool
{
    global $conn;
    $sql = 'select * from users';
    $result = $conn->query($sql);
    return $result;
}

function generateUniqueUID() {
    global $conn;
    $is_unique = false;
    $new_uid = 0;

    while (!$is_unique) {
        // สุ่มตัวเลข 6 หลัก (เปลี่ยนช่วงตัวเลขได้ตามต้องการ เช่น 100000 - 999999)
        $new_uid = mt_rand(100000, 999999); 
        
        // เช็คในฐานข้อมูลว่ามี UID นี้หรือยัง
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE user_id = ?");
        $stmt->bind_param("i", $new_uid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // ถ้าไม่เจอข้อมูลแสดงว่าไม่ซ้ำ ให้ออกจากลูป
        if ($result->num_rows === 0) {
            $is_unique = true;
        }
        $stmt->close();
    }
    
    return $new_uid;
}

// 2. ปรับปรุงฟังก์ชัน createUser ให้เพิ่ม UID เข้าไปด้วย
function createUser($data) {
    global $conn;
    if (!isset($data['name'])) {
        die("Error: Name data is missing.");
    }

    // เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // สุ่ม UID ใหม่ที่ไม่ซ้ำ
    $user_id = generateUniqueUID();

    // เพิ่ม user_id เข้าไปในคำสั่ง INSERT
    $sql = "INSERT INTO Users (user_id, name, gender, birthdate, province, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // เปลี่ยน bind_param เป็น "issssss" (i = integer สำหรับ user_id, s = string สำหรับตัวอื่นๆ)
    $stmt->bind_param("issssss", 
        $user_id,
        $data['name'], 
        $data['gender'], 
        $data['birthdate'], 
        $data['province'], 
        $data['email'], 
        $hashed_password
    );

    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function getUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
   
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function registerUser($name, $email, $password, $gender, $birth_date, $province) {
    global $conn;
    
   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
 
    $sql = "INSERT INTO users (name, email, password, gender, birthdate, province) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    
    if (!$stmt) {
        die("เกิดข้อผิดพลาดกับคำสั่ง SQL: " . $conn->error);
    }
    
    $stmt->bind_param("ssssss", $name, $email, $hashed_password, $gender, $birth_date, $province);
    
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
    
?>