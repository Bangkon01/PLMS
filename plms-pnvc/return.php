<?php
require_once 'config.php';

// ตรวจสอบการล็อกอิน
if (!isLoggedIn()) {
    $_SESSION['login_redirect'] = 'return.php';
    redirect('login.php');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction_id = $_POST['transaction_id'] ?? '';
    $return_date = $_POST['return_date'] ?? '';
    
    if (empty($transaction_id) || empty($return_date)) {
        $error = 'กรุณากรอกรหัสการยืมและวันที่คืน';
    } else {
        $transaction_found = false;
        
        foreach ($_SESSION['transactions'] as &$transaction) {
            if ($transaction['id'] === $transaction_id && $transaction['status'] === 'borrowed') {
                // อัพเดทรายการคืน
                $transaction['actual_return_date'] = $return_date;
                $transaction['status'] = 'returned';
                
                // อัพเดทสถานะหนังสือ
                foreach ($_SESSION['books'] as &$book) {
                    if ($book['id'] == $transaction['book_id']) {
                        $book['available'] = true;
                        break;
                    }
                }
                
                $success = "คืนหนังสือสำเร็จ! <br>ชื่อหนังสือ: <strong>{$transaction['book_title']}</strong>";
                $transaction_found = true;
                break;
            }
        }
        
        if (!$transaction_found) {
            $error = 'ไม่พบรหัสการยืมหรือหนังสือถูกคืนแล้ว';
        }
    }
}

// รายการที่ยังไม่ได้คืน
$active_transactions = array_filter($_SESSION['transactions'], function($transaction) {
    return $transaction['status'] === 'borrowed';
});
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คืนหนังสือ - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        
    </style>
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
                    <li><a href="return.php" class="active"><i class="fas fa-exchange-alt"></i> คืนหนังสือ</a></li>
                    <li><a href="history.php"><i class="fas fa-history"></i> ประวัติ</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Return Form -->
    <div class="form-container">
        <h2>คืนหนังสือ</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="transaction_id"><i class="fas fa-barcode"></i> รหัสการยืม</label>
                <input type="text" id="transaction_id" name="transaction_id" placeholder="TRXXXXXX" required>
                <small>สามารถดูรหัสการยืมได้ในหน้าประวัติการยืม</small>
            </div>
            
            <div class="form-group">
                <label for="return_date"><i class="fas fa-calendar-check"></i> วันที่คืน</label>
                <input type="date" id="return_date" name="return_date" required>
            </div>
            
            <button type="submit" class="btn btn-block">ยืนยันการคืนหนังสือ</button>
        </form>
        
        <div style="margin-top: 30px;">
            <h3>รายการที่ยังไม่ได้คืน (<?php echo count($active_transactions); ?> รายการ)</h3>
            <?php if (count($active_transactions) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>รหัสการยืม</th>
                                <th>ชื่อหนังสือ</th>
                                <th>ผู้ยืม</th>
                                <th>วันที่ยืม</th>
                                <th>วันที่คืน (กำหนด)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo $transaction['id']; ?></td>
                                    <td><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['borrower_name']); ?></td>
                                    <td><?php echo $transaction['borrow_date']; ?></td>
                                    <td><?php echo $transaction['expected_return_date']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="alert alert-info">ไม่มีรายการที่ค้างคืนในขณะนี้</p>
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
                <p>พัฒนาโดยทีม PLMS-SYSTEM | สำหรับใช้ในการการศึกษาเท่านั้น</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>