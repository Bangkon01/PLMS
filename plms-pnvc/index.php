<?php
// index.php
require_once 'config.php';

// ดึงข้อมูลผู้ใช้
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'ผู้เยี่ยมชม';
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
$is_logged_in = isLoggedIn();

// ดึงข้อมูลจากฐานข้อมูล
try {
    // ใช้ฟังก์ชัน getAllBooks() จาก config.php
    $books = getAllBooks();
    $available_books = getAvailableBooks();
    $books_count = count($books);
    $available_count = count($available_books);
} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ใช้ค่าเริ่มต้น
    $books_count = 0;
    $available_count = 0;
    handleDatabaseError($e);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMS-SYSTEM - ระบบยืม-คืนหนังสือ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo-img {
            font-size: 2.5rem;
            margin-right: 15px;
            color: #3498db;
        }
        
        .logo-text h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .logo-text p {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .main-nav ul {
            display: flex;
            list-style: none;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .main-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s;
        }
        
        .main-nav a i {
            margin-right: 8px;
        }
        
        .main-nav a:hover, .main-nav a.active {
            background-color: #3498db;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background-color: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 5px;
        }
        
        .user-info i {
            font-size: 1.2rem;
        }
        
        .btn-logout {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .btn-logout:hover {
            background-color: #c0392b;
        }
        
        .main-content {
            padding: 40px 0;
        }
        
        .welcome-section {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
            margin-top: 15px;
        }
        
        .btn-admin {
            background-color: #e74c3c;
        }
        
        .btn-logout-main {
            background-color: #95a5a6;
        }
        
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .link-card {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .link-card:hover {
            transform: translateY(-5px);
        }
        
        .link-card i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .link-card h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-nav ul {
                justify-content: center;
            }
            
            .logo {
                text-align: center;
                flex-direction: column;
            }
            
            .logo-img {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .quick-links {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-img">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="logo-text">
                        <h1><?php echo SITE_NAME; ?></h1>
                        <p>ระบบจัดการยืม-คืนหนังสือ</p>
                    </div>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <?php
                        $current_page = basename($_SERVER['PHP_SELF']);
                        ?>
                        <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> หน้าแรก</a></li>
                        <li><a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>"><i class="fas fa-list"></i> หมวดหมู่</a></li>
                        <li><a href="borrow.php" class="<?php echo $current_page == 'borrow.php' ? 'active' : ''; ?>"><i class="fas fa-book"></i> ยืมหนังสือ</a></li>
                        <li><a href="return.php" class="<?php echo $current_page == 'return.php' ? 'active' : ''; ?>"><i class="fas fa-exchange-alt"></i> คืนหนังสือ</a></li>
                        <li><a href="history.php" class="<?php echo $current_page == 'history.php' ? 'active' : ''; ?>"><i class="fas fa-history"></i> ประวัติ</a></li>
                        <?php if ($is_logged_in): ?>
                            <?php if (isAdmin()): ?>
                                <li><a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a></li>
                            <?php endif; ?>
                            <li><a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
                        <?php else: ?>
                            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <?php if ($is_logged_in): ?>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <div><strong><?php echo escape($user_name); ?></strong></div>
                        <div style="font-size: 0.8rem; opacity: 0.8;"><?php echo $user_role; ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Flash Messages -->
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <i class="fas fa-<?php echo $flash['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>

            <div class="welcome-section">
                <h2>ยินดีต้อนรับสู่ระบบ PLMS-SYSTEM</h2>
                <p>ระบบจัดการยืม-คืนหนังสือออนไลน์สำหรับสถาบันการศึกษา</p>
                
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-item">
                        <i class="fas fa-book" style="color: #3498db; font-size: 1.5rem;"></i>
                        <div class="stat-number"><?php echo $books_count; ?></div>
                        <div class="stat-label">หนังสือทั้งหมด</div>
                    </div>
                    
                    <div class="stat-item">
                        <i class="fas fa-check-circle" style="color: #2ecc71; font-size: 1.5rem;"></i>
                        <div class="stat-number"><?php echo $available_count; ?></div>
                        <div class="stat-label">พร้อมให้ยืม</div>
                    </div>
                    
                    <div class="stat-item">
                        <i class="fas fa-users" style="color: #e74c3c; font-size: 1.5rem;"></i>
                        <div class="stat-number"><?php 
                            try {
                                $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
                                $result = $stmt->fetch();
                                echo $result['count'] ?? 0;
                            } catch (Exception $e) {
                                echo "0";
                            }
                        ?></div>
                        <div class="stat-label">ผู้ใช้ทั้งหมด</div>
                    </div>
                    
                    <div class="stat-item">
                        <i class="fas fa-exchange-alt" style="color: #f39c12; font-size: 1.5rem;"></i>
                        <div class="stat-number"><?php 
                            try {
                                $stmt = $db->query("SELECT COUNT(*) as count FROM transactions");
                                $result = $stmt->fetch();
                                echo $result['count'] ?? 0;
                            } catch (Exception $e) {
                                echo "0";
                            }
                        ?></div>
                        <div class="stat-label">การยืมทั้งหมด</div>
                    </div>
                </div>
                
                <?php if ($is_logged_in): ?>
                    <p style="margin-top: 20px; padding: 10px; background: #e3f2fd; border-radius: 5px;">
                        คุณล็อกอินในฐานะ: <strong><?php echo $user_role; ?> - <?php echo escape($user_name); ?></strong>
                    </p>
                    
                    <?php if (isAdmin()): ?>
                        <a href="admin/dashboard.php" class="btn btn-admin">
                            <i class="fas fa-cog"></i> เข้าสู่ระบบผู้ดูแล
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="margin-top: 20px; padding: 10px; background: #fff3cd; border-radius: 5px;">
                        <i class="fas fa-info-circle"></i> กรุณาล็อกอินเพื่อใช้งานระบบ
                    </p>
                    <a href="login.php" class="btn">เข้าสู่ระบบ</a>
                <?php endif; ?>
            </div>
            
            <div class="quick-links">
                <div class="link-card">
                    <i class="fas fa-search"></i>
                    <h3>ค้นหาหนังสือ</h3>
                    <p>ค้นหาหนังสือที่ต้องการจากระบบ</p>
                    <a href="categories.php" class="btn" style="margin-top: 10px;">เข้าสู่หมวดหมู่</a>
                </div>
                
                <div class="link-card">
                    <i class="fas fa-book"></i>
                    <h3>ยืมหนังสือ</h3>
                    <p>ยืมหนังสือที่ต้องการด้วยขั้นตอนง่ายๆ</p>
                    <a href="borrow.php" class="btn" style="margin-top: 10px;">ยืมหนังสือ</a>
                </div>
                
                <div class="link-card">
                    <i class="fas fa-exchange-alt"></i>
                    <h3>คืนหนังสือ</h3>
                    <p>คืนหนังสืออย่างรวดเร็ว พร้อมตรวจสอบสภาพ</p>
                    <a href="return.php" class="btn" style="margin-top: 10px;">คืนหนังสือ</a>
                </div>
                
                <div class="link-card">
                    <i class="fas fa-history"></i>
                    <h3>ตรวจสอบประวัติ</h3>
                    <p>ตรวจสอบประวัติการยืม-คืนหนังสือ</p>
                    <a href="history.php" class="btn" style="margin-top: 10px;">ดูประวัติ</a>
                </div>
            </div>
            
            <!-- Recent Books -->
            <?php if ($books_count > 0): ?>
            <div class="welcome-section" style="margin-top: 30px;">
                <h3><i class="fas fa-book-open"></i> หนังสือแนะนำ</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                    <?php 
                    $recent_books = array_slice($books, 0, 4);
                    foreach ($recent_books as $book): 
                    ?>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 3px solid #3498db;">
                        <h4 style="margin-bottom: 5px; color: #2c3e50;"><?php echo escape($book['title']); ?></h4>
                        <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 5px;">
                            <i class="fas fa-user-edit"></i> <?php echo escape($book['author']); ?>
                        </p>
                        <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 5px;">
                            <i class="fas fa-tag"></i> <?php echo escape($book['category_name'] ?? 'ไม่มีหมวดหมู่'); ?>
                        </p>
                        <span style="background: <?php echo $book['available'] > 0 ? '#d4edda' : '#f8d7da'; ?>; 
                                color: <?php echo $book['available'] > 0 ? '#155724' : '#721c24'; ?>;
                                padding: 3px 8px; border-radius: 10px; font-size: 0.8rem;">
                            <?php echo $book['available'] > 0 ? 'พร้อมให้ยืม' : 'ถูกยืมหมด'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <footer style="background-color: #2c3e50; color: white; padding: 40px 0 20px; margin-top: 40px;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px;">
                <div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 10px; display: flex; align-items: center;">
                        <i class="fas fa-book-open" style="margin-right: 10px; color: #3498db;"></i> <?php echo SITE_NAME; ?>
                    </h3>
                    <p style="opacity: 0.8;">ระบบจัดการยืม-คืนหนังสือออนไลน์</p>
                </div>
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.2rem;">ลิงก์ด่วน</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 10px;"><a href="index.php" style="color: #ecf0f1; text-decoration: none; transition: color 0.3s;">หน้าแรก</a></li>
                        <li style="margin-bottom: 10px;"><a href="categories.php" style="color: #ecf0f1; text-decoration: none; transition: color 0.3s;">หมวดหมู่หนังสือ</a></li>
                        <li style="margin-bottom: 10px;"><a href="borrow.php" style="color: #ecf0f1; text-decoration: none; transition: color 0.3s;">ยืมหนังสือ</a></li>
                        <li style="margin-bottom: 10px;"><a href="return.php" style="color: #ecf0f1; text-decoration: none; transition: color 0.3s;">คืนหนังสือ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.2rem;">ข้อมูลติดต่อ</h4>
                    <p style="margin-bottom: 10px; display: flex; align-items: center;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 10px; color: #3498db; width: 20px;"></i>
                        อาคารห้องสมุดกลาง มหาวิทยาลัยเทคโนโลยี
                    </p>
                    <p style="margin-bottom: 10px; display: flex; align-items: center;">
                        <i class="fas fa-phone" style="margin-right: 10px; color: #3498db; width: 20px;"></i>
                        02-123-4567
                    </p>
                    <p style="display: flex; align-items: center;">
                        <i class="fas fa-envelope" style="margin-right: 10px; color: #3498db; width: 20px;"></i>
                        contact@plms-system.ac.th
                    </p>
                </div>
            </div>
            <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px; text-align: center; color: #bdc3c7; font-size: 0.9rem;">
                <p>&copy; 2023 <?php echo SITE_NAME; ?>. สงวนลิขสิทธิ์</p>
                <p>พัฒนาโดยทีม PLMS-SYSTEM | สำหรับใช้ในการศึกษาเท่านั้น</p>
            </div>
        </div>
    </footer>
</body>
</html>