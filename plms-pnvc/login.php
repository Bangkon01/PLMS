<?php
// login.php
require_once 'config.php';

$error = '';

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // แก้ไข: เช็คว่าจำเป็นต้องมี key ก่อน trim
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        try {
            // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && isset($user['password'])) {
                // ตรวจสอบรหัสผ่าน - เช็คว่า password key มีอยู่ก่อน
                if (password_verify($password, $user['password'])) {
                    // บันทึกข้อมูลผู้ใช้ในเซสชัน
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_name'] = $user['NAME'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // บันทึกข้อมูลเพิ่มเติมตาม role
                    if ($user['role'] == 'student' && !empty($user['student_id'])) {
                        $_SESSION['student_id'] = $user['student_id'];
                    } elseif ($user['role'] == 'teacher' && !empty($user['teacher_id'])) {
                        $_SESSION['teacher_id'] = $user['teacher_id'];
                    } elseif ($user['role'] == 'staff' && !empty($user['staff_id'])) {
                        $_SESSION['staff_id'] = $user['staff_id'];
                    }
                    
                    // บันทึกเวลาล็อกอิน
                    $_SESSION['login_time'] = date('Y-m-d H:i:s');
                    
                    // ตรวจสอบการ redirect
                    if (isset($_SESSION['login_redirect'])) {
                        $redirect_url = $_SESSION['login_redirect'];
                        unset($_SESSION['login_redirect']);
                        redirect($redirect_url);
                    } else {
                        // ตรวจสอบว่าเป็น admin หรือไม่
                        if ($user['role'] === 'admin') {
                            redirect('admin/dashboard.php');
                        } else {
                            redirect('index.php');
                        }
                    }
                } else {
                    $error = 'รหัสผ่านไม่ถูกต้อง';
                }
            } else {
                $error = 'ไม่พบชื่อผู้ใช้นี้ในระบบ';
            }
            
        } catch (PDOException $e) {
            $error = 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล';
            if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
                $error .= ': ' . $e->getMessage();
            }
        }
    }
}

// ตรวจสอบว่ามีการ redirect หรือไม่
$redirect_message = '';
if (isset($_SESSION['login_redirect'])) {
    $redirect_message = 'กรุณาล็อกอินเพื่อเข้าถึงหน้านี้';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - PLMS-SYSTEM</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .logo h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
        
        .info-message {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #bee5eb;
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
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn-login {
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 600;
        }
        
        .btn-login:hover {
            background-color: #2980b9;
        }
        
        .test-accounts {
            margin-top: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
        }
        
        .test-accounts h4 {
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        
        .test-accounts p {
            margin-bottom: 8px;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-book-open"></i>
            <h1>PLMS-SYSTEM</h1>
            <p>ระบบจัดการยืม-คืนหนังสือ</p>
        </div>
        
        <?php if ($redirect_message): ?>
            <div class="info-message">
                <i class="fas fa-info-circle"></i> <?php echo $redirect_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required 
                       placeholder="กรุณากรอกชื่อผู้ใช้" value="<?php echo isset($_POST['username']) ? escape($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> รหัสผ่าน</label>
                <input type="password" id="password" name="password" required 
                       placeholder="กรุณากรอกรหัสผ่าน">
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
            </button>
        </form>
        
        <div class="test-accounts">
            <h4><i class="fas fa-key"></i> ข้อมูลล็อกอินสำหรับทดสอบระบบ</h4>
            <p><strong>ผู้ดูแลระบบ:</strong> admin / 123456</p>
            <p><strong>นักศึกษา:</strong> student1 / 123456</p>
            <p><strong>ครู/อาจารย์:</strong> teacher1 / 123456</p>
            <p><strong>บุคลากร:</strong> staff1 / 123456</p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value.trim();
                
                if (!username || !password) {
                    e.preventDefault();
                    alert('กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน');
                    return false;
                }
                return true;
            });
        });
    </script>
</body>
</html>