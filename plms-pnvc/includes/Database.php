<?php
// includes/Database.php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // ทดสอบการเชื่อมต่อ
            $this->pdo->query("SELECT 1");
            
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // ==================== USER FUNCTIONS ====================
    
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getUserByUsername($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getUsersByRole($role) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY id DESC");
            $stmt->execute([$role]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function addUser($data) {
        try {
            $sql = "INSERT INTO users (username, password, name, email, role, student_id, teacher_id, staff_id, year, major, department, position, phone) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['name'],
                $data['email'],
                $data['role'],
                $data['student_id'] ?? null,
                $data['teacher_id'] ?? null,
                $data['staff_id'] ?? null,
                $data['year'] ?? null,
                $data['major'] ?? null,
                $data['department'] ?? null,
                $data['position'] ?? null,
                $data['phone'] ?? null
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function updateUser($id, $data) {
        try {
            // สร้าง SQL query แบบไดนามิก
            $fields = [];
            $params = [];
            
            $updatableFields = [
                'name', 'email', 'phone', 'student_id', 'year', 'major',
                'teacher_id', 'department', 'position', 'staff_id'
            ];
            
            foreach ($updatableFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            // ถ้ามีการเปลี่ยนรหัสผ่าน
            if (!empty($data['password'])) {
                $fields[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $params[] = $id;
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function deleteUser($id) {
        try {
            // ตรวจสอบว่ามีการยืมค้างหรือไม่
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE user_id = ? AND status = 'borrowed'");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีหนังสือที่ยังไม่ได้คืน'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            return [
                'success' => $success, 
                'message' => $success ? 'ลบข้อมูลสำเร็จ' : 'เกิดข้อผิดพลาด'
            ];
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    // ==================== BOOK FUNCTIONS ====================
    
    public function getAllBooks() {
        try {
            $stmt = $this->pdo->query("
                SELECT b.*, c.name as category_name 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                ORDER BY b.id DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getBookById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT b.*, c.name as category_name 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getAvailableBooks() {
        try {
            $stmt = $this->pdo->query("
                SELECT b.*, c.name as category_name 
                FROM books b 
                LEFT JOIN categories c ON b.category_id = c.id 
                WHERE b.available > 0 
                ORDER BY b.title
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function searchBooks($keyword = '', $category_id = null) {
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
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function addBook($data) {
        try {
            $sql = "INSERT INTO books (title, category_id, author, year, isbn, publisher, pages, description, location, quantity, available) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['title'],
                $data['category_id'],
                $data['author'],
                $data['year'],
                $data['isbn'] ?? '',
                $data['publisher'] ?? '',
                $data['pages'] ?? 0,
                $data['description'] ?? '',
                $data['location'] ?? '',
                $data['quantity'] ?? 1,
                $data['quantity'] ?? 1
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function updateBook($id, $data) {
        try {
            // ดึงข้อมูลปัจจุบัน
            $current = $this->getBookById($id);
            
            // คำนวณ available ใหม่
            $new_quantity = $data['quantity'] ?? $current['quantity'];
            $available = $current['available'] + ($new_quantity - $current['quantity']);
            if ($available < 0) $available = 0;
            
            $sql = "UPDATE books SET 
                    title = ?, category_id = ?, author = ?, year = ?, 
                    isbn = ?, publisher = ?, pages = ?, description = ?, 
                    location = ?, quantity = ?, available = ?
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['title'] ?? $current['title'],
                $data['category_id'] ?? $current['category_id'],
                $data['author'] ?? $current['author'],
                $data['year'] ?? $current['year'],
                $data['isbn'] ?? $current['isbn'],
                $data['publisher'] ?? $current['publisher'],
                $data['pages'] ?? $current['pages'],
                $data['description'] ?? $current['description'],
                $data['location'] ?? $current['location'],
                $new_quantity,
                $available,
                $id
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function deleteBook($id) {
        try {
            // ตรวจสอบว่ามีการยืมค้างหรือไม่
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE book_id = ? AND status = 'borrowed'");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากหนังสือถูกยืมอยู่'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            return [
                'success' => $success, 
                'message' => $success ? 'ลบหนังสือสำเร็จ' : 'เกิดข้อผิดพลาด'
            ];
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    // ==================== CATEGORY FUNCTIONS ====================
    
    public function getAllCategories() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getCategoryById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function addCategory($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            return $stmt->execute([$data['name'], $data['description'] ?? '']);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function updateCategory($id, $data) {
        try {
            $stmt = $this->pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            return $stmt->execute([$data['name'], $data['description'] ?? '', $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function deleteCategory($id) {
        try {
            // ตรวจสอบว่ามีหนังสือใช้ประเภทนี้หรือไม่
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM books WHERE category_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีหนังสือที่ใช้ประเภทนี้อยู่'];
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            return [
                'success' => $success, 
                'message' => $success ? 'ลบประเภทหนังสือสำเร็จ' : 'เกิดข้อผิดพลาด'
            ];
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    // ==================== TRANSACTION FUNCTIONS ====================
    
    public function borrowBook($book_id, $user_id, $borrow_date, $expected_return_date) {
        try {
            // เริ่ม transaction
            $this->pdo->beginTransaction();
            
            // ตรวจสอบว่าหนังสือว่างหรือไม่
            $stmt = $this->pdo->prepare("SELECT available FROM books WHERE id = ? FOR UPDATE");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch();
            
            if (!$book || $book['available'] <= 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'หนังสือหมดหรือไม่พบหนังสือ'];
            }
            
            // ตรวจสอบว่าผู้ใช้ยืมเกินจำนวนหรือไม่
            $max_books = $this->getSetting('max_books_per_user') ?? 5;
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE user_id = ? AND status = 'borrowed'");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            
            if ($result['count'] >= $max_books) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => "ยืมหนังสือครบจำนวนสูงสุดแล้ว ($max_books เล่ม)"];
            }
            
            // สร้างรหัสการยืม
            $stmt = $this->pdo->query("SELECT MAX(CAST(SUBSTRING(id, 3) AS UNSIGNED)) as max_id FROM transactions");
            $result = $stmt->fetch();
            $next_id = ($result['max_id'] ?? 1000) + 1;
            $transaction_id = 'TR' . str_pad($next_id, 7, '0', STR_PAD_LEFT);
            
            // เพิ่มรายการยืม
            $sql = "INSERT INTO transactions (id, book_id, user_id, borrow_date, expected_return_date, status) 
                    VALUES (?, ?, ?, ?, ?, 'borrowed')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$transaction_id, $book_id, $user_id, $borrow_date, $expected_return_date]);
            
            // ลดจำนวนหนังสือที่ว่าง
            $stmt = $this->pdo->prepare("UPDATE books SET available = available - 1 WHERE id = ?");
            $stmt->execute([$book_id]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'transaction_id' => $transaction_id,
                'message' => 'ยืมหนังสือสำเร็จ'
            ];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
    
    public function returnBook($transaction_id, $return_date) {
        try {
            // เริ่ม transaction
            $this->pdo->beginTransaction();
            
            // ดึงข้อมูลการยืม
            $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'borrowed' FOR UPDATE");
            $stmt->execute([$transaction_id]);
            $transaction = $stmt->fetch();
            
            if (!$transaction) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'ไม่พบรายการยืมหรือหนังสือถูกคืนแล้ว'];
            }
            
            // คำนวณค่าปรับ
            $expected = new DateTime($transaction['expected_return_date']);
            $actual = new DateTime($return_date);
            $late_fee = 0;
            
            if ($actual > $expected) {
                $days_late = $expected->diff($actual)->days;
                $fee_per_day = $this->getSetting('late_fee_per_day') ?? 10;
                $late_fee = $days_late * $fee_per_day;
            }
            
            // อัพเดทการคืน
            $sql = "UPDATE transactions SET 
                    actual_return_date = ?, status = 'returned', late_fee = ? 
                    WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$return_date, $late_fee, $transaction_id]);
            
            // เพิ่มจำนวนหนังสือที่ว่าง
            $stmt = $this->pdo->prepare("UPDATE books SET available = available + 1 WHERE id = ?");
            $stmt->execute([$transaction['book_id']]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'late_fee' => $late_fee,
                'message' => 'คืนหนังสือสำเร็จ' . ($late_fee > 0 ? " (ค่าปรับ: $late_fee บาท)" : "")
            ];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
    
    public function getAllTransactions($user_id = null) {
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
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getActiveTransactions($user_id = null) {
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
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    // ==================== SETTING FUNCTIONS ====================
    
    public function getSetting($key) {
        try {
            $stmt = $this->pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            return $result['setting_value'] ?? null;
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function getAllSettings() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM settings");
            $settings = $stmt->fetchAll();
            
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting['setting_key']] = $setting['setting_value'];
            }
            
            return $result;
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    // ==================== REPORT FUNCTIONS ====================
    
    public function getSummaryReport() {
        try {
            $report = [];
            
            // จำนวนหนังสือ
            $stmt = $this->pdo->query("SELECT SUM(quantity) as total, SUM(available) as available FROM books");
            $books = $stmt->fetch();
            $report['total_books'] = $books['total'] ?? 0;
            $report['available_books'] = $books['available'] ?? 0;
            $report['borrowed_books'] = $report['total_books'] - $report['available_books'];
            
            // จำนวนผู้ใช้ (ไม่นับ admin)
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users WHERE role != 'admin'");
            $users = $stmt->fetch();
            $report['total_users'] = $users['total'] ?? 0;
            
            // จำนวนการยืม
            $stmt = $this->pdo->query("SELECT COUNT(*) as total, 
                SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as active 
                FROM transactions");
            $trans = $stmt->fetch();
            $report['total_transactions'] = $trans['total'] ?? 0;
            $report['active_transactions'] = $trans['active'] ?? 0;
            
            // ค่าปรับทั้งหมด
            $stmt = $this->pdo->query("SELECT SUM(late_fee) as total FROM transactions");
            $fee = $stmt->fetch();
            $report['total_late_fee'] = $fee['total'] ?? 0;
            
            // จำนวนประเภทหนังสือ
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM categories");
            $cats = $stmt->fetch();
            $report['categories_count'] = $cats['total'] ?? 0;
            
            return $report;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
?>