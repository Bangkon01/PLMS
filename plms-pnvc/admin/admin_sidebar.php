<?php
// admin/admin_sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-container">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-profile">
            <div class="profile-image">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="profile-info">
                <h4><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'ผู้ดูแลระบบ'; ?></h4>
                <p>ผู้ดูแลระบบ</p>
            </div>
        </div>
        
        <nav class="admin-menu">
            <ul>
                <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> แดชบอร์ด
                </a></li>
                <li class="menu-section">จัดการข้อมูล</li>
                <li><a href="students.php" class="<?php echo $current_page == 'students.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-graduate"></i> นักศึกษา
                </a></li>
                <li><a href="teachers.php" class="<?php echo $current_page == 'teachers.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chalkboard-teacher"></i> ครู/อาจารย์
                </a></li>
                <li><a href="staff.php" class="<?php echo $current_page == 'staff.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-tie"></i> บุคลากร
                </a></li>
                <li><a href="books.php" class="<?php echo $current_page == 'books.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> หนังสือ
                </a></li>
                <li><a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> ประเภทหนังสือ
                </a></li>
                <li class="menu-section">รายงาน</li>
                <li><a href="reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i> สถิติการยืม-คืน
                </a></li>
                <li><a href="transactions.php" class="<?php echo $current_page == 'transactions.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i> ประวัติการยืมทั้งหมด
                </a></li>
                <li class="menu-section">การตั้งค่า</li>
                <li><a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> ตั้งค่าระบบ
                </a></li>
                <li><a href="../index.php">
                    <i class="fas fa-home"></i> กลับหน้าแรก
                </a></li>
            </ul>
        </nav>
    </div>