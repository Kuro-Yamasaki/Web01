<?php
session_start(); 
require_once '../Include/database.php';
require_once '../databases/Events.php';

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
?>