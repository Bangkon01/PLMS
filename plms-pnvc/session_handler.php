<?php
// session_handler.php
class PLMSSessionHandler {
    
    public function __construct() {
        if (!isset($_SESSION['initialized'])) {
            $this->initializeSystem();
        }
    }
    
    /**
     * เริ่มต้นระบบและสร้างข้อมูลเริ่มต้น
     */
    private function initializeSystem() {
        // ข้อมูลประเภทหนังสือ
        $_SESSION['categories'] = [
            ['id' => 1, 'name' => 'วิทยานิพนธ์วิจัย', 'description' => 'วิทยานิพนธ์และงานวิจัยทางวิชาการ'],
            ['id' => 2, 'name' => 'พงศาวดาร', 'description' => 'หนังสือประวัติศาสตร์และพงศาวดาร'],
            ['id' => 3, 'name' => 'จิตวิทยา', 'description' => 'หนังสือจิตวิทยาและการพัฒนาตนเอง'],
            ['id' => 4, 'name' => 'วิทยาศาสตร์', 'description' => 'หนังสือวิทยาศาสตร์ทั่วไป'],
            ['id' => 5, 'name' => 'เทคโนโลยี', 'description' => 'หนังสือเทคโนโลยีและคอมพิวเตอร์'],
            ['id' => 6, 'name' => 'วรรณกรรม', 'description' => 'นวนิยายและงานวรรณกรรม']
        ];
        
        // ข้อมูลหนังสือ (ลดจำนวนเพื่อให้โค้ดสั้นลง)
        $_SESSION['books'] = [
            [
                'id' => 1,
                'title' => 'วิทยานิพนธ์เรื่องการพัฒนาเว็บไซต์',
                'category_id' => 1,
                'author' => 'สมชาย ใจดี',
                'year' => 2023,
                'isbn' => '978-616-123-456-1',
                'publisher' => 'สำนักพิมพ์มหาวิทยาลัย',
                'pages' => 150,
                'description' => 'วิทยานิพนธ์เกี่ยวกับการพัฒนาเว็บไซต์ด้วยเทคโนโลยีสมัยใหม่',
                'location' => 'ชั้น 1 แถว A',
                'quantity' => 3,
                'available' => 3,
                'created_at' => '2023-01-15'
            ],
            [
                'id' => 2,
                'title' => 'ประวัติศาสตร์สุโขทัย',
                'category_id' => 2,
                'author' => 'วิไลลักษณ์ สุวรรณ',
                'year' => 2020,
                'isbn' => '978-616-123-456-2',
                'publisher' => 'สำนักพิมพ์ประวัติศาสตร์',
                'pages' => 200,
                'description' => 'บันทึกประวัติศาสตร์ราชอาณาจักรสุโขทัยอย่างละเอียด',
                'location' => 'ชั้น 2 แถว B',
                'quantity' => 2,
                'available' => 2,
                'created_at' => '2023-02-10'
            ],
            [
                'id' => 3,
                'title' => 'จิตวิทยาการเรียนรู้',
                'category_id' => 3,
                'author' => 'ดร.วิชัย พัฒนะ',
                'year' => 2021,
                'isbn' => '978-616-123-456-3',
                'publisher' => 'สำนักพิมพ์จิตวิทยา',
                'pages' => 180,
                'description' => 'หนังสือจิตวิทยาเกี่ยวกับกระบวนการเรียนรู้ของมนุษย์',
                'location' => 'ชั้น 3 แถว C',
                'quantity' => 4,
                'available' => 3,
                'created_at' => '2023-03-05'
            ]
        ];
        
        // ข้อมูลผู้ใช้
        $_SESSION['users'] = [
            // Admin
            [
                'id' => 1,
                'username' => 'admin',
                'password' => '123456',
                'name' => 'ผู้ดูแลระบบ',
                'email' => 'admin@plms.local',
                'role' => 'admin',
                'created_at' => '2023-01-01'
            ],
            // Students
            [
                'id' => 2,
                'username' => 'student1',
                'password' => '123456',
                'name' => 'สมชาย เรียนดี',
                'email' => 'student1@college.ac.th',
                'role' => 'student',
                'student_id' => 'ST001',
                'year' => 3,
                'major' => 'วิศวกรรมคอมพิวเตอร์',
                'phone' => '081-111-1111',
                'created_at' => '2023-01-02'
            ],
            // Teachers
            [
                'id' => 3,
                'username' => 'teacher1',
                'password' => '123456',
                'name' => 'ดร.สมศรี สอนดี',
                'email' => 'teacher1@college.ac.th',
                'role' => 'teacher',
                'teacher_id' => 'TC001',
                'department' => 'คณะวิศวกรรมศาสตร์',
                'position' => 'ผู้ช่วยศาสตราจารย์',
                'phone' => '082-222-2222',
                'created_at' => '2023-01-03'
            ]
        ];
        
        // ข้อมูลการยืม
        $_SESSION['transactions'] = [
            [
                'id' => 'TR0001000',
                'book_id' => 1,
                'book_title' => 'วิทยานิพนธ์เรื่องการพัฒนาเว็บไซต์',
                'user_id' => 2,
                'user_name' => 'สมชาย เรียนดี',
                'user_role' => 'student',
                'borrow_date' => '2023-10-01',
                'expected_return_date' => '2023-10-08',
                'actual_return_date' => null,
                'status' => 'borrowed',
                'late_fee' => 0,
                'created_at' => '2023-10-01'
            ]
        ];
        
        // ตั้งค่าตัวแปรระบบ
        $_SESSION['initialized'] = true;
        $_SESSION['transaction_id_counter'] = 1001;
        $_SESSION['system_settings'] = [
            'library_name' => 'วิทยาลัยเทคนิคแห่งหนึ่ง',
            'max_borrow_days' => 7,
            'max_books_per_user' => 5,
            'late_fee_per_day' => 10,
            'opening_time' => '08:30',
            'closing_time' => '16:30'
        ];
    }
    
