<?php
// categories.php
require_once 'config.php';

// ตรวจสอบการล็อกอิน
if (!isLoggedIn()) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// รับหมวดหมู่ที่เลือก
$selected_category = $_GET['category'] ?? 'all';
$category_name = '';

// กำหนดหมวดหมู่
$categories = [
    'all' => ['name' => 'หนังสือทั้งหมด', 'icon' => 'fas fa-book'],
    'วิทยานิพนธ์วิจัย' => ['name' => 'วิทยานิพนธ์วิจัย', 'icon' => 'fas fa-graduation-cap'],
    'พงศาวดาร' => ['name' => 'พงศาวดาร', 'icon' => 'fas fa-landmark'],
    'จิตวิทยา' => ['name' => 'จิตวิทยา', 'icon' => 'fas fa-brain']
];

// ดึงหนังสือตามหมวดหมู่
$filtered_books = $_SESSION['books'];
if ($selected_category !== 'all' && isset($categories[$selected_category])) {
    $filtered_books = array_filter($_SESSION['books'], function($book) use ($selected_category) {
        return $book['category'] === $selected_category;
    });
    $category_name = $categories[$selected_category]['name'];
}

$page_title = $selected_category === 'all' ? 'หมวดหมู่หนังสือ' : 'หมวดหมู่: ' . $category_name;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .page-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .page-header h1 i {
            margin-right: 10px;
            color: #3498db;
        }
        
        /* Category Navigation */
        .category-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 30px;
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .category-btn {
            padding: 10px 20px;
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            text-decoration: none;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .category-btn:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .category-btn.active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .category-btn i {
            font-size: 1.1rem;
        }
        
        /* Category Sections */
        .category-sections {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }
        
        .category-section {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .category-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .category-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .category-icon {
            font-size: 2rem;
            color: #3498db;
            background-color: white;
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .category-info h2 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .category-info p {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .view-all-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
            font-weight: 500;
        }
        
        .view-all-btn:hover {
            background-color: #2980b9;
        }
        
        /* Books Grid */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 30px;
        }
        
        .book-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .book-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .book-image i {
            font-size: 4rem;
            opacity: 0.9;
        }
        
        .book-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .book-content {
            padding: 20px;
        }
        
        .book-category {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
        
        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.4;
            height: 3.2em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .book-author {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .book-author i {
            color: #3498db;
        }
        
        .book-details {
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .book-detail-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .book-detail-item i {
            color: #3498db;
        }
        
        .book-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-borrow {
            flex: 1;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        
        .btn-borrow:hover {
            background-color: #218838;
        }
        
        .btn-detail {
            background-color: #17a2b8;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-detail:hover {
            background-color: #138496;
        }
        
        .btn-disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        .btn-disabled:hover {
            background-color: #5a6268;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            width: 90%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px 10px 0 0;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .close-modal {
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .book-modal-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        
        .book-modal-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            height: 300px;
        }
        
        .book-modal-image i {
            font-size: 5rem;
            opacity: 0.9;
        }
        
        .book-modal-info h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        
        .book-modal-meta {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .meta-item {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .meta-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .meta-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        
        .meta-value {
            color: #6c757d;
        }
        
        .book-description {
            line-height: 1.6;
            color: #495057;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .no-books {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .no-books i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .category-nav {
                justify-content: center;
            }
            
            .category-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .category-title {
                flex-direction: column;
                text-align: center;
            }
            
            .books-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                padding: 20px;
            }
            
            .book-modal-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 2% auto;
            }
        }
        
        @media (max-width: 576px) {
            .books-grid {
                grid-template-columns: 1fr;
            }
            
            .book-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> <?php echo $page_title; ?></h1>
            <p>ค้นหาและเลือกหนังสือจากหมวดหมู่ที่คุณสนใจ</p>
        </div>
        
        <!-- Category Navigation -->
        <div class="category-nav">
            <?php foreach ($categories as $key => $category): ?>
                <a href="?category=<?php echo $key; ?>" 
                   class="category-btn <?php echo $selected_category == $key ? 'active' : ''; ?>">
                    <i class="<?php echo $category['icon']; ?>"></i>
                    <?php echo $category['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Category Sections -->
        <div class="category-sections">
            <?php if ($selected_category == 'all'): ?>
                <!-- แสดงทุกหมวดหมู่ -->
                <?php foreach ($categories as $key => $category): ?>
                    <?php if ($key != 'all'): ?>
                        <?php 
                        $category_books = array_filter($_SESSION['books'], function($book) use ($key) {
                            return $book['category'] === $key;
                        });
                        
                        if (count($category_books) > 0):
                        ?>
                        <div class="category-section">
                            <div class="category-header">
                                <div class="category-title">
                                    <div class="category-icon">
                                        <i class="<?php echo $category['icon']; ?>"></i>
                                    </div>
                                    <div class="category-info">
                                        <h2><?php echo $category['name']; ?></h2>
                                        <p>พบหนังสือ <?php echo count($category_books); ?> เล่ม</p>
                                    </div>
                                </div>
                                <a href="?category=<?php echo $key; ?>" class="view-all-btn">
                                    <i class="fas fa-book-open"></i> ดูหนังสือทั้งหมด
                                </a>
                            </div>
                            
                            <div class="books-grid">
                                <?php 
                                $display_books = array_slice($category_books, 0, 3);
                                foreach ($display_books as $book): 
                                ?>
                                <div class="book-card" data-book-id="<?php echo $book['id']; ?>">
                                    <div class="book-image">
                                        <i class="fas fa-book"></i>
                                        <?php if (!$book['available']): ?>
                                            <div class="book-badge">ถูกยืมแล้ว</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="book-content">
                                        <span class="book-category"><?php echo $book['category']; ?></span>
                                        <h3 class="book-title"><?php echo $book['title']; ?></h3>
                                        <div class="book-author">
                                            <i class="fas fa-user-edit"></i>
                                            <span><?php echo $book['author']; ?></span>
                                        </div>
                                        <div class="book-details">
                                            <div class="book-detail-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span><?php echo $book['year']; ?></span>
                                            </div>
                                            <div class="book-detail-item">
                                                <i class="fas fa-id-card"></i>
                                                <span>B<?php echo str_pad($book['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                            </div>
                                        </div>
                                        <div class="book-actions">
                                            <?php if ($book['available']): ?>
                                                <a href="borrow.php?book_id=<?php echo $book['id']; ?>" class="btn-borrow">
                                                    <i class="fas fa-book"></i> ยืมหนังสือ
                                                </a>
                                            <?php else: ?>
                                                <button class="btn-borrow btn-disabled" disabled>
                                                    <i class="fas fa-clock"></i> ถูกยืมแล้ว
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn-detail" onclick="showBookDetail(<?php echo htmlspecialchars(json_encode($book)); ?>)">
                                                <i class="fas fa-info-circle"></i> รายละเอียด
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- แสดงเฉพาะหมวดหมู่ที่เลือก -->
                <div class="category-section">
                    <div class="category-header">
                        <div class="category-title">
                            <div class="category-icon">
                                <i class="<?php echo $categories[$selected_category]['icon']; ?>"></i>
                            </div>
                            <div class="category-info">
                                <h2><?php echo $category_name; ?></h2>
                                <p>พบหนังสือ <?php echo count($filtered_books); ?> เล่ม</p>
                            </div>
                        </div>
                        <a href="?category=all" class="view-all-btn">
                            <i class="fas fa-arrow-left"></i> กลับสู่หน้าหมวดหมู่ทั้งหมด
                        </a>
                    </div>
                    
                    <div class="books-grid">
                        <?php if (count($filtered_books) > 0): ?>
                            <?php foreach ($filtered_books as $book): ?>
                            <div class="book-card" data-book-id="<?php echo $book['id']; ?>">
                                <div class="book-image">
                                    <i class="fas fa-book"></i>
                                    <?php if (!$book['available']): ?>
                                        <div class="book-badge">ถูกยืมแล้ว</div>
                                    <?php endif; ?>
                                </div>
                                <div class="book-content">
                                    <span class="book-category"><?php echo $book['category']; ?></span>
                                    <h3 class="book-title"><?php echo $book['title']; ?></h3>
                                    <div class="book-author">
                                        <i class="fas fa-user-edit"></i>
                                        <span><?php echo $book['author']; ?></span>
                                    </div>
                                    <div class="book-details">
                                        <div class="book-detail-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?php echo $book['year']; ?></span>
                                        </div>
                                        <div class="book-detail-item">
                                            <i class="fas fa-id-card"></i>
                                            <span>B<?php echo str_pad($book['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                        </div>
                                    </div>
                                    <div class="book-actions">
                                        <?php if ($book['available']): ?>
                                            <a href="borrow.php?book_id=<?php echo $book['id']; ?>" class="btn-borrow">
                                                <i class="fas fa-book"></i> ยืมหนังสือ
                                            </a>
                                        <?php else: ?>
                                            <button class="btn-borrow btn-disabled" disabled>
                                                <i class="fas fa-clock"></i> ถูกยืมแล้ว
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-detail" onclick="showBookDetail(<?php echo htmlspecialchars(json_encode($book)); ?>)">
                                            <i class="fas fa-info-circle"></i> รายละเอียด
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="grid-column: 1 / -1;">
                                <div class="no-books">
                                    <i class="fas fa-book"></i>
                                    <h3>ไม่พบหนังสือในหมวดหมู่นี้</h3>
                                    <p>ยังไม่มีหนังสือในหมวดหมู่ <?php echo $category_name; ?></p>
                                    <a href="?category=all" class="btn-borrow" style="display: inline-flex; width: auto; margin-top: 20px;">
                                        <i class="fas fa-arrow-left"></i> กลับสู่หน้าหมวดหมู่ทั้งหมด
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <!-- Book Detail Modal -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-book"></i> รายละเอียดหนังสือ</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="bookModalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Book Detail Modal
        const modal = document.getElementById('bookModal');
        const closeBtn = document.querySelector('.close-modal');
        
        // ฟังก์ชันแสดงรายละเอียดหนังสือ
        function showBookDetail(book) {
            const modalContent = document.getElementById('bookModalContent');
            
            // แปลงสถานะหนังสือ
            const statusText = book.available ? 'พร้อมให้ยืม' : 'ถูกยืมแล้ว';
            const statusClass = book.available ? 'available' : 'unavailable';
            
            // สร้างเนื้อหา modal
            modalContent.innerHTML = `
                <div class="book-modal-grid">
                    <div class="book-modal-image">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="book-modal-info">
                        <h3>${book.title}</h3>
                        
                        <div class="book-modal-meta">
                            <div class="meta-item">
                                <span class="meta-label">หมวดหมู่:</span>
                                <span class="meta-value">${book.category}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">ผู้เขียน:</span>
                                <span class="meta-value">${book.author}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">ปีที่พิมพ์:</span>
                                <span class="meta-value">${book.year}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">รหัสหนังสือ:</span>
                                <span class="meta-value">B${book.id.toString().padStart(3, '0')}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">สถานะ:</span>
                                <span class="meta-value">
                                    <span style="padding: 5px 10px; border-radius: 20px; background-color: ${book.available ? '#d4edda' : '#f8d7da'}; color: ${book.available ? '#155724' : '#721c24'}; font-weight: bold;">
                                        ${statusText}
                                    </span>
                                </span>
                            </div>
                        </div>
                        
                        <div class="book-description">
                            <h4>รายละเอียดหนังสือ</h4>
                            <p>${book.description || 'ไม่มีรายละเอียดเพิ่มเติม'}</p>
                        </div>
                        
                        <div class="modal-actions">
                            ${book.available ? 
                                `<a href="borrow.php?book_id=${book.id}" class="btn-borrow" style="flex: 1;">
                                    <i class="fas fa-book"></i> ยืมหนังสือเล่มนี้
                                </a>` : 
                                `<button class="btn-borrow btn-disabled" style="flex: 1;" disabled>
                                    <i class="fas fa-clock"></i> ถูกยืมแล้ว
                                </button>`
                            }
                            <button class="btn-detail close-modal" style="background-color: #6c757d;">
                                <i class="fas fa-times"></i> ปิด
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // แสดง modal
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        // ปิด modal เมื่อคลิกปุ่มปิด
        closeBtn.onclick = function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // ปิด modal เมื่อคลิกนอกพื้นที่
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
        
        // ปิด modal ด้วยปุ่ม Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
        
        // เพิ่มข้อมูลหนังสือ (ตัวอย่าง)
        document.addEventListener('DOMContentLoaded', function() {
            // เพิ่มข้อมูลตัวอย่างหนังสือถ้ายังไม่มี
            <?php if (empty($_SESSION['books'])): ?>
            const sampleBooks = [
                {
                    id: 1,
                    title: 'วิทยานิพนธ์เรื่องการพัฒนาเว็บไซต์',
                    category: 'วิทยานิพนธ์วิจัย',
                    author: 'สมชาย ใจดี',
                    year: 2023,
                    description: 'วิทยานิพนธ์เกี่ยวกับการพัฒนาเว็บไซต์ด้วยเทคโนโลยีสมัยใหม่ โดยเน้นการออกแบบระบบจัดการห้องสมุดออนไลน์',
                    available: true
                },
                {
                    id: 2,
                    title: 'พระราชพงศาวดารกรุงศรีอยุธยา',
                    category: 'พงศาวดาร',
                    author: 'วิไลลักษณ์ สุวรรณ',
                    year: 2020,
                    description: 'บันทึกประวัติศาสตร์กรุงศรีอยุธยาอย่างละเอียด ตั้งแต่สมัยพระเจ้าอู่ทองจนถึงการเสียกรุงครั้งที่ 2',
                    available: true
                },
                {
                    id: 3,
                    title: 'พงศาวดารสุโขทัย',
                    category: 'พงศาวดาร',
                    author: 'ธีรพงศ์ ณ เชียงใหม่',
                    year: 2019,
                    description: 'ประวัติศาสตร์อาณาจักรสุโขทัย ศิลปวัฒนธรรม และความเจริญรุ่งเรืองในสมัยพ่อขุนรามคำแหง',
                    available: false
                },
                {
                    id: 4,
                    title: 'จิตวิทยาการเรียนรู้',
                    category: 'จิตวิทยา',
                    author: 'ดร.วิชัย พัฒนะ',
                    year: 2021,
                    description: 'จิตวิทยาที่เกี่ยวข้องกับกระบวนการเรียนรู้ การพัฒนาทักษะ และเทคนิคการเรียนอย่างมีประสิทธิภาพ',
                    available: true
                },
                {
                    id: 5,
                    title: 'การวิจัยเชิงคุณภาพ',
                    category: 'วิทยานิพนธ์วิจัย',
                    author: 'รศ.ดร.สุนีย์ วัฒนา',
                    year: 2022,
                    description: 'หนังสือเกี่ยวกับวิธีการวิจัยเชิงคุณภาพ ตั้งแต่การออกแบบงานวิจัยจนถึงการวิเคราะห์ข้อมูล',
                    available: true
                },
                {
                    id: 6,
                    title: 'จิตวิทยาสังคม',
                    category: 'จิต心理学',
                    author: 'อ.ธนาพร ศรีสุข',
                    year: 2020,
                    description: 'จิตวิทยาที่เกี่ยวข้องกับพฤติกรรมมนุษย์ในสังคม การมีปฏิสัมพันธ์ และการปรับตัว',
                    available: true
                }
            ];
            
            if (!localStorage.getItem('books_initialized')) {
                localStorage.setItem('books', JSON.stringify(sampleBooks));
                localStorage.setItem('books_initialized', 'true');
            }
            <?php endif; ?>
            
            // เพิ่มเอฟเฟกต์ให้กับการ์ดหนังสือ
            const bookCards = document.querySelectorAll('.book-card');
            bookCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
                
                // คลิกที่การ์ด (ยกเว้นปุ่ม)
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('.btn-borrow') && !e.target.closest('.btn-detail')) {
                        const bookId = this.getAttribute('data-book-id');
                        const books = <?php echo json_encode($_SESSION['books']); ?>;
                        const book = books.find(b => b.id == bookId);
                        if (book) {
                            showBookDetail(book);
                        }
                    }
                });
            });
            
            // ตรวจสอบสถานะการยืม
            checkBorrowStatus();
        });
        
        function checkBorrowStatus() {
            // ดึงข้อมูลการยืมจากเซสชัน
            const transactions = <?php echo json_encode($_SESSION['transactions'] ?? []); ?>;
            const userId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
            
            // ตรวจสอบว่าผู้ใช้ยืมหนังสือเกินจำนวนหรือไม่
            let borrowedCount = 0;
            transactions.forEach(transaction => {
                if (transaction.user_id == userId && transaction.status == 'borrowed') {
                    borrowedCount++;
                }
            });
            
            // ถ้ายืมเกิน 3 เล่ม แจ้งเตือน
            if (borrowedCount >= 3) {
                const borrowButtons = document.querySelectorAll('.btn-borrow:not(.btn-disabled)');
                borrowButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        alert('คุณยืมหนังสือครบจำนวนสูงสุด (3 เล่ม) แล้ว กรุณาคืนหนังสือก่อนยืมเล่มใหม่');
                    });
                });
            }
        }
    </script>
</body>
</html>