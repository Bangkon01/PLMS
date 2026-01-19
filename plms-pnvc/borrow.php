<?php
// borrow.php (ส่วนที่เกี่ยวข้อง)
require_once 'config.php';

// ตรวจสอบการล็อกอิน
if (!isLoggedIn()) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// รับ book_id จาก URL
$book_id = $_GET['book_id'] ?? null;

$page_title = 'ยืมหนังสือ';
$success = '';
$error = '';

// ถ้ามีการส่ง book_id มาก็แสดงหนังสือเล่มนั้น
if ($book_id) {
    $selected_book = null;
    foreach ($_SESSION['books'] as $book) {
        if ($book['id'] == $book_id) {
            $selected_book = $book;
            break;
        }
    }
    
    if ($selected_book) {
        // ตั้งค่าข้อมูลเริ่มต้นในฟอร์ม
        $_POST['book_id'] = $selected_book['id'];
        $_POST['borrower_name'] = $_SESSION['user_name'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'] ?? '';
    $borrower_name = $_POST['borrower_name'] ?? '';
    $borrow_date = $_POST['borrow_date'] ?? date('Y-m-d');
    $return_date = $_POST['return_date'] ?? date('Y-m-d', strtotime('+7 days'));
    
    if (empty($book_id) || empty($borrower_name)) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } else {
        // ค้นหาหนังสือ
        $book_found = false;
        foreach ($_SESSION['books'] as &$book) {
            if ($book['id'] == $book_id) {
                if ($book['available']) {
                    // สร้างรหัสการยืม
                    $transaction_id = 'TR' . str_pad(count($_SESSION['transactions']) + 1000, 7, '0', STR_PAD_LEFT);
                    
                    // สร้างรายการยืม
                    $transaction = [
                        'id' => $transaction_id,
                        'book_id' => $book_id,
                        'book_title' => $book['title'],
                        'user_id' => $_SESSION['user_id'],
                        'user_name' => $borrower_name,
                        'borrow_date' => $borrow_date,
                        'expected_return_date' => $return_date,
                        'actual_return_date' => null,
                        'status' => 'borrowed'
                    ];
                    
                    $_SESSION['transactions'][] = $transaction;
                    
                    // อัพเดทสถานะหนังสือ
                    $book['available'] = false;
                    
                    $success = "ยืมหนังสือสำเร็จ!<br>ชื่อหนังสือ: <strong>{$book['title']}</strong><br>รหัสการยืม: <strong>$transaction_id</strong>";
                } else {
                    $error = 'หนังสือเล่มนี้ถูกยืมไปแล้ว';
                }
                $book_found = true;
                break;
            }
        }
        
        if (!$book_found) {
            $error = 'ไม่พบหนังสือที่เลือก';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .borrow-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .borrow-container {
                grid-template-columns: 1fr;
            }
        }
        
        /* ส่วนแสดงหนังสือ */
        .book-preview {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .book-preview-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .book-preview-image {
            width: 100px;
            height: 140px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .book-preview-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .book-category {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .book-details-list {
            margin-top: 15px;
        }
        
        .book-detail-item {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .book-detail-item:last-child {
            border-bottom: none;
        }
        
        .book-detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 100px;
        }
        
        .book-detail-value {
            color: #6c757d;
        }
        
        /* ส่วนฟอร์ม */
        .form-container {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
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
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> ยืมหนังสือ</h1>
            <p>กรอกข้อมูลเพื่อยืมหนังสือจากระบบ</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <div style="margin-top: 15px;">
                    <a href="categories.php" class="btn" style="margin-right: 10px;">
                        <i class="fas fa-book"></i> ดูหนังสืออื่นๆ
                    </a>
                    <a href="history.php" class="btn btn-success">
                        <i class="fas fa-history"></i> ดูประวัติการยืม
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> คุณล็อกอินในฐานะ: <strong><?php echo $_SESSION['user_role']; ?></strong>
        </div>
        
        <div class="borrow-container">
            <!-- ส่วนแสดงหนังสือ -->
            <?php if (isset($selected_book)): ?>
            <div class="book-preview">
                <div class="book-preview-header">
                    <div class="book-preview-image">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="book-preview-info">
                        <h3><?php echo $selected_book['title']; ?></h3>
                        <span class="book-category"><?php echo $selected_book['category']; ?></span>
                    </div>
                </div>
                
                <div class="book-details-list">
                    <div class="book-detail-item">
                        <span class="book-detail-label">ผู้เขียน:</span>
                        <span class="book-detail-value"><?php echo $selected_book['author']; ?></span>
                    </div>
                    <div class="book-detail-item">
                        <span class="book-detail-label">ปีที่พิมพ์:</span>
                        <span class="book-detail-value"><?php echo $selected_book['year']; ?></span>
                    </div>
                    <div class="book-detail-item">
                        <span class="book-detail-label">สถานะ:</span>
                        <span class="book-detail-value">
                            <span style="padding: 5px 10px; border-radius: 20px; background-color: <?php echo $selected_book['available'] ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $selected_book['available'] ? '#155724' : '#721c24'; ?>; font-weight: bold;">
                                <?php echo $selected_book['available'] ? 'พร้อมให้ยืม' : 'ถูกยืมแล้ว'; ?>
                            </span>
                        </span>
                    </div>
                </div>
                
                <?php if (isset($selected_book['description'])): ?>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <h4>รายละเอียด:</h4>
                    <p style="color: #6c757d; line-height: 1.6;"><?php echo $selected_book['description']; ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="book-preview">
                <h3>เลือกหนังสือ</h3>
                <p>กรุณาเลือกหนังสือจากหน้ามดวดหมู่</p>
                <a href="categories.php" class="btn btn-block" style="margin-top: 20px;">
                    <i class="fas fa-arrow-left"></i> กลับไปเลือกหนังสือ
                </a>
            </div>
            <?php endif; ?>
            
            <!-- ส่วนฟอร์ม -->
            <div class="form-container">
                <h2><i class="fas fa-edit"></i> แบบฟอร์มยืมหนังสือ</h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="book_id" value="<?php echo $selected_book['id'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label for="borrower_name"><i class="fas fa-user"></i> ชื่อผู้ยืม *</label>
                        <input type="text" id="borrower_name" name="borrower_name" 
                               value="<?php echo $_SESSION['user_name']; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="borrow_date"><i class="fas fa-calendar-plus"></i> วันที่ยืม *</label>
                            <input type="date" id="borrow_date" name="borrow_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="return_date"><i class="fas fa-calendar-check"></i> วันที่คืน (กำหนด) *</label>
                            <input type="date" id="return_date" name="return_date" 
                                   value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                        </div>
                    </div>
                    
                    <?php if (isset($selected_book) && $selected_book['available']): ?>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> ยืนยันการยืมหนังสือ
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-block" disabled style="background-color: #6c757d;">
                            <i class="fas fa-times"></i> ไม่สามารถยืมหนังสือได้
                        </button>
                        <a href="categories.php" class="btn btn-block" style="margin-top: 10px;">
                            <i class="fas fa-book"></i> เลือกหนังสืออื่น
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ตั้งค่าวันเริ่มต้นและวันที่คืน
            const today = new Date().toISOString().split('T')[0];
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            const nextWeekFormatted = nextWeek.toISOString().split('T')[0];
            
            document.getElementById('borrow_date').value = today;
            document.getElementById('borrow_date').min = today;
            document.getElementById('return_date').value = nextWeekFormatted;
            document.getElementById('return_date').min = today;
        });
    </script>
</body>
</html>