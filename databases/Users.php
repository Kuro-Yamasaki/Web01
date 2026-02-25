<?php
function getUser():mysqli_result|bool
{
    global $conn;
    $sql = 'select * from users';
    $result = $conn->query($sql);
    return $result;
}

function createUser($data) {
    global $conn;
    if (!isset($data['name'])) {
        die("Error: Name data is missing.");
    }

   
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Users (name, gender, birthdate, province, email, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("ssssss", 
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