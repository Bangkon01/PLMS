<?php
// admin/dashboard.php
require_once '../config.php';
require_once '../includes/Auth.php';

// ตรวจสอบสิทธิ์ Admin
Auth::requireAdmin();

$page_title = 'แดชบอร์ดผู้ดูแลระบบ';

// ดึงข้อมูลจากฐานข้อมูล
$summary = getSummaryReport();
$recent_transactions = array_slice( getAllTransactions(), 0, 10);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
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
        
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .admin-logo i {
            margin-right: 10px;
            color: #3498db;
        }
        
        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-user-info i {
            font-size: 1.2rem;
        }
        
        .btn-logout {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn-logout:hover {
            background-color: #c0392b;
        }
        
        .admin-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        
        .admin-sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            position: sticky;
            top: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        
        .admin-profile {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2.5rem;
        }
        
        .profile-info h4 {
            margin-bottom: 5px;
        }
        
        .profile-info p {
            color: #bdc3c7;
            font-size: 0.9rem;
        }
        
        .admin-menu {
            padding: 20px 0;
        }
        
        .admin-menu ul {
            list-style: none;
        }
        
        .admin-menu li {
            margin-bottom: 5px;
        }
        
        .admin-menu a {
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .admin-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .admin-menu a:hover,
        .admin-menu a.active {
            background-color: #3498db;
            color: white;
        }
        
        .menu-section {
            padding: 15px 20px 5px;
            color: #95a5a6;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .admin-content {
            flex: 1;
            background-color: #f8f9fa;
            padding: 20px;
            overflow-y: auto;
        }
        
        .admin-content-header {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .admin-content-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .admin-content-header h1 i {
            margin-right: 10px;
            color: #3498db;
        }
        
        .breadcrumb {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-card:nth-child(1) .stat-icon { background-color: #3498db; }
        .stat-card:nth-child(2) .stat-icon { background-color: #2ecc71; }
        .stat-card:nth-child(3) .stat-icon { background-color: #e74c3c; }
        .stat-card:nth-child(4) .stat-icon { background-color: #f39c12; }
        
        .stat-info h3 {
            font-size: 2rem;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .stat-info p {
            color: #7f8c8d;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .table-container {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-borrowed {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-returned {
            background-color: #d4edda;
            color: #155724;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="admin-header-content">
                <div class="admin-logo">
                    <i class="fas fa-cog"></i>
                    <span><?php echo SITE_NAME; ?> - Admin Panel</span>
                </div>
                <div class="admin-user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo Auth::user()['name']; ?></span>
                    <a href="../logout.php" class="btn-logout">ออกจากระบบ</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-profile">
                <div class="profile-image">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="profile-info">
                    <h4><?php echo Auth::user()['name']; ?></h4>
                    <p>ผู้ดูแลระบบ</p>
                </div>
            </div>
            
            <nav class="admin-menu">
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> แดชบอร์ด</a></li>
                    <li class="menu-section">จัดการข้อมูล</li>
                    <li><a href="students.php"><i class="fas fa-user-graduate"></i> นักศึกษา</a></li>
                    <li><a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> ครู/อาจารย์</a></li>
                    <li><a href="staff.php"><i class="fas fa-user-tie"></i> บุคลากร</a></li>
                    <li><a href="books.php"><i class="fas fa-book"></i> หนังสือ</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> ประเภทหนังสือ</a></li>
                    <li class="menu-section">รายงาน</li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> สถิติการยืม-คืน</a></li>
                    <li><a href="../index.php"><i class="fas fa-home"></i> กลับหน้าแรก</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-content-header">
                <h1><i class="fas fa-tachometer-alt"></i> แดชบอร์ดผู้ดูแลระบบ</h1>
                <div class="breadcrumb">
                    <a href="../index.php">หน้าแรก</a> &raquo; <span>แดชบอร์ด</span>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <i class="fas fa-<?php echo $flash['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($summary['total_books']); ?></h3>
                        <p>หนังสือทั้งหมด</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($summary['total_users']); ?></h3>
                        <p>ผู้ใช้ทั้งหมด</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($summary['total_transactions']); ?></h3>
                        <p>การยืมทั้งหมด</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($summary['total_late_fee']); ?></h3>
                        <p>ค่าปรับทั้งหมด (บาท)</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="table-container">
                <h2><i class="fas fa-history"></i> การยืม-คืนล่าสุด</h2>
                
                <?php if (count($recent_transactions) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>วันที่ยืม</th>
                                <th>ผู้ใช้</th>
                                <th>หนังสือ</th>
                                <th>วันที่คืนกำหนด</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_transactions as $transaction): ?>
                            <tr>
                                <td><?php echo $transaction['borrow_date']; ?></td>
                                <td><?php echo $transaction['user_name']; ?></td>
                                <td><?php echo $transaction['book_title']; ?></td>
                                <td><?php echo $transaction['expected_return_date']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $transaction['status']; ?>">
                                        <?php echo $transaction['status'] === 'borrowed' ? 'กำลังยืม' : 'คืนแล้ว'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; padding: 20px; color: #6c757d;">
                        <i class="fas fa-history"></i> ยังไม่มีประวัติการยืม-คืน
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>