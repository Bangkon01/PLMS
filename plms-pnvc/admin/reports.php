<?php
// admin/reports.php
require_once '../config.php';
require_once '../includes/auth.php';

// ตรวจสอบสิทธิ์ Admin
Auth::requireAdmin();

$page_title = 'รายงานสถิติการยืม-คืน';
$report_type = $_GET['type'] ?? 'summary';
$date = $_GET['date'] ?? date('Y-m-d');
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// ดึงข้อมูลรายงาน
$summary = $sessionHandler->getSummaryReport();
$dailyStats = $sessionHandler->getDailyStats($date);
$monthlyStats = $sessionHandler->getMonthlyStats($year, $month);
$categoryStats = $sessionHandler->getCategoryStats();
$userStats = $sessionHandler->getUserStats();
$popularBooks = $sessionHandler->getPopularBooks(10);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-content-header">
                <h1><i class="fas fa-chart-bar"></i> <?php echo $page_title; ?></h1>
                <div class="breadcrumb">
                    <a href="dashboard.php">แดชบอร์ด</a> &raquo; <span>รายงานสถิติ</span>
                </div>
            </div>

            <!-- รายงานเมนู -->
            <div class="report-tabs">
                <a href="?type=summary" class="<?php echo $report_type == 'summary' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i> สรุปภาพรวม
                </a>
                <a href="?type=daily" class="<?php echo $report_type == 'daily' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-day"></i> รายวัน
                </a>
                <a href="?type=monthly" class="<?php echo $report_type == 'monthly' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i> รายเดือน
                </a>
                <a href="?type=category" class="<?php echo $report_type == 'category' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> ตามประเภท
                </a>
                <a href="?type=user" class="<?php echo $report_type == 'user' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> ตามผู้ใช้
                </a>
                <a href="?type=popular" class="<?php echo $report_type == 'popular' ? 'active' : ''; ?>">
                    <i class="fas fa-star"></i> ยอดนิยม
                </a>
            </div>

            <!-- สรุปภาพรวม -->
            <?php if ($report_type == 'summary'): ?>
                <div class="report-summary">
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

                    <div class="report-details">
                        <div class="detail-card">
                            <h3><i class="fas fa-info-circle"></i> ข้อมูลหนังสือ</h3>
                            <ul>
                                <li>จำนวนหนังสือทั้งหมด: <?php echo number_format($summary['total_books']); ?> เล่ม</li>
                                <li>หนังสือที่พร้อมให้ยืม: <?php echo number_format($summary['available_books']); ?> เล่ม</li>
                                <li>หนังสือที่ถูกยืมอยู่: <?php echo number_format($summary['borrowed_books']); ?> เล่ม</li>
                                <li>จำนวนประเภทหนังสือ: <?php echo number_format($summary['categories_count']); ?> ประเภท</li>
                            </ul>
                        </div>
                        
                        <div class="detail-card">
                            <h3><i class="fas fa-exchange-alt"></i> ข้อมูลการยืม-คืน</h3>
                            <ul>
                                <li>การยืมทั้งหมด: <?php echo number_format($summary['total_transactions']); ?> ครั้ง</li>
                                <li>กำลังยืมอยู่: <?php echo number_format($summary['active_transactions']); ?> ครั้ง</li>
                                <li>คืนแล้ว: <?php echo number_format($summary['total_transactions'] - $summary['active_transactions']); ?> ครั้ง</li>
                                <li>ค่าปรับสะสม: <?php echo number_format($summary['total_late_fee']); ?> บาท</li>
                            </ul>
                        </div>
                    </div>
                </div>

            <!-- รายงานรายวัน -->
            <?php elseif ($report_type == 'daily'): ?>
                <div class="report-daily">
                    <div class="report-filter">
                        <h3>รายงานประจำวัน</h3>
                        <form method="GET" class="filter-form">
                            <input type="hidden" name="type" value="daily">
                            <input type="date" name="date" value="<?php echo $date; ?>" 
                                   onchange="this.form.submit()">
                        </form>
                    </div>
                    
                    <div class="daily-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $dailyStats['borrowed']; ?></h3>
                                <p>ยืมหนังสือ</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $dailyStats['returned']; ?></h3>
                                <p>คืนหนังสือ</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $dailyStats['total']; ?></h3>
                                <p>รวมทั้งหมด</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="report-table">
                        <h3>รายการยืม-คืนวันที่ <?php echo $date; ?></h3>
                        <?php 
                        $dailyTransactions = array_filter($sessionHandler->getAllTransactions(), 
                            function($transaction) use ($date) {
                                return substr($transaction['borrow_date'], 0, 10) === $date ||
                                       ($transaction['actual_return_date'] && 
                                        substr($transaction['actual_return_date'], 0, 10) === $date);
                            });
                        ?>
                        
                        <?php if (count($dailyTransactions) > 0): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>เวลา</th>
                                        <th>ผู้ใช้</th>
                                        <th>หนังสือ</th>
                                        <th>ประเภท</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dailyTransactions as $transaction): ?>
                                    <tr>
                                        <td>
                                            <?php echo substr($transaction['borrow_date'], 11, 5); ?>
                                            <?php if ($transaction['actual_return_date']): ?>
                                            <br><small>คืน: <?php echo substr($transaction['actual_return_date'], 11, 5); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $transaction['user_name']; ?></td>
                                        <td><?php echo $transaction['book_title']; ?></td>
                                        <td><?php echo $transaction['user_role']; ?></td>
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
                            <p class="no-data">ไม่มีข้อมูลการยืม-คืนในวันนี้</p>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- รายงานรายเดือน -->
            <?php elseif ($report_type == 'monthly'): ?>
                <div class="report-monthly">
                    <div class="report-filter">
                        <h3>รายงานประจำเดือน</h3>
                        <form method="GET" class="filter-form">
                            <input type="hidden" name="type" value="monthly">
                            <div class="form-row">
                                <select name="year" onchange="this.form.submit()">
                                    <?php for ($y = 2022; $y <= date('Y'); $y++): ?>
                                    <option value="<?php echo $y; ?>" 
                                        <?php echo $year == $y ? 'selected' : ''; ?>>
                                        พ.ศ. <?php echo $y + 543; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                                
                                <select name="month" onchange="this.form.submit()">
                                    <?php 
                                    $months = [
                                        '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม',
                                        '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน',
                                        '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน',
                                        '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                                    ];
                                    ?>
                                    <?php foreach ($months as $key => $name): ?>
                                    <option value="<?php echo $key; ?>" 
                                        <?php echo $month == $key ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    
                    <div class="monthly-stats">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $monthlyStats['borrowed']; ?></h3>
                                    <p>ยืมหนังสือ</p>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $monthlyStats['returned']; ?></h3>
                                    <p>คืนหนังสือ</p>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $monthlyStats['late_returns']; ?></h3>
                                    <p>คืนล่าช้า</p>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo number_format($monthlyStats['total_late_fee']); ?></h3>
                                    <p>ค่าปรับ (บาท)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="monthly-chart">
                        <h3>สถิติการยืม-คืน ประจำเดือน <?php echo $months[$month]; ?> <?php echo $year + 543; ?></h3>
                        <div class="chart-container">
                            <div class="chart-bar">
                                <div class="bar-label">ยืมหนังสือ</div>
                                <div class="bar-container">
                                    <div class="bar-fill" style="width: <?php echo min($monthlyStats['borrowed'] * 10, 100); ?>%;">
                                        <?php echo $monthlyStats['borrowed']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-bar">
                                <div class="bar-label">คืนหนังสือ</div>
                                <div class="bar-container">
                                    <div class="bar-fill" style="width: <?php echo min($monthlyStats['returned'] * 10, 100); ?>%;">
                                        <?php echo $monthlyStats['returned']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-bar">
                                <div class="bar-label">คืนล่าช้า</div>
                                <div class="bar-container">
                                    <div class="bar-fill" style="width: <?php echo min($monthlyStats['late_returns'] * 20, 100); ?>%;">
                                        <?php echo $monthlyStats['late_returns']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- รายงานตามประเภทหนังสือ -->
            <?php elseif ($report_type == 'category'): ?>
                <div class="report-category">
                    <h3>สถิติตามประเภทหนังสือ</h3>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ประเภทหนังสือ</th>
                                <th>จำนวนหนังสือ</th>
                                <th>ว่าง</th>
                                <th>ถูกยืม</th>
                                <th>จำนวนการยืม</th>
                                <th>อัตราการยืม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categoryStats as $category): ?>
                            <?php 
                            $rate = $category['total_books'] > 0 ? 
                                    round(($category['total_borrowed'] / $category['total_books']) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?php echo $category['name']; ?></td>
                                <td><?php echo $category['total_books']; ?></td>
                                <td><?php echo $category['available_books']; ?></td>
                                <td><?php echo $category['total_books'] - $category['available_books']; ?></td>
                                <td><?php echo $category['total_borrowed']; ?></td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $rate; ?>%;"></div>
                                        <span><?php echo $rate; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <!-- รายงานตามผู้ใช้ -->
            <?php elseif ($report_type == 'user'): ?>
                <div class="report-user">
                    <h3>สถิติตามผู้ใช้</h3>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ชื่อผู้ใช้</th>
                                <th>ประเภท</th>
                                <th>ยืมทั้งหมด</th>
                                <th>กำลังยืม</th>
                                <th>คืนล่าช้า</th>
                                <th>ค่าปรับ (บาท)</th>
                                <th>อัตราการยืม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userStats as $user): ?>
                            <?php 
                            $rate = $user['total_borrowed'] > 0 ? 
                                    round(($user['current_borrowed'] / $user['total_borrowed']) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?php echo $user['name']; ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php 
                                        $roleNames = [
                                            'student' => 'นักศึกษา',
                                            'teacher' => 'ครู/อาจารย์',
                                            'staff' => 'บุคลากร'
                                        ];
                                        echo $roleNames[$user['role']] ?? $user['role'];
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo $user['total_borrowed']; ?></td>
                                <td><?php echo $user['current_borrowed']; ?></td>
                                <td><?php echo $user['late_returns']; ?></td>
                                <td><?php echo number_format($user['total_late_fee']); ?></td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $rate; ?>%;"></div>
                                        <span><?php echo $rate; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <!-- หนังสือยอดนิยม -->
            <?php elseif ($report_type == 'popular'): ?>
                <div class="report-popular">
                    <h3>หนังสือยอดนิยม 10 อันดับแรก</h3>
                    
                    <div class="popular-list">
                        <?php $rank = 1; ?>
                        <?php foreach ($popularBooks as $item): ?>
                        <?php if ($item['book']): ?>
                        <div class="popular-item">
                            <div class="popular-rank"><?php echo $rank; ?></div>
                            <div class="popular-info">
                                <h4><?php echo $item['book']['title']; ?></h4>
                                <p><i class="fas fa-user-edit"></i> <?php echo $item['book']['author']; ?></p>
                                <p><i class="fas fa-tag"></i> 
                                    <?php 
                                    $category = $sessionHandler->getCategoryById($item['book']['category_id']);
                                    echo $category ? $category['name'] : 'ไม่ระบุ';
                                    ?>
                                </p>
                            </div>
                            <div class="popular-stats">
                                <span class="count"><?php echo $item['count']; ?> ครั้ง</span>
                                <div class="progress-bar">
                                    <div class="progress-fill" 
                                         style="width: <?php echo min($item['count'] * 10, 100); ?>%;"></div>
                                </div>
                            </div>
                        </div>
                        <?php $rank++; ?>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ปุ่มพิมพ์รายงาน -->
            <div class="report-actions">
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> พิมพ์รายงาน
                </button>
                <a href="reports.php?type=summary" class="btn btn-primary">
                    <i class="fas fa-redo"></i> รีเฟรช
                </a>
            </div>
        </div>
    </div>

    <script src="../script.js"></script>
    <script src="admin.js"></script>
</body>
</html>