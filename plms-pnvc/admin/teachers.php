<?php
// admin/teachers.php
require_once '../config.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// ตรวจสอบว่าเป็น admin หรือไม่
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้'); window.location.href = '../index.php';</script>";
    exit();
}

$page_title = 'จัดการข้อมูลครู/อาจารย์';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// ตัวแปรสำหรับข้อความแจ้งเตือน
$success = '';
$error = '';

// ตรวจสอบการกระทำ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // ตรวจสอบ CSRF token ง่ายๆ
    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        $error = 'Token ไม่ถูกต้อง';
    } else {
        switch ($action) {
            case 'add':
                $data = [
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'teacher_id' => $_POST['teacher_id'],
                    'department' => $_POST['department'],
                    'position' => $_POST['position'],
                    'phone' => $_POST['phone'] ?? ''
                ];
                
                // ตรวจสอบว่าชื่อผู้ใช้มีอยู่แล้วหรือไม่ (ตรวจสอบจากเซสชัน)
                $user_exists = false;
                foreach ($_SESSION['users'] as $user) {
                    if ($user['username'] === $data['username']) {
                        $user_exists = true;
                        break;
                    }
                }
                
                if ($user_exists) {
                    $error = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
                } else {
                    // เพิ่มครูใหม่ในเซสชัน
                    $max_id = 0;
                    foreach ($_SESSION['users'] as $user) {
                        if ($user['id'] > $max_id) {
                            $max_id = $user['id'];
                        }
                    }
                    
                    $new_teacher = [
                        'id' => $max_id + 1,
                        'username' => $data['username'],
                        'password' => $data['password'],
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role' => 'teacher',
                        'teacher_id' => $data['teacher_id'],
                        'department' => $data['department'],
                        'position' => $data['position'],
                        'phone' => $data['phone'],
                        'created_at' => date('Y-m-d')
                    ];
                    
                    $_SESSION['users'][] = $new_teacher;
                    $success = 'เพิ่มข้อมูลครู/อาจารย์สำเร็จ';
                }
                break;
                
            case 'edit':
                if (!$id) {
                    $error = 'ไม่พบรหัสครู';
                    break;
                }
                
                $data = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'teacher_id' => $_POST['teacher_id'],
                    'department' => $_POST['department'],
                    'position' => $_POST['position'],
                    'phone' => $_POST['phone'] ?? ''
                ];
                
                // หาครูและอัพเดทข้อมูล
                $found = false;
                foreach ($_SESSION['users'] as &$user) {
                    if ($user['id'] == $id && $user['role'] == 'teacher') {
                        $user['name'] = $data['name'];
                        $user['email'] = $data['email'];
                        $user['teacher_id'] = $data['teacher_id'];
                        $user['department'] = $data['department'];
                        $user['position'] = $data['position'];
                        $user['phone'] = $data['phone'];
                        
                        if (!empty($_POST['password'])) {
                            $user['password'] = $_POST['password'];
                        }
                        
                        $found = true;
                        $success = 'แก้ไขข้อมูลครู/อาจารย์สำเร็จ';
                        break;
                    }
                }
                
                if (!$found) {
                    $error = 'ไม่พบข้อมูลครู';
                }
                break;
                
            case 'delete':
                if (!$id) {
                    $error = 'ไม่พบรหัสครู';
                    break;
                }
                
                // ตรวจสอบว่ามีการยืมหนังสือค้างหรือไม่
                $has_borrow = false;
                foreach ($_SESSION['transactions'] as $transaction) {
                    if ($transaction['user_id'] == $id && $transaction['status'] == 'borrowed') {
                        $has_borrow = true;
                        break;
                    }
                }
                
                if ($has_borrow) {
                    $error = 'ไม่สามารถลบได้ เนื่องจากมีหนังสือที่ยังไม่ได้คืน';
                } else {
                    // ลบครูออกจากเซสชัน
                    foreach ($_SESSION['users'] as $key => $user) {
                        if ($user['id'] == $id && $user['role'] == 'teacher') {
                            unset($_SESSION['users'][$key]);
                            $_SESSION['users'] = array_values($_SESSION['users']);
                            $success = 'ลบข้อมูลครู/อาจารย์สำเร็จ';
                            break;
                        }
                    }
                }
                break;
        }
    }
}

// ดึงข้อมูลครูทั้งหมด
$teachers = [];
foreach ($_SESSION['users'] as $user) {
    if ($user['role'] == 'teacher') {
        $teachers[] = $user;
    }
}

// ดึงข้อมูลครูที่ต้องการแก้ไข (ถ้ามี)
$teacher = null;
if ($action == 'edit' && $id) {
    foreach ($_SESSION['users'] as $user) {
        if ($user['id'] == $id && $user['role'] == 'teacher') {
            $teacher = $user;
            break;
        }
    }
}

// สร้าง CSRF token ถ้ายังไม่มี
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<?php include 'admin_header.php'; ?>

