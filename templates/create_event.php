<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สร้างกิจกรรมใหม่</title>
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
        .btn-save { flex: 2; background: #007bff; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-cancel { flex: 1; background: #6c757d; color: white; text-decoration: none; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; }
        .btn-save:hover { background: #0056b3; }
        .btn-cancel:hover { background: #5a6268; }
    </style>
</head>
<body>

    <div class="form-card">
        <h2>✨ สร้างกิจกรรมใหม่</h2>
        
        <form action="/routes/Event.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">

            <div class="form-group">
                <label>ชื่อกิจกรรม</label>
                <input type="text" name="event_name" required>
            </div>

            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" rows="4"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>วันที่เริ่ม</label>
                    <input type="datetime-local" name="start_date" required>
                </div>
                <div class="form-group">
                    <label>วันที่สิ้นสุด</label>
                    <input type="datetime-local" name="end_date" required>
                </div>
            </div>

            <div class="form-group">
                <label>สถานที่</label>
                <input type="text" name="location" required>
            </div>

            <div class="form-group">
                <label>จำนวนผู้เข้าร่วมสูงสุด (คน)</label>
                <input type="number" name="max_participants" required>
            </div>

            <div class="form-group" style="padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px dashed #ccc;">
                <label>📸 รูปภาพประกอบกิจกรรม (เลือกเพิ่มทีละรูป หรือหลายรูปพร้อมกันได้)</label>
                <button type="button" onclick="document.getElementById('imageInput').click()" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-top: 5px;">+ เลือกรูปภาพ</button>
                <input type="file" id="imageInput" name="event_images[]" multiple accept="image/*" style="display: none;">
                
                <div id="previewContainer" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;"></div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-save">สร้างกิจกรรม</button>
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