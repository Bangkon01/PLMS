<?php
// includes/auth.php
class Auth {
    
    /**
     * ตรวจสอบว่ามีการล็อกอินหรือไม่
     */
    public static function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
            header('Location: ../login.php');
            exit();
        }
    }
    
    /**
     * ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่
     */
    public static function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * ตรวจสอบว่าผู้ใช้เป็น Teacher หรือไม่
     */
    public static function isTeacher() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher';
    }
    
    /**
     * ตรวจสอบว่าผู้ใช้เป็น Student หรือไม่
     */
    public static function isStudent() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student';
    }
    
    /**
     * ตรวจสอบว่าผู้ใช้เป็น Staff หรือไม่
     */
    public static function isStaff() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'staff';
    }
    
    /**
     * ตรวจสอบสิทธิ์การเข้าถึงหน้า Admin
     */
    public static function requireAdmin() {
        self::checkLogin();
        
        if (!self::isAdmin()) {
            $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้';
            header('Location: ../index.php');
            exit();
        }
    }
    
    /**
     * ตรวจสอบสิทธิ์การเข้าถึงตาม role ที่กำหนด
     */
    public static function requireRole($roles) {
        self::checkLogin();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        $hasRole = false;
        foreach ($roles as $role) {
            switch ($role) {
                case 'admin':
                    if (self::isAdmin()) $hasRole = true;
                    break;
                case 'teacher':
                    if (self::isTeacher()) $hasRole = true;
                    break;
                case 'student':
                    if (self::isStudent()) $hasRole = true;
                    break;
                case 'staff':
                    if (self::isStaff()) $hasRole = true;
                    break;
                case 'user':
                    if (self::isStudent() || self::isTeacher() || self::isStaff()) $hasRole = true;
                    break;
            }
        }
        
        if (!$hasRole) {
            $_SESSION['error'] = 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้';
            header('Location: ../index.php');
            exit();
        }
    }
    
    /**
     * ดึงข้อมูลผู้ใช้ปัจจุบัน
     */
    public static function user() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
            'student_id' => $_SESSION['student_id'] ?? null,
            'phone' => $_SESSION['phone'] ?? null
        ];
    }
    
    /**
     * ล็อกอิน
     */
    public static function login($username, $password) {
        global $sessionHandler;
        
        foreach ($sessionHandler->getAllUsers() as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                // บันทึกข้อมูลผู้ใช้ในเซสชัน
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                if (isset($user['student_id'])) {
                    $_SESSION['student_id'] = $user['student_id'];
                }
                if (isset($user['teacher_id'])) {
                    $_SESSION['teacher_id'] = $user['teacher_id'];
                }
                if (isset($user['staff_id'])) {
                    $_SESSION['staff_id'] = $user['staff_id'];
                }
                if (isset($user['phone'])) {
                    $_SESSION['phone'] = $user['phone'];
                }
                
                // บันทึกเวลาล็อกอิน
                $_SESSION['login_time'] = date('Y-m-d H:i:s');
                
                return true;
            }
        }
        return false;
    }
    
    /**
     * ออกจากระบบ
     */
    public static function logout() {
        // บันทึกเวลาออกจากระบบ
        if (isset($_SESSION['login_time'])) {
            $_SESSION['last_logout'] = date('Y-m-d H:i:s');
        }
        
        // ลบข้อมูลผู้ใช้จากเซสชัน
        session_unset();
        session_destroy();
        
        return true;
    }
}
?>