<?php include 'admin_sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-content">
    <div class="admin-content-header">
        <h1><i class="fas fa-chalkboard-teacher"></i> <?php echo $page_title; ?></h1>
        <div class="breadcrumb">
            <a href="dashboard.php">แดชบอร์ด</a> &raquo; <span>จัดการข้อมูลครู/อาจารย์</span>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        <?php if ($action == 'add' || $action == 'edit'): ?>
            <script>
                setTimeout(function() {
                    window.location.href = 'teachers.php';
                }, 1500);
            </script>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($action == 'list'): ?>
        <!-- รายการครู/อาจารย์ -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>รายชื่อครู/อาจารย์ทั้งหมด (<?php echo count($teachers); ?> คน)</h3>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> เพิ่มครู/อาจารย์ใหม่
                </a>
            </div>
            
            <?php if (count($teachers) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>รหัสครู</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ตำแหน่ง</th>
                            <th>ภาควิชา</th>
                            <th>อีเมล</th>
                            <th>โทรศัพท์</th>
                            <th>วันที่สมัคร</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo $teacher['teacher_id'] ?? '-'; ?></td>
                            <td><?php echo $teacher['name']; ?></td>
                            <td><?php echo $teacher['position'] ?? '-'; ?></td>
                            <td><?php echo $teacher['department'] ?? '-'; ?></td>
                            <td><?php echo $teacher['email']; ?></td>
                            <td><?php echo $teacher['phone'] ?? '-'; ?></td>
                            <td><?php echo $teacher['created_at'] ?? '-'; ?></td>
                            <td class="table-actions">
                                <a href="?action=edit&id=<?php echo $teacher['id']; ?>" 
                                   class="action-btn action-btn-edit">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <a href="?action=delete&id=<?php echo $teacher['id']; ?>" 
                                   class="action-btn action-btn-delete"
                                   onclick="return confirm('คุณแน่ใจที่จะลบครู/อาจารย์คนนี้?')">
                                    <i class="fas fa-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-chalkboard-teacher" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 15px;"></i>
                    <h3>ยังไม่มีข้อมูลครู/อาจารย์</h3>
                    <p>เริ่มต้นด้วยการเพิ่มครู/อาจารย์คนแรก</p>
                    <a href="?action=add" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-user-plus"></i> เพิ่มครู/อาจารย์ใหม่
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
    <?php elseif ($action == 'add' || $action == 'edit'): ?>
        <!-- แบบฟอร์มเพิ่ม/แก้ไขครู/อาจารย์ -->
        <div class="form-container">
            <h2>
                <i class="fas fa-<?php echo $action == 'add' ? 'user-plus' : 'user-edit'; ?>"></i>
                <?php echo $action == 'add' ? 'เพิ่มครู/อาจารย์ใหม่' : 'แก้ไขข้อมูลครู/อาจารย์'; ?>
            </h2>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="teacher_id"><i class="fas fa-id-card"></i> รหัสครู *</label>
                        <input type="text" id="teacher_id" name="teacher_id" 
                               value="<?php echo $teacher['teacher_id'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> ชื่อ-นามสกุล *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo $teacher['name'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user-tag"></i> ชื่อผู้ใช้ *</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo $teacher['username'] ?? ''; ?>" 
                               <?php echo $action == 'edit' ? 'readonly' : 'required'; ?>>
                        <?php if ($action == 'edit'): ?>
                        <small style="color: #6c757d; font-size: 0.9rem;">ไม่สามารถเปลี่ยนชื่อผู้ใช้ได้</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> รหัสผ่าน <?php echo $action == 'add' ? '*' : ''; ?>
                        </label>
                        <input type="password" id="password" name="password" 
                               <?php echo $action == 'add' ? 'required' : ''; ?>>
                        <?php if ($action == 'edit'): ?>
                        <small style="color: #6c757d; font-size: 0.9rem;">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> อีเมล *</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo $teacher['email'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> โทรศัพท์</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo $teacher['phone'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="department"><i class="fas fa-building"></i> ภาควิชา *</label>
                        <input type="text" id="department" name="department" 
                               value="<?php echo $teacher['department'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="position"><i class="fas fa-briefcase"></i> ตำแหน่ง *</label>
                        <select id="position" name="position" required>
                            <option value="">-- เลือกตำแหน่ง --</option>
                            <option value="ครู" <?php echo ($teacher['position'] ?? '') == 'ครู' ? 'selected' : ''; ?>>ครู</option>
                            <option value="อาจารย์" <?php echo ($teacher['position'] ?? '') == 'อาจารย์' ? 'selected' : ''; ?>>อาจารย์</option>
                            <option value="ผู้ช่วยศาสตราจารย์" <?php echo ($teacher['position'] ?? '') == 'ผู้ช่วยศาสตราจารย์' ? 'selected' : ''; ?>>ผู้ช่วยศาสตราจารย์</option>
                            <option value="รองศาสตราจารย์" <?php echo ($teacher['position'] ?? '') == 'รองศาสตราจารย์' ? 'selected' : ''; ?>>รองศาสตราจารย์</option>
                            <option value="ศาสตราจารย์" <?php echo ($teacher['position'] ?? '') == 'ศาสตราจารย์' ? 'selected' : ''; ?>>ศาสตราจารย์</option>
                            <option value="หัวหน้าภาควิชา" <?php echo ($teacher['position'] ?? '') == 'หัวหน้าภาควิชา' ? 'selected' : ''; ?>>หัวหน้าภาควิชา</option>
                            <option value="คณบดี" <?php echo ($teacher['position'] ?? '') == 'คณบดี' ? 'selected' : ''; ?>>คณบดี</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                    <a href="teachers.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ตรวจสอบฟอร์ม
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let valid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        valid = false;
                        field.style.borderColor = '#e74c3c';
                    } else {
                        field.style.borderColor = '#ced4da';
                    }
                });
                
                // ตรวจสอบอีเมล
                const emailField = document.getElementById('email');
                if (emailField && emailField.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailField.value)) {
                        valid = false;
                        emailField.style.borderColor = '#e74c3c';
                        alert('กรุณากรอกอีเมลให้ถูกต้อง');
                    }
                }
                
                if (!valid) {
                    e.preventDefault();
                    alert('กรุณากรอกข้อมูลในช่องที่จำเป็นให้ครบถ้วน');
                }
            });
        }
    });
</script>

</body>
</html>