    // ==================== FUNCTIONS FOR ALL USERS ====================
    
    /**
     * ดึงข้อมูลผู้ใช้ทั้งหมด
     */
    public function getAllUsers() {
        return $_SESSION['users'] ?? [];
    }
    
    /**
     * ดึงข้อมูลผู้ใช้ตาม ID
     */
    public function getUserById($id) {
        foreach ($_SESSION['users'] as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * ดึงข้อมูลผู้ใช้ตาม Username
     */
    public function getUserByUsername($username) {
        foreach ($_SESSION['users'] as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * ดึงข้อมูลผู้ใช้ตาม Role
     */
    public function getUsersByRole($role) {
        return array_filter($_SESSION['users'], function($user) use ($role) {
            return $user['role'] === $role;
        });
    }
    
    /**
     * ดึงข้อมูลหนังสือทั้งหมด
     */
    public function getAllBooks() {
        return $_SESSION['books'] ?? [];
    }
    
    /**
     * ดึงข้อมูลหนังสือตาม ID
     */
    public function getBookById($id) {
        foreach ($_SESSION['books'] as $book) {
            if ($book['id'] == $id) {
                return $book;
            }
        }
        return null;
    }
    
    /**
     * ดึงข้อมูลประเภทหนังสือทั้งหมด
     */
    public function getAllCategories() {
        return $_SESSION['categories'] ?? [];
    }
    
    /**
     * ดึงข้อมูลประเภทหนังสือตาม ID
     */
    public function getCategoryById($id) {
        foreach ($_SESSION['categories'] as $category) {
            if ($category['id'] == $id) {
                return $category;
            }
        }
        return null;
    }
    
    /**
     * ดึงข้อมูลการยืมทั้งหมด
     */
    public function getAllTransactions() {
        return $_SESSION['transactions'] ?? [];
    }
    
    /**
     * ดึงหนังสือที่พร้อมให้ยืม
     */
    public function getAvailableBooks() {
        return array_filter($_SESSION['books'], function($book) {
            return $book['available'] > 0;
        });
    }
    
    /**
     * ค้นหาหนังสือ
     */
    public function searchBooks($keyword, $category_id = null) {
        $results = $_SESSION['books'];
        
        if ($keyword) {
            $results = array_filter($results, function($book) use ($keyword) {
                return stripos($book['title'], $keyword) !== false || 
                       stripos($book['author'], $keyword) !== false ||
                       stripos($book['description'], $keyword) !== false;
            });
        }
        
        if ($category_id) {
            $results = array_filter($results, function($book) use ($category_id) {
                return $book['category_id'] == $category_id;
            });
        }
        
        return array_values($results);
    }
    
    // ==================== BORROW/RETURN FUNCTIONS ====================
    
    /**
     * ยืมหนังสือ
     */
    public function borrowBook($book_id, $user_id, $borrow_date, $expected_return_date) {
        $book = $this->getBookById($book_id);
        $user = $this->getUserById($user_id);
        
        if (!$book) {
            return ['success' => false, 'message' => 'ไม่พบหนังสือ'];
        }
        
        if (!$user) {
            return ['success' => false, 'message' => 'ไม่พบผู้ใช้'];
        }
        
        if ($book['available'] <= 0) {
            return ['success' => false, 'message' => 'หนังสือหมด'];
        }
        
        // ตรวจสอบว่าผู้ใช้ยืมเกินจำนวนหรือไม่
        $user_borrowed = 0;
        foreach ($_SESSION['transactions'] as $transaction) {
            if ($transaction['user_id'] == $user_id && $transaction['status'] == 'borrowed') {
                $user_borrowed++;
            }
        }
        
        if ($user_borrowed >= $_SESSION['system_settings']['max_books_per_user']) {
            return ['success' => false, 'message' => 'ยืมหนังสือครบจำนวนสูงสุดแล้ว'];
        }
        
        // สร้าง transaction ID
        $transaction_id = 'TR' . str_pad($_SESSION['transaction_id_counter'], 7, '0', STR_PAD_LEFT);
        $_SESSION['transaction_id_counter']++;
        
        // สร้างรายการยืม
        $transaction = [
            'id' => $transaction_id,
            'book_id' => $book_id,
            'book_title' => $book['title'],
            'user_id' => $user_id,
            'user_name' => $user['name'],
            'user_role' => $user['role'],
            'borrow_date' => $borrow_date,
            'expected_return_date' => $expected_return_date,
            'actual_return_date' => null,
            'status' => 'borrowed',
            'late_fee' => 0,
            'created_at' => date('Y-m-d')
        ];
        
        $_SESSION['transactions'][] = $transaction;
        
        // ลดจำนวนหนังสือที่ว่าง
        foreach ($_SESSION['books'] as &$b) {
            if ($b['id'] == $book_id) {
                $b['available']--;
                break;
            }
        }
        
        return [
            'success' => true, 
            'transaction_id' => $transaction_id,
            'message' => 'ยืมหนังสือสำเร็จ'
        ];
    }
    
    /**
     * คืนหนังสือ
     */
    public function returnBook($transaction_id, $return_date) {
        foreach ($_SESSION['transactions'] as &$transaction) {
            if ($transaction['id'] == $transaction_id && $transaction['status'] == 'borrowed') {
                // คำนวณค่าปรับ
                $expected = new DateTime($transaction['expected_return_date']);
                $actual = new DateTime($return_date);
                $late_fee = 0;
                
                if ($actual > $expected) {
                    $days_late = $expected->diff($actual)->days;
                    $late_fee = $days_late * $_SESSION['system_settings']['late_fee_per_day'];
                }
                
                // อัพเดทข้อมูล
                $transaction['actual_return_date'] = $return_date;
                $transaction['status'] = 'returned';
                $transaction['late_fee'] = $late_fee;
                
                // เพิ่มจำนวนหนังสือที่ว่าง
                foreach ($_SESSION['books'] as &$book) {
                    if ($book['id'] == $transaction['book_id']) {
                        $book['available']++;
                        break;
                    }
                }
                
                return [
                    'success' => true,
                    'late_fee' => $late_fee,
                    'message' => 'คืนหนังสือสำเร็จ'
                ];
            }
        }
        
        return ['success' => false, 'message' => 'ไม่พบรายการยืม'];
    }
    
    /**
     * ดึงประวัติการยืมตามผู้ใช้
     */
    public function getBorrowHistory($user_id = null) {
        $transactions = $_SESSION['transactions'];
        
        if ($user_id) {
            $transactions = array_filter($transactions, function($transaction) use ($user_id) {
                return $transaction['user_id'] == $user_id;
            });
        }
        
        // เรียงลำดับตามวันที่ยืมล่าสุด
        usort($transactions, function($a, $b) {
            return strtotime($b['borrow_date']) - strtotime($a['borrow_date']);
        });
        
        return array_values($transactions);
    }
    
    // ==================== ADMIN FUNCTIONS ====================
    
    /**
     * เพิ่มนักศึกษาใหม่
     */
    public function addStudent($data) {
        // ตรวจสอบ username ซ้ำ
        foreach ($_SESSION['users'] as $user) {
            if ($user['username'] == $data['username']) {
                return ['success' => false, 'message' => 'ชื่อผู้ใช้นี้มีอยู่แล้ว'];
            }
        }
        
        $maxId = 0;
        foreach ($_SESSION['users'] as $user) {
            if ($user['id'] > $maxId) $maxId = $user['id'];
        }
        
        $newStudent = [
            'id' => $maxId + 1,
            'username' => $data['username'],
            'password' => $data['password'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'student',
            'student_id' => $data['student_id'],
            'year' => $data['year'] ?? 1,
            'major' => $data['major'] ?? '',
            'phone' => $data['phone'] ?? '',
            'created_at' => date('Y-m-d')
        ];
        
        $_SESSION['users'][] = $newStudent;
        return ['success' => true, 'data' => $newStudent];
    }
    
    /**
     * เพิ่มครูใหม่
     */
    public function addTeacher($data) {
    // ตรวจสอบว่ามี username อยู่แล้วหรือไม่
    foreach ($_SESSION['users'] as $user) {
        if ($user['username'] === $data['username']) {
            return ['success' => false, 'message' => 'ชื่อผู้ใช้นี้มีอยู่แล้ว'];
        }
    }
    
    $maxId = 0;
    foreach ($_SESSION['users'] as $user) {
        if ($user['id'] > $maxId) $maxId = $user['id'];
    }
    
    $newTeacher = [
        'id' => $maxId + 1,
        'username' => $data['username'],
        'password' => $data['password'],
        'name' => $data['name'],
        'email' => $data['email'],
        'role' => 'teacher',
        'teacher_id' => $data['teacher_id'],
        'department' => $data['department'] ?? '',
        'position' => $data['position'] ?? 'ครู',
        'phone' => $data['phone'] ?? '',
        'created_at' => date('Y-m-d')
    ];
    
    $_SESSION['users'][] = $newTeacher;
    return ['success' => true, 'data' => $newTeacher];
}
    
    /**
     * เพิ่มบุคลากรใหม่
     */
    public function addStaff($data) {
    // ตรวจสอบว่ามี username อยู่แล้วหรือไม่
    foreach ($_SESSION['users'] as $user) {
        if ($user['username'] === $data['username']) {
            return ['success' => false, 'message' => 'ชื่อผู้ใช้นี้มีอยู่แล้ว'];
        }
    }
    
    $maxId = 0;
    foreach ($_SESSION['users'] as $user) {
        if ($user['id'] > $maxId) $maxId = $user['id'];
    }
    
    $newStaff = [
        'id' => $maxId + 1,
        'username' => $data['username'],
        'password' => $data['password'],
        'name' => $data['name'],
        'email' => $data['email'],
        'role' => 'staff',
        'staff_id' => $data['staff_id'],
        'department' => $data['department'] ?? '',
        'position' => $data['position'] ?? 'เจ้าหน้าที่',
        'phone' => $data['phone'] ?? '',
        'created_at' => date('Y-m-d')
    ];
    
    $_SESSION['users'][] = $newStaff;
    return ['success' => true, 'data' => $newStaff];
}
    
    /**
     * เพิ่มหนังสือใหม่
     */
    public function addBook($data) {
        $maxId = 0;
        foreach ($_SESSION['books'] as $book) {
            if ($book['id'] > $maxId) $maxId = $book['id'];
        }
        
        $newBook = [
            'id' => $maxId + 1,
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'author' => $data['author'],
            'year' => $data['year'],
            'isbn' => $data['isbn'] ?? '',
            'publisher' => $data['publisher'] ?? '',
            'pages' => $data['pages'] ?? 0,
            'description' => $data['description'] ?? '',
            'location' => $data['location'] ?? '',
            'quantity' => $data['quantity'] ?? 1,
            'available' => $data['quantity'] ?? 1,
            'created_at' => date('Y-m-d')
        ];
        
        $_SESSION['books'][] = $newBook;
        return ['success' => true, 'data' => $newBook];
    }
    
    /**
     * เพิ่มประเภทหนังสือใหม่
     */
    public function addCategory($data) {
        $maxId = 0;
        foreach ($_SESSION['categories'] as $category) {
            if ($category['id'] > $maxId) $maxId = $category['id'];
        }
        
        $newCategory = [
            'id' => $maxId + 1,
            'name' => $data['name'],
            'description' => $data['description'] ?? ''
        ];
        
        $_SESSION['categories'][] = $newCategory;
        return ['success' => true, 'data' => $newCategory];
    }
    
    /**
     * แก้ไขข้อมูลผู้ใช้
     */
    public function updateUser($id, $data) {
    foreach ($_SESSION['users'] as &$user) {
        if ($user['id'] == $id) {
            $user['name'] = $data['name'] ?? $user['name'];
            $user['email'] = $data['email'] ?? $user['email'];
            $user['phone'] = $data['phone'] ?? $user['phone'];
            
            if (isset($data['password']) && !empty($data['password'])) {
                $user['password'] = $data['password'];
            }
            
            if ($user['role'] == 'teacher') {
                $user['teacher_id'] = $data['teacher_id'] ?? $user['teacher_id'];
                $user['department'] = $data['department'] ?? $user['department'];
                $user['position'] = $data['position'] ?? $user['position'];
            } elseif ($user['role'] == 'staff') {
                $user['staff_id'] = $data['staff_id'] ?? $user['staff_id'];
                $user['department'] = $data['department'] ?? $user['department'];
                $user['position'] = $data['position'] ?? $user['position'];
            }
            
            return true;
        }
    }
    return false;
}
    
    /**
     * แก้ไขข้อมูลหนังสือ
     */
    public function updateBook($id, $data) {
        foreach ($_SESSION['books'] as &$book) {
            if ($book['id'] == $id) {
                $book['title'] = $data['title'] ?? $book['title'];
                $book['category_id'] = $data['category_id'] ?? $book['category_id'];
                $book['author'] = $data['author'] ?? $book['author'];
                $book['year'] = $data['year'] ?? $book['year'];
                $book['isbn'] = $data['isbn'] ?? $book['isbn'];
                $book['publisher'] = $data['publisher'] ?? $book['publisher'];
                $book['pages'] = $data['pages'] ?? $book['pages'];
                $book['description'] = $data['description'] ?? $book['description'];
                $book['location'] = $data['location'] ?? $book['location'];
                
                if (isset($data['quantity'])) {
                    $difference = $data['quantity'] - $book['quantity'];
                    $book['quantity'] = $data['quantity'];
                    $book['available'] = max(0, $book['available'] + $difference);
                }
                
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'ไม่พบหนังสือ'];
    }
    
    /**
     * แก้ไขประเภทหนังสือ
     */
    public function updateCategory($id, $data) {
        foreach ($_SESSION['categories'] as &$category) {
            if ($category['id'] == $id) {
                $category['name'] = $data['name'] ?? $category['name'];
                $category['description'] = $data['description'] ?? $category['description'];
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'ไม่พบประเภทหนังสือ'];
    }
    
    /**
     * ลบข้อมูลผู้ใช้
     */
    public function deleteUser($id) {
    foreach ($_SESSION['users'] as $key => $user) {
        if ($user['id'] == $id && in_array($user['role'], ['teacher', 'staff'])) {
            // ตรวจสอบว่ามีการยืมหนังสือค้างอยู่หรือไม่
            $hasActiveBorrow = false;
            foreach ($_SESSION['transactions'] as $transaction) {
                if ($transaction['user_id'] == $id && $transaction['status'] == 'borrowed') {
                    $hasActiveBorrow = true;
                    break;
                }
            }
            
            if ($hasActiveBorrow) {
                return ['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีหนังสือที่ยังไม่ได้คืน'];
            }
            
            unset($_SESSION['users'][$key]);
            $_SESSION['users'] = array_values($_SESSION['users']);
            return ['success' => true, 'message' => 'ลบข้อมูลสำเร็จ'];
        }
    }
    return ['success' => false, 'message' => 'ไม่พบข้อมูล'];
}
    
    /**
     * ลบหนังสือ
     */
    public function deleteBook($id) {
        foreach ($_SESSION['books'] as $key => $book) {
            if ($book['id'] == $id) {
                // ตรวจสอบว่ามีการยืมค้างหรือไม่
                foreach ($_SESSION['transactions'] as $transaction) {
                    if ($transaction['book_id'] == $id && $transaction['status'] == 'borrowed') {
                        return ['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากหนังสือถูกยืมอยู่'];
                    }
                }
                
                unset($_SESSION['books'][$key]);
                $_SESSION['books'] = array_values($_SESSION['books']);
                return ['success' => true, 'message' => 'ลบหนังสือสำเร็จ'];
            }
        }
        return ['success' => false, 'message' => 'ไม่พบหนังสือ'];
    }
    
    /**
     * ลบประเภทหนังสือ
     */
    public function deleteCategory($id) {
        // ตรวจสอบว่ามีหนังสือใช้ประเภทนี้หรือไม่
        foreach ($_SESSION['books'] as $book) {
            if ($book['category_id'] == $id) {
                return ['success' => false, 'message' => 'ไม่สามารถลบได้ เนื่องจากมีหนังสือที่ใช้ประเภทนี้อยู่'];
            }
        }
        
        foreach ($_SESSION['categories'] as $key => $category) {
            if ($category['id'] == $id) {
                unset($_SESSION['categories'][$key]);
                $_SESSION['categories'] = array_values($_SESSION['categories']);
                return ['success' => true, 'message' => 'ลบประเภทหนังสือสำเร็จ'];
            }
        }
        return ['success' => false, 'message' => 'ไม่พบประเภทหนังสือ'];
    }
    
    /**
     * สรุปรายงานระบบ
     */
    public function getSummaryReport() {
    $books = $_SESSION['books'] ?? [];
    $users = $_SESSION['users'] ?? [];
    $transactions = $_SESSION['transactions'] ?? [];
    $categories = $_SESSION['categories'] ?? [];

    $totalBooks = 0;
    $availableBooks = 0;

    foreach ($books as $book) {
        $quantity = $book['quantity'] ?? 0;
        $available = $book['available'] ?? 0;

        $totalBooks += $quantity;
        $availableBooks += $available;
    }

    $totalUsers = max(0, count($users) - 1); // ไม่นับ admin

    $activeTransactions = array_filter($transactions, function($transaction) {
        return ($transaction['status'] ?? '') === 'borrowed';
    });

    $totalLateFee = 0;
    foreach ($transactions as $transaction) {
        $totalLateFee += $transaction['late_fee'] ?? 0;
    }

    return [
        'total_books' => $totalBooks,
        'available_books' => $availableBooks,
        'borrowed_books' => max(0, $totalBooks - $availableBooks),
        'total_users' => $totalUsers,
        'total_transactions' => count($transactions),
        'active_transactions' => count($activeTransactions),
        'total_late_fee' => $totalLateFee,
        'categories_count' => count($categories)
    ];
}

    
    /**
     * สถิติรายวัน
     */
    public function getDailyStats($date = null) {
        if (!$date) $date = date('Y-m-d');
        
        $borrowed = 0;
        $returned = 0;
        
        foreach ($_SESSION['transactions'] as $transaction) {
            if (substr($transaction['borrow_date'], 0, 10) == $date) {
                $borrowed++;
            }
            if ($transaction['actual_return_date'] && substr($transaction['actual_return_date'], 0, 10) == $date) {
                $returned++;
            }
        }
        
        return [
            'date' => $date,
            'borrowed' => $borrowed,
            'returned' => $returned,
            'total' => $borrowed + $returned
        ];
    }
    
}
?>