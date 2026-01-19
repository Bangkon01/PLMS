<?php
// config.php
// เริ่ม session แค่ครั้งเดียว
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตั้งค่าการแสดงข้อผิดพลาด
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // หากมีรหัสผ่านให้ใส่ที่นี่
define('DB_NAME', 'plms_system');
define('DB_CHARSET', 'utf8mb4');

// ตั้งค่าพื้นฐาน
define('SITE_NAME', 'PLMS-SYSTEM');
define('SITE_TITLE', 'ระบบยืม-คืนหนังสือ PLMS');
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));

// ประกาศตัวแปร $db เป็น global
global $db;

// ฟังก์ชันเชื่อมต่อฐานข้อมูล
function getConnection() {
    global $db;
    
    if (!isset($db)) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // แสดงข้อผิดพลาดแบบ user-friendly
            if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
                die("Database connection failed: " . $e->getMessage() . 
                    "<br>Please check: " .
                    "<br>1. Database name: " . DB_NAME .
                    "<br>2. Username: " . DB_USER .
                    "<br>3. Run setup_database.php first");
            } else {
                die("Database connection failed. Please contact administrator.");
            }
        }
    }
    
    return $db;
}

// เชื่อมต่อฐานข้อมูล
$db = getConnection();

// ฟังก์ชันพื้นฐาน
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
}

function flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ฟังก์ชันสำหรับตรวจสอบสิทธิ์
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isTeacher() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher';
}

function isStudent() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student';
}

function isStaff() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff';
}

// ฟังก์ชันสำหรับ URL
function url($path = '') {
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

function asset($path) {
    return url($path);
}

// ฟังก์ชันช่วยเหลือสำหรับฐานข้อมูล
function getAllBooks() {
    global $db;
    try {
        $stmt = $db->query("
            SELECT b.*, c.name as category_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            ORDER BY b.id DESC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getAvailableBooks() {
    global $db;
    try {
        $stmt = $db->query("
            SELECT b.*, c.name as category_name 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            WHERE b.available > 0 
            ORDER BY b.title
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getUserById($id) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function getUserByUsername($username) {
    global $db;
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function getAllTransactions($user_id = null) {
    global $db;
    try {
        $sql = "SELECT t.*, b.title as book_title, u.name as user_name, u.role as user_role 
                FROM transactions t
                JOIN books b ON t.book_id = b.id
                JOIN users u ON t.user_id = u.id";
        
        $params = [];
        if ($user_id) {
            $sql .= " WHERE t.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY t.borrow_date DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getActiveTransactions($user_id = null) {
    global $db;
    try {
        $sql = "SELECT t.*, b.title as book_title, u.name as user_name 
                FROM transactions t
                JOIN books b ON t.book_id = b.id
                JOIN users u ON t.user_id = u.id
                WHERE t.status = 'borrowed'";
        
        $params = [];
        if ($user_id) {
            $sql .= " AND t.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY t.expected_return_date";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function getAllCategories() {
    global $db;
    try {
        $stmt = $db->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function searchBooks($keyword = '', $category_id = null) {
    global $db;
    try {
        $sql = "SELECT b.*, c.name as category_name FROM books b LEFT JOIN categories c ON b.category_id = c.id WHERE 1=1";
        $params = [];
        
        if (!empty($keyword)) {
            $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
            $search = "%$keyword%";
            $params = array_merge($params, [$search, $search, $search]);
        }
        
        if ($category_id) {
            $sql .= " AND b.category_id = ?";
            $params[] = $category_id;
        }
        
        $sql .= " ORDER BY b.title";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// ฟังก์ชันสำหรับจัดการข้อผิดพลาดฐานข้อมูล
function handleDatabaseError($e) {
    if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px;'>";
        echo "<h3>Database Error</h3>";
        echo "<p><strong>Message:</strong> " . escape($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . escape($e->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . escape($e->getLine()) . "</p>";
        echo "<p><a href='setup_database.php'>คลิกที่นี่เพื่อตั้งค่าฐานข้อมูลใหม่</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px;'>";
        echo "<p>เกิดข้อผิดพลาดในการประมวลผลข้อมูล กรุณาลองใหม่อีกครั้งในภายหลัง</p>";
        echo "</div>";
    }
}
?>