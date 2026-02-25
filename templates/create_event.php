<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างกิจกรรมใหม่</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #333;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .top-section {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .image-col {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* --- ส่วนจัดการกล่อง Preview ภาพ --- */
        .image-preview-box {
            width: 100%;
            height: 250px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fafafa;
            color: #888;
            font-size: 18px;
            font-weight: bold;
            overflow: hidden;
            position: relative;
        }

        .image-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }

        /* ปุ่มลูกศร ซ้าย-ขวา */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            font-size: 18px;
            border-radius: 5px;
            display: none; /* ซ่อนไว้ก่อน จะโชว์เมื่อมี > 1 ภาพ */
            z-index: 10;
            transition: background 0.2s;
        }

        .nav-btn:hover { background: rgba(0, 0, 0, 0.8); }
        .prev-btn { left: 5px; }
        .next-btn { right: 5px; }

        /* ตัวนับภาพ (เช่น 1/3) */
        .image-counter {
            position: absolute;
            bottom: 5px;
            right: 10px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 12px;
            display: none;
            z-index: 10;
        }

        .info-col {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            height: 100%;
            min-height: 175px;
        }

        .middle-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .date-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #fcfcfc;
        }

        .bottom-actions {
            display: flex;
            justify-content: flex-end; /* เปลี่ยนให้อยู่ตรงกลาง */
            gap: 20px; /* ระยะห่างระหว่าง 2 ปุ่ม */
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .btn-create {
            background-color: #2ecc71;
            color: white;
            padding: 12px 30px;
            border: 2px solid #27ae60;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-create:hover { background-color: #27ae60; }

        .btn-cancel {
            background-color: #ecf0f1;
            color: #333;
            padding: 12px 30px;
            border: 2px solid #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-cancel:hover { background-color: #bdc3c7; }

        input[type="file"]::file-selector-button {
            border: 1px solid #3498db;
            padding: 8px 15px;
            border-radius: 4px;
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: 0.2s;
        }
        input[type="file"]::file-selector-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <h2>📝 สร้างกิจกรรมใหม่</h2>

        <form action="/routes/event.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="organizer_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>"> 
            
            <div class="top-section">
                <div class="image-col">
                    <div class="image-preview-box" id="previewBox">
                        <span id="previewText">ภาพ</span>
                        <img id="imagePreview" src="" alt="Preview">
                        
                        <button type="button" class="nav-btn prev-btn" id="prevBtn" onclick="changeImage(-1)">&#10094;</button>
                        <button type="button" class="nav-btn next-btn" id="nextBtn" onclick="changeImage(1)">&#10095;</button>
                        <span class="image-counter" id="imageCounter">1/1</span>
                    </div>
                    <input type="file" name="event_images[]" id="fileInput" accept="image/*" multiple required>
                </div>

                <div class="info-col">
                    <div class="form-group">
                        <label>ชื่อกิจกรรม</label>
                        <input type="text" name="event_name" placeholder="ระบุชื่อกิจกรรมของคุณ..." required>
                    </div>
                    <div class="form-group" style="flex-grow: 1;">
                        <label>รายละเอียด</label>
                        <textarea name="description" placeholder="เพิ่มรายละเอียดที่น่าสนใจของกิจกรรมนี้..."></textarea>
                    </div>
                </div>
            </div>

            <div class="middle-row">
                <div class="form-group">
                    <label>สถานที่</label>
                    <input type="text" name="location" placeholder="เช่น ห้องประชุม 1, หอประชุมใหญ่...">
                </div>
                <div class="form-group">
                    <label>จำนวนที่รับ (คน)</label>
                    <input type="number" name="max_participants" placeholder="ใส่ตัวเลข">
                </div>
            </div>

            <div class="date-row">
                <div class="form-group">
                    <label>วันเริ่มงาน</label>
                    <input type="datetime-local" name="start_date" required>
                </div>
                <div class="form-group">
                    <label>วันจบงาน</label>
                    <input type="datetime-local" name="end_date" required>
                </div>
            </div>
            
            <div class="bottom-actions">
                <button type="submit" class="btn-create">สร้าง</button>
                <a href="/templates/home.php" class="btn-cancel">ยกเลิก</a>
            </div>

        </form>
    </div>

    <script>
        let uploadedImages = [];
        let currentIndex = 0;

        document.getElementById('fileInput').addEventListener('change', function(event) {
            const files = event.target.files;
            uploadedImages = [];
            currentIndex = 0;

            const previewImg = document.getElementById('imagePreview');
            const previewText = document.getElementById('previewText');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const counter = document.getElementById('imageCounter');

            if (files.length > 0) {
                previewText.style.display = 'none';
                previewImg.style.display = 'block';
                
                // อ่านไฟล์ทั้งหมดเก็บลง Array
                let loaded = 0;
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        uploadedImages[i] = e.target.result; // เก็บเรียงตามลำดับไฟล์
                        loaded++;
                        if (loaded === files.length) {
                            updatePreviewDisplay();
                        }
                    };
                    reader.readAsDataURL(files[i]);
                }
            } else {
                // กรณีผู้ใช้กดยกเลิกการเลือกไฟล์
                previewImg.src = "";
                previewImg.style.display = 'none';
                previewText.style.display = 'block';
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                counter.style.display = 'none';
            }
        });

        // ฟังก์ชันเปลี่ยนภาพ
        function changeImage(step) {
            currentIndex += step;
            // วนลูปภาพ ถ้าเลยภาพสุดท้ายให้กลับไปภาพแรก
            if (currentIndex < 0) currentIndex = uploadedImages.length - 1;
            if (currentIndex >= uploadedImages.length) currentIndex = 0;
            
            updatePreviewDisplay();
        }

        // ฟังก์ชันอัปเดตหน้าจอ
        function updatePreviewDisplay() {
            const previewImg = document.getElementById('imagePreview');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const counter = document.getElementById('imageCounter');

            if (uploadedImages.length > 0) {
                previewImg.src = uploadedImages[currentIndex];
                
                // โชว์ปุ่มเลื่อนและตัวนับ เฉพาะเมื่อมีรูปมากกว่า 1 รูป
                if (uploadedImages.length > 1) {
                    prevBtn.style.display = 'block';
                    nextBtn.style.display = 'block';
                    counter.style.display = 'block';
                    counter.innerText = (currentIndex + 1) + " / " + uploadedImages.length;
                } else {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    counter.style.display = 'none';
                }
            }
        }
    </script>

</body>
</html>
