<?php
// header.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - PLMS-SYSTEM' : 'PLMS-SYSTEM'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
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
            justify-content: space-between; /* Keep space between logo and nav */
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-right: auto; /* Push the logo to the left */
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
        
        .main-nav {
            flex: 1;
            display: flex;
            justify-content: center; /* Center the navigation */
            align-items: center; /* Align items vertically in the center */
            margin: 0; /* Remove auto margin */
        }
        
        .main-nav ul {
            display: flex;
            list-style: none;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
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
            transition: background-color 0.3s;
        }
        
        .btn-logout:hover {
            background-color: #c0392b;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-nav ul {
                justify-content: center;
            }
            
            .logo {
                flex-direction: column;
                text-align: center;
            }
            
            .logo-img {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-img">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="logo-text">
                        <h1>PLMS-SYSTEM</h1>
                        <p>ระบบจัดการยืม-คืนหนังสือ</p>
                    </div>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <?php
                        $current_page = basename($_SERVER['PHP_SELF']);
                        ?>
                        <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> หน้าแรก
                        </a></li>
                        <li><a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i> หมวดหมู่
                        </a></li>
                        <li><a href="borrow.php" class="<?php echo $current_page == 'borrow.php' ? 'active' : ''; ?>">
                            <i class="fas fa-book"></i> ยืมหนังสือ
                        </a></li>
                        <li><a href="return.php" class="<?php echo $current_page == 'return.php' ? 'active' : ''; ?>">
                            <i class="fas fa-exchange-alt"></i> คืนหนังสือ
                        </a></li>
                        <li><a href="history.php" class="<?php echo $current_page == 'history.php' ? 'active' : ''; ?>">
                            <i class="fas fa-history"></i> ประวัติ
                        </a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <li><a href="admin/dashboard.php">
                                    <i class="fas fa-cog"></i> Admin
                                </a></li>
                            <?php endif; ?>
                            <li><a href="logout.php" class="btn-logout">
                                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                            </a></li>
                        <?php else: ?>
                            <li><a href="login.php">
                                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                            </a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <div><strong><?php echo $_SESSION['user_name'] ?? 'ผู้ใช้'; ?></strong></div>
                        <div style="font-size: 0.8rem; opacity: 0.8;">
                            <?php 
                            $role_text = '';
                            switch ($_SESSION['user_role'] ?? '') {
                                case 'admin': $role_text = 'ผู้ดูแลระบบ'; break;
                                case 'student': $role_text = 'นักศึกษา'; break;
                                case 'teacher': $role_text = 'ครู/อาจารย์'; break;
                                case 'staff': $role_text = 'บุคลากร'; break;
                                default: $role_text = 'ผู้ใช้';
                            }
                            echo $role_text;
                            ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>