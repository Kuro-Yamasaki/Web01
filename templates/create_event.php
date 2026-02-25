
<form action="/routes/event.php" method="POST" enctype="multipart/form-data">
    
    <input type="hidden" name="action" value="create">
    
    <input type="hidden" name="organizer_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>"> 
    
    <label>ชื่อกิจกรรม:</label>
    <input type="text" name="event_name" required><br>
    
    <label>รายละเอียด:</label>
    <textarea name="description"></textarea><br>
    
    <label>วันที่เริ่ม:</label>
    <input type="datetime-local" name="start_date" required><br>
    
    <label>วันที่สิ้นสุด:</label>
    <input type="datetime-local" name="end_date" required><br>
    
    <label>จำนวนที่รับ:</label>
    <input type="number" name="max_participants"><br>
    
    <label>สถานที่:</label>
    <input type="text" name="location"><br>
    
    <label>รูปภาพกิจกรรม (เลือกได้หลายรูป):</label>
    <input type="file" name="event_images[]" accept="image/*" multiple required><br><br>
    
    <button type="submit">บันทึกกิจกรรม</button>

</form>