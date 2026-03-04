<?php
session_start();
require_once '../Include/database.php';
require_once '../databases/Events.php';

$event_id = $_GET['id'] ?? 0;
$event = getEventById($event_id);

if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
    die("คุณไม่มีสิทธิ์แก้ไขกิจกรรมนี้");
}

// ดึงรูปภาพที่มีอยู่แล้วของกิจกรรมนี้
$img_stmt = $conn->prepare("SELECT * FROM Event_Images WHERE event_id = ?");
$img_stmt->bind_param("i", $event_id);
$img_stmt->execute();
$existing_images = $img_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขกิจกรรม</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; margin: 0; padding: 40px; }
        .form-card { max-width: 600px; margin: auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { color: #2c3e50; margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input[type="text"], input[type="datetime-local"], input[type="number"], textarea {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 15px; transition: 0.3s;
        }
        input:focus, textarea:focus { border-color: #007bff; outline: none; box-shadow: 0 0 8px rgba(0,123,255,0.1); }
        .btn-group { display: flex; gap: 10px; margin-top: 30px; }
        .btn-save { flex: 2; background: #28a745; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-cancel { flex: 1; background: #6c757d; color: white; text-decoration: none; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; }
        .btn-save:hover { background: #218838; }
        .btn-cancel:hover { background: #5a6268; }
    </style>
</head>
<body>

    <div class="form-card">
        <h2>✏️ แก้ไขข้อมูลกิจกรรม</h2>
        
        <form action="/routes/Event.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">

            <div class="form-group">
                <label>ชื่อกิจกรรม</label>
                <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>วันที่เริ่ม</label>
                    <input type="datetime-local" name="start_date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_date'])); ?>" required>
                </div>
                <div class="form-group">
                    <label>วันที่สิ้นสุด</label>
                    <input type="datetime-local" name="end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['end_date'])); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>สถานที่</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>

            <div class="form-group">
                <label>จำนวนผู้เข้าร่วมสูงสุด (คน)</label>
                <input type="number" name="max_participants" value="<?php echo $event['max_participants']; ?>" required>
            </div>

            <div class="form-group" style="padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px dashed #ccc;">
                <label>📸 จัดการรูปภาพกิจกรรม</label>
                
                <?php if (!empty($existing_images)): ?>
                    <p style="font-size: 13px; color: #888; margin-bottom: 5px;">ภาพเดิม (กดกากบาทเพื่อลบ):</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
                        <?php foreach ($existing_images as $img): ?>
                            <div style="position: relative;">
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                <a href="/routes/Event.php?action=delete_image&image_id=<?php echo $img['image_id']; ?>&event_id=<?php echo $event['event_id']; ?>" 
                                   onclick="return confirm('ต้องการลบภาพนี้ออกจากระบบใช่หรือไม่?');" 
                                   style="position: absolute; top: -8px; right: -8px; background: #e74c3c; color: white; text-decoration: none; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-weight: bold;">X</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <p style="font-size: 13px; color: #888; margin-bottom: 5px;">เพิ่มภาพใหม่:</p>
                <button type="button" onclick="document.getElementById('imageInput').click()" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">+ เพิ่มรูปภาพใหม่</button>
                <input type="file" id="imageInput" name="event_images[]" multiple accept="image/*" style="display: none;">
                
                <div id="previewContainer" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;"></div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-save">💾 บันทึกการเปลี่ยนแปลง</button>
                <a href="manage_event.php" class="btn-cancel">ยกเลิก</a>
            </div>
        </form>
    </div>

    <script>
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        let selectedFiles = [];

        imageInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    selectedFiles.push(file);
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const div = document.createElement('div');
                        div.style.position = 'relative';
                        div.innerHTML = `
                            <img src="${event.target.result}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                            <button type="button" onclick="removeFile('${file.name}', this)" style="position: absolute; top: -8px; right: -8px; background: #e74c3c; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-weight: bold;">X</button>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
            updateFileInput();
        });

        function removeFile(fileName, btnElement) {
            selectedFiles = selectedFiles.filter(f => f.name !== fileName);
            btnElement.parentElement.remove();
            updateFileInput();
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(f => dataTransfer.items.add(f));
            imageInput.files = dataTransfer.files;
        }
    </script>
</body>
</html>