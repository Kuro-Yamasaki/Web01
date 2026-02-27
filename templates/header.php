<?php if (isset($_SESSION['user_id'])): ?>
    <div style="background-color: #aeb8c2; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">

        <div>
            <a href="/routes/User.php?action=go_home" style="text-decoration: none; font-weight: bold; color: #8e44ad;">🏠 Home</a>
        </div>

        <div>
            <b>ชื่อผู้ใช้:</b> <?php echo htmlspecialchars($_SESSION['name'] ?? 'ผู้ใช้งาน'); ?> &nbsp;|&nbsp;

            <a href="/routes/User.php?action=view_profile" style="text-decoration: none; font-weight: bold; color: #3498db;">👤 ข้อมูลบัญชี</a> &nbsp;|&nbsp;

            <a href="/routes/User.php?action=view_history" style="text-decoration: none; color: black;">📜 ประวัติการเข้าร่วม</a> &nbsp;|&nbsp;
            
            <a href="/routes/User.php?action=manage_event" style="text-decoration: none; color: black;">⚙️ จัดการกิจกรรม</a> &nbsp;|&nbsp;
            
            <a href="/routes/User.php?action=logout" style="text-decoration: none; color: #e74c3c;">🚪 ออกจากระบบ</a>
        </div>

    </div>
<?php else: ?>
    <?php endif; ?>