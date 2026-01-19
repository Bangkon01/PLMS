<?php
require_once 'config.php';

// ตรวจสอบการล็อกอิน
if (!isLoggedIn()) {
    $_SESSION['login_redirect'] = 'history.php';
    redirect('login.php');
}

// กรองตามผู้ใช้ถ้าไม่ใช่แอดมิน
$user_transactions = $_SESSION['transactions'];
if ($_SESSION['username'] !== 'admin') {
    $user_transactions = array_filter($user_transactions, function($transaction) {
        return $transaction['borrower_id'] == $_SESSION['user_id'];
    });
}

// เรียงลำดับตามวันที่ยืมล่าสุด
usort($user_transactions, function($a, $b) {
    return strtotime($b['borrow_date']) - strtotime($a['borrow_date']);
});
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการยืมคืน - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
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
                    <li><a href="index.php"><i class="fas fa-home"></i> หน้าแรก</a></li>
                    <li><a href="categories.php"><i class="fas fa-list"></i> หมวดหมู่</a></li>
                    <li><a href="borrow.php"><i class="fas fa-book"></i> ยืมหนังสือ</a></li>
                    <li><a href="return.php"><i class="fas fa-exchange-alt"></i> คืนหนังสือ</a></li>
                    <li><a href="history.php" class="active"><i class="fas fa-history"></i> ประวัติ</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- History -->
    <div class="form-container">
        <h2>ประวัติการยืม-คืนหนังสือ</h2>
        
        <?php if ($_SESSION['username'] !== 'admin'): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> กำลังแสดงประวัติของ: <strong><?php echo $_SESSION['user_name']; ?></strong>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> กำลังแสดงประวัติทั้งหมด (โหมดผู้ดูแลระบบ)
            </div>
        <?php endif; ?>
        
        <div class="table-container">
            <?php if (count($user_transactions) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>รหัสการยืม</th>
                            <th>ชื่อหนังสือ</th>
                            <?php if ($_SESSION['username'] === 'admin'): ?>
                                <th>ผู้ยืม</th>
                            <?php endif; ?>
                            <th>วันที่ยืม</th>
                            <th>คืนกำหนด</th>
                            <th>คืนจริง</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($user_transactions as $transaction): ?>
                            <tr>
                                <td><?php echo $transaction['id']; ?></td>
                                <td><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                                <?php if ($_SESSION['username'] === 'admin'): ?>
                                    <td><?php echo htmlspecialchars($transaction['borrower_name']); ?></td>
                                <?php endif; ?>
                                <td><?php echo $transaction['borrow_date']; ?></td>
                                <td><?php echo $transaction['expected_return_date']; ?></td>
                                <td><?php echo $transaction['actual_return_date'] ?: '-'; ?></td>
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
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-history" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <h3>ไม่พบประวัติการยืม-คืน</h3>
                    <p>ยังไม่มีรายการยืม-คืนหนังสือ</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3><i class="fas fa-book-open"></i> <?php echo SITE_NAME; ?></h3>
                    <p>ระบบจัดการยืม-คืนหนังสือออนไลน์</p>
                </div>
                <div class="footer-links">
                    <h4>ลิงก์ด่วน</h4>
                    <ul>
                        <li><a href="index.php">หน้าแรก</a></li>
                        <li><a href="categories.php">หมวดหมู่หนังสือ</a></li>
                        <li><a href="borrow.php">ยืมหนังสือ</a></li>
                        <li><a href="return.php">คืนหนังสือ</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>ข้อมูลติดต่อ</h4>
                    <p><i class="fas fa-map-marker-alt"></i> อาคารห้องสมุดกลาง มหาวิทยาลัยเทคโนโลยี</p>
                    <p><i class="fas fa-phone"></i> 02-123-4567</p>
                    <p><i class="fas fa-envelope"></i> contact@plms-system.ac.th</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 <?php echo SITE_NAME; ?>. สงวนลิขสิทธิ์</p>
                <p>พัฒนาโดยทีม PLMS-SYSTEM | สำหรับใช้ในการศึกษาเท่านั้น</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>