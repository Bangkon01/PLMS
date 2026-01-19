<?php
// admin/student.php
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

$page_title = 'จัดการข้อมูลนักศึกษา';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// ตัวแปรสำหรับข้อความแจ้งเตือน
$success = '';
$error = '';

// ตรวจสอบการกระทำ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // ตรวจสอบ CSRF token
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
                    'student_id' => $_POST['student_id'],
                    'faculty' => $_POST['faculty'],
                    'major' => $_POST['major'],
                    'year' => $_POST['year'],
                    'phone' => $_POST['phone'] ?? '',
                    'address' => $_POST['address'] ?? ''
                ];
                
                // ตรวจสอบว่าชื่อผู้ใช้มีอยู่แล้วหรือไม่
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
                    // เพิ่มนักศึกษาใหม่ในเซสชัน
                    $max_id = 0;
                    foreach ($_SESSION['users'] as $user) {
                        if ($user['id'] > $max_id) {
                            $max_id = $user['id'];
                        }
                    }
                    
                    $new_student = [
                        'id' => $max_id + 1,
                        'username' => $data['username'],
                        'password' => $data['password'],
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role' => 'student',
                        'student_id' => $data['student_id'],
                        'faculty' => $data['faculty'],
                        'major' => $data['major'],
                        'year' => $data['year'],
                        'phone' => $data['phone'],
                        'address' => $data['address'],
                        'created_at' => date('Y-m-d')
                    ];
                    
                    $_SESSION['users'][] = $new_student;
                    $success = 'เพิ่มข้อมูลนักศึกษาสำเร็จ';
                }
                break;
                
            case 'edit':
                if (!$id) {
                    $error = 'ไม่พบรหัสนักศึกษา';
                    break;
                }
                
                $data = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'student_id' => $_POST['student_id'],
                    'faculty' => $_POST['faculty'],
                    'major' => $_POST['major'],
                    'year' => $_POST['year'],
                    'phone' => $_POST['phone'] ?? '',
                    'address' => $_POST['address'] ?? ''
                ];
                
                // หานักศึกษาและอัพเดทข้อมูล
                $found = false;
                foreach ($_SESSION['users'] as &$user) {
                    if ($user['id'] == $id && $user['role'] == 'student') {
                        $user['name'] = $data['name'];
                        $user['email'] = $data['email'];
                        $user['student_id'] = $data['student_id'];
                        $user['faculty'] = $data['faculty'];
                        $user['major'] = $data['major'];
                        $user['year'] = $data['year'];
                        $user['phone'] = $data['phone'];
                        $user['address'] = $data['address'];
                        
                        if (!empty($_POST['password'])) {
                            $user['password'] = $_POST['password'];
                        }
                        
                        $found = true;
                        $success = 'แก้ไขข้อมูลนักศึกษาสำเร็จ';
                        break;
                    }
                }
                
                if (!$found) {
                    $error = 'ไม่พบข้อมูลนักศึกษา';
                }
                break;
                
            case 'delete':
                if (!$id) {
                    $error = 'ไม่พบรหัสนักศึกษา';
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
                    // ลบนักศึกษาออกจากเซสชัน
                    foreach ($_SESSION['users'] as $key => $user) {
                        if ($user['id'] == $id && $user['role'] == 'student') {
                            unset($_SESSION['users'][$key]);
                            $_SESSION['users'] = array_values($_SESSION['users']);
                            $success = 'ลบข้อมูลนักศึกษาสำเร็จ';
                            break;
                        }
                    }
                }
                break;
        }
    }
}

// ดึงข้อมูลนักศึกษาทั้งหมด
$students = [];
foreach ($_SESSION['users'] as $user) {
    if ($user['role'] == 'student') {
        $students[] = $user;
    }
}

