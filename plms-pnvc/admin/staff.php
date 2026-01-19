<?php
// admin/staff.php
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

$page_title = 'จัดการข้อมูลบุคลากร';
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
                    'staff_id' => $_POST['staff_id'],
                    'department' => $_POST['department'],
                    'position' => $_POST['position'],
                    'phone' => $_POST['phone'] ?? ''
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
                    // เพิ่มบุคลากรใหม่ในเซสชัน
                    $max_id = 0;
                    foreach ($_SESSION['users'] as $user) {
                        if ($user['id'] > $max_id) {
                            $max_id = $user['id'];
                        }
                    }
                    
                    $new_staff = [
                        'id' => $max_id + 1,
                        'username' => $data['username'],
                        'password' => $data['password'],
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role' => 'staff',
                        'staff_id' => $data['staff_id'],
                        'department' => $data['department'],
                        'position' => $data['position'],
                        'phone' => $data['phone'],
                        'created_at' => date('Y-m-d')
                    ];
                    
                    $_SESSION['users'][] = $new_staff;
                    $success = 'เพิ่มข้อมูลบุคลากรสำเร็จ';
                }
                break;
                
            case 'edit':
                if (!$id) {
                    $error = 'ไม่พบรหัสพนักงาน';
                    break;
                }
                
                $data = [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'staff_id' => $_POST['staff_id'],
                    'department' => $_POST['department'],
                    'position' => $_POST['position'],
                    'phone' => $_POST['phone'] ?? ''
                ];
                
                // หาบุคลากรและอัพเดทข้อมูล
                $found = false;
                foreach ($_SESSION['users'] as &$user) {
                    if ($user['id'] == $id && $user['role'] == 'staff') {
                        $user['name'] = $data['name'];
                        $user['email'] = $data['email'];
                        $user['staff_id'] = $data['staff_id'];
                        $user['department'] = $data['department'];
                        $user['position'] = $data['position'];
                        $user['phone'] = $data['phone'];
                        
                        if (!empty($_POST['password'])) {
                            $user['password'] = $_POST['password'];
                        }
                        
                        $found = true;
                        $success = 'แก้ไขข้อมูลบุคลากรสำเร็จ';
                        break;
                    }
                }
                
                if (!$found) {
                    $error = 'ไม่พบข้อมูลบุคลากร';
                }
                break;
                
            case 'delete':
                if (!$id) {
                    $error = 'ไม่พบรหัสพนักงาน';
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
                    // ลบบุคลากรออกจากเซสชัน
                    foreach ($_SESSION['users'] as $key => $user) {
                        if ($user['id'] == $id && $user['role'] == 'staff') {
                            unset($_SESSION['users'][$key]);
                            $_SESSION['users'] = array_values($_SESSION['users']);
                            $success = 'ลบข้อมูลบุคลากรสำเร็จ';
                            break;
                        }
                    }
                }
                break;
        }
    }
}

// ดึงข้อมูลบุคลากรทั้งหมด
$staffs = [];
foreach ($_SESSION['users'] as $user) {
    if ($user['role'] == 'staff') {
        $staffs[] = $user;
    }
}

