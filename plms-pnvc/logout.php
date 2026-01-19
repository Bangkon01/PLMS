<?php
// logout.php
require_once 'config.php';

// ล้างข้อมูลเซสชัน
session_unset();
session_destroy();

// ไปที่หน้า login
redirect('login.php');
?>