// ดึงข้อมูลนักศึกษาที่ต้องการแก้ไข (ถ้ามี)
$student = null;
if ($action == 'edit' && $id) {
    foreach ($_SESSION['users'] as $user) {
        if ($user['id'] == $id && $user['role'] == 'student') {
            $student = $user;
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
        <h1><i class="fas fa-user-graduate"></i> <?php echo $page_title; ?></h1>
        <div class="breadcrumb">
            <a href="dashboard.php">แดชบอร์ด</a> &raquo; <span>จัดการข้อมูลนักศึกษา</span>
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
                    window.location.href = 'student.php';
                }, 1500);
            </script>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($action == 'list'): ?>
        <!-- รายการนักศึกษา -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>รายชื่อนักศึกษาทั้งหมด (<?php echo count($students); ?> คน)</h3>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> เพิ่มนักศึกษาใหม่
                </a>
            </div>
            
            <?php if (count($students) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>รหัสนักศึกษา</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>คณะ/สาขา</th>
                            <th>ปีการศึกษา</th>
                            <th>อีเมล</th>
                            <th>โทรศัพท์</th>
                            <th>วันที่สมัคร</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['student_id'] ?? '-'; ?></td>
                            <td><?php echo $student['name']; ?></td>
                            <td>
                                <?php 
                                echo ($student['faculty'] ?? '-') . ' / ' . ($student['major'] ?? '-');
                                ?>
                            </td>
                            <td><?php echo $student['year'] ?? '-'; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td><?php echo $student['phone'] ?? '-'; ?></td>
                            <td><?php echo $student['created_at'] ?? '-'; ?></td>
                            <td class="table-actions">
                                <a href="?action=edit&id=<?php echo $student['id']; ?>" 
                                   class="action-btn action-btn-edit">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <a href="?action=delete&id=<?php echo $student['id']; ?>" 
                                   class="action-btn action-btn-delete"
                                   onclick="return confirm('คุณแน่ใจที่จะลบนักศึกษาคนนี้?')">
                                    <i class="fas fa-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-user-graduate" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 15px;"></i>
                    <h3>ยังไม่มีข้อมูลนักศึกษา</h3>
                    <p>เริ่มต้นด้วยการเพิ่มนักศึกษาคนแรก</p>
                    <a href="?action=add" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-user-plus"></i> เพิ่มนักศึกษาใหม่
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
    <?php elseif ($action == 'add' || $action == 'edit'): ?>
        <!-- แบบฟอร์มเพิ่ม/แก้ไขนักศึกษา -->
        <div class="form-container">
            <h2>
                <i class="fas fa-<?php echo $action == 'add' ? 'user-plus' : 'user-edit'; ?>"></i>
                <?php echo $action == 'add' ? 'เพิ่มนักศึกษาใหม่' : 'แก้ไขข้อมูลนักศึกษา'; ?>
            </h2>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_id"><i class="fas fa-id-card"></i> รหัสนักศึกษา *</label>
                        <input type="text" id="student_id" name="student_id" 
                               value="<?php echo $student['student_id'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> ชื่อ-นามสกุล *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo $student['name'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user-tag"></i> ชื่อผู้ใช้ *</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo $student['username'] ?? ''; ?>" 
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
                               value="<?php echo $student['email'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> โทรศัพท์</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo $student['phone'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="faculty"><i class="fas fa-university"></i> คณะ *</label>
                        <select id="faculty" name="faculty" required>
                            <option value="">-- เลือกคณะ --</option>
                            <option value="คณะวิทยาศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะวิทยาศาสตร์' ? 'selected' : ''; ?>>คณะวิทยาศาสตร์</option>
                            <option value="คณะวิศวกรรมศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะวิศวกรรมศาสตร์' ? 'selected' : ''; ?>>คณะวิศวกรรมศาสตร์</option>
                            <option value="คณะบริหารธุรกิจ" <?php echo ($student['faculty'] ?? '') == 'คณะบริหารธุรกิจ' ? 'selected' : ''; ?>>คณะบริหารธุรกิจ</option>
                            <option value="คณะมนุษยศาสตร์และสังคมศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะมนุษยศาสตร์และสังคมศาสตร์' ? 'selected' : ''; ?>>คณะมนุษยศาสตร์และสังคมศาสตร์</option>
                            <option value="คณะศึกษาศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะศึกษาศาสตร์' ? 'selected' : ''; ?>>คณะศึกษาศาสตร์</option>
                            <option value="คณะเทคโนโลยีสารสนเทศ" <?php echo ($student['faculty'] ?? '') == 'คณะเทคโนโลยีสารสนเทศ' ? 'selected' : ''; ?>>คณะเทคโนโลยีสารสนเทศ</option>
                            <option value="คณะนิเทศศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะนิเทศศาสตร์' ? 'selected' : ''; ?>>คณะนิเทศศาสตร์</option>
                            <option value="คณะศิลปกรรมศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะศิลปกรรมศาสตร์' ? 'selected' : ''; ?>>คณะศิลปกรรมศาสตร์</option>
                            <option value="คณะสาธารณสุขศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะสาธารณสุขศาสตร์' ? 'selected' : ''; ?>>คณะสาธารณสุขศาสตร์</option>
                            <option value="คณะพยาบาลศาสตร์" <?php echo ($student['faculty'] ?? '') == 'คณะพยาบาลศาสตร์' ? 'selected' : ''; ?>>คณะพยาบาลศาสตร์</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="major"><i class="fas fa-book"></i> สาขาวิชา *</label>
                        <input type="text" id="major" name="major" 
                               value="<?php echo $student['major'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="year"><i class="fas fa-calendar-alt"></i> ปีการศึกษา *</label>
                        <select id="year" name="year" required>
                            <option value="">-- เลือกปีการศึกษา --</option>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <option value="ปี <?php echo $i; ?>" 
                                <?php echo ($student['year'] ?? '') == 'ปี ' . $i ? 'selected' : ''; ?>>
                                ปี <?php echo $i; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="address"><i class="fas fa-home"></i> ที่อยู่</label>
                        <textarea id="address" name="address" rows="2"><?php echo $student['address'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                    <a href="student.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>