// ดึงข้อมูลบุคลากรที่ต้องการแก้ไข (ถ้ามี)
$staff = null;
if ($action == 'edit' && $id) {
    foreach ($_SESSION['users'] as $user) {
        if ($user['id'] == $id && $user['role'] == 'staff') {
            $staff = $user;
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
        <h1><i class="fas fa-user-tie"></i> <?php echo $page_title; ?></h1>
        <div class="breadcrumb">
            <a href="dashboard.php">แดชบอร์ด</a> &raquo; <span>จัดการข้อมูลบุคลากร</span>
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
                    window.location.href = 'staff.php';
                }, 1500);
            </script>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($action == 'list'): ?>
        <!-- รายการบุคลากร -->
        <div class="data-table-container">
            <div class="table-header">
                <h3>รายชื่อบุคลากรทั้งหมด (<?php echo count($staffs); ?> คน)</h3>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> เพิ่มบุคลากรใหม่
                </a>
            </div>
            
            <?php if (count($staffs) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>รหัสพนักงาน</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>ตำแหน่ง</th>
                            <th>หน่วยงาน</th>
                            <th>อีเมล</th>
                            <th>โทรศัพท์</th>
                            <th>วันที่สมัคร</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffs as $staff): ?>
                        <tr>
                            <td><?php echo $staff['staff_id'] ?? '-'; ?></td>
                            <td><?php echo $staff['name']; ?></td>
                            <td><?php echo $staff['position'] ?? '-'; ?></td>
                            <td><?php echo $staff['department'] ?? '-'; ?></td>
                            <td><?php echo $staff['email']; ?></td>
                            <td><?php echo $staff['phone'] ?? '-'; ?></td>
                            <td><?php echo $staff['created_at'] ?? '-'; ?></td>
                            <td class="table-actions">
                                <a href="?action=edit&id=<?php echo $staff['id']; ?>" 
                                   class="action-btn action-btn-edit">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <a href="?action=delete&id=<?php echo $staff['id']; ?>" 
                                   class="action-btn action-btn-delete"
                                   onclick="return confirm('คุณแน่ใจที่จะลบบุคลากรคนนี้?')">
                                    <i class="fas fa-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-user-tie" style="font-size: 3rem; color: #bdc3c7; margin-bottom: 15px;"></i>
                    <h3>ยังไม่มีข้อมูลบุคลากร</h3>
                    <p>เริ่มต้นด้วยการเพิ่มบุคลากรคนแรก</p>
                    <a href="?action=add" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-user-plus"></i> เพิ่มบุคลากรใหม่
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
    <?php elseif ($action == 'add' || $action == 'edit'): ?>
        <!-- แบบฟอร์มเพิ่ม/แก้ไขบุคลากร -->
        <div class="form-container">
            <h2>
                <i class="fas fa-<?php echo $action == 'add' ? 'user-plus' : 'user-edit'; ?>"></i>
                <?php echo $action == 'add' ? 'เพิ่มบุคลากรใหม่' : 'แก้ไขข้อมูลบุคลากร'; ?>
            </h2>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="staff_id"><i class="fas fa-id-card"></i> รหัสพนักงาน *</label>
                        <input type="text" id="staff_id" name="staff_id" 
                               value="<?php echo $staff['staff_id'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> ชื่อ-นามสกุล *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo $staff['name'] ?? ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user-tag"></i> ชื่อผู้ใช้ *</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo $staff['username'] ?? ''; ?>" 
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
                               value="<?php echo $staff['email'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> โทรศัพท์</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo $staff['phone'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="department"><i class="fas fa-building"></i> หน่วยงาน *</label>
                        <select id="department" name="department" required>
                            <option value="">-- เลือกหน่วยงาน --</option>
                            <option value="งานทะเบียน" <?php echo ($staff['department'] ?? '') == 'งานทะเบียน' ? 'selected' : ''; ?>>งานทะเบียน</option>
                            <option value="งานบัญชี" <?php echo ($staff['department'] ?? '') == 'งานบัญชี' ? 'selected' : ''; ?>>งานบัญชี</option>
                            <option value="งานบุคคล" <?php echo ($staff['department'] ?? '') == 'งานบุคคล' ? 'selected' : ''; ?>>งานบุคคล</option>
                            <option value="งานอาคารสถานที่" <?php echo ($staff['department'] ?? '') == 'งานอาคารสถานที่' ? 'selected' : ''; ?>>งานอาคารสถานที่</option>
                            <option value="งานวิชาการ" <?php echo ($staff['department'] ?? '') == 'งานวิชาการ' ? 'selected' : ''; ?>>งานวิชาการ</option>
                            <option value="งานแผนงาน" <?php echo ($staff['department'] ?? '') == 'งานแผนงาน' ? 'selected' : ''; ?>>งานแผนงาน</option>
                            <option value="งานกิจการนักศึกษา" <?php echo ($staff['department'] ?? '') == 'งานกิจการนักศึกษา' ? 'selected' : ''; ?>>งานกิจการนักศึกษา</option>
                            <option value="ห้องสมุด" <?php echo ($staff['department'] ?? '') == 'ห้องสมุด' ? 'selected' : ''; ?>>ห้องสมุด</option>
                            <option value="งานเทคโนโลยีสารสนเทศ" <?php echo ($staff['department'] ?? '') == 'งานเทคโนโลยีสารสนเทศ' ? 'selected' : ''; ?>>งานเทคโนโลยีสารสนเทศ</option>
                            <option value="งานประชาสัมพันธ์" <?php echo ($staff['department'] ?? '') == 'งานประชาสัมพันธ์' ? 'selected' : ''; ?>>งานประชาสัมพันธ์</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="position"><i class="fas fa-briefcase"></i> ตำแหน่ง *</label>
                        <select id="position" name="position" required>
                            <option value="">-- เลือกตำแหน่ง --</option>
                            <option value="เจ้าหน้าที่" <?php echo ($staff['position'] ?? '') == 'เจ้าหน้าที่' ? 'selected' : ''; ?>>เจ้าหน้าที่</option>
                            <option value="นักวิชาการ" <?php echo ($staff['position'] ?? '') == 'นักวิชาการ' ? 'selected' : ''; ?>>นักวิชาการ</option>
                            <option value="นักจัดการงานทั่วไป" <?php echo ($staff['position'] ?? '') == 'นักจัดการงานทั่วไป' ? 'selected' : ''; ?>>นักจัดการงานทั่วไป</option>
                            <option value="นักทรัพยากรบุคคล" <?php echo ($staff['position'] ?? '') == 'นักทรัพยากรบุคคล' ? 'selected' : ''; ?>>นักทรัพยากรบุคคล</option>
                            <option value="นักเทคโนโลยีสารสนเทศ" <?php echo ($staff['position'] ?? '') == 'นักเทคโนโลยีสารสนเทศ' ? 'selected' : ''; ?>>นักเทคโนโลยีสารสนเทศ</option>
                            <option value="นักวิชาการเงินและบัญชี" <?php echo ($staff['position'] ?? '') == 'นักวิชาการเงินและบัญชี' ? 'selected' : ''; ?>>นักวิชาการเงินและบัญชี</option>
                            <option value="หัวหน้าแผนก" <?php echo ($staff['position'] ?? '') == 'หัวหน้าแผนก' ? 'selected' : ''; ?>>หัวหน้าแผนก</option>
                            <option value="ผู้จัดการ" <?php echo ($staff['position'] ?? '') == 'ผู้จัดการ' ? 'selected' : ''; ?>>ผู้จัดการ</option>
                            <option value="ผู้อำนวยการ" <?php echo ($staff['position'] ?? '') == 'ผู้อำนวยการ' ? 'selected' : ''; ?>>ผู้อำนวยการ</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึกข้อมูล
                    </button>
                    <a href="staff.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>