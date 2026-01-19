<?php
// admin/admin_header.php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - PLMS-SYSTEM' : 'PLMS-SYSTEM Admin'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .admin-logo i {
            margin-right: 10px;
            color: #3498db;
        }
        
        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-user-info i {
            font-size: 1.2rem;
        }
        
        .btn-logout {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 0.9rem;
        }
        
        .btn-logout:hover {
            background-color: #c0392b;
        }
        
        .admin-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }
        
        .admin-sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            position: sticky;
            top: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        
        .admin-profile {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2.5rem;
        }
        
        .admin-menu {
            padding: 20px 0;
        }
        
        .admin-menu ul {
            list-style: none;
        }
        
        .admin-menu li {
            margin-bottom: 5px;
        }
        
        .admin-menu a {
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .admin-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .admin-menu a:hover,
        .admin-menu a.active {
            background-color: #3498db;
            color: white;
        }
        
        .menu-section {
            padding: 15px 20px 5px;
            color: #95a5a6;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .admin-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .admin-content-header {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .admin-content-header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .admin-content-header h1 i {
            margin-right: 10px;
            color: #3498db;
        }
        
        .breadcrumb {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-card:nth-child(1) .stat-icon { background-color: #3498db; }
        .stat-card:nth-child(2) .stat-icon { background-color: #2ecc71; }
        .stat-card:nth-child(3) .stat-icon { background-color: #e74c3c; }
        .stat-card:nth-child(4) .stat-icon { background-color: #f39c12; }
        
        .stat-info h3 {
            font-size: 2rem;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .stat-info p {
            color: #7f8c8d;
        }
        
        /* Form Styles */
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        /* Table Styles */
        .data-table-container {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .table-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .table-actions {
            display: flex;
            gap: 5px;
        }
        
        /* Button Styles */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s;
        }
        
        .action-btn-edit {
            background-color: #17a2b8;
            color: white;
        }
        
        .action-btn-edit:hover {
            background-color: #138496;
        }
        
        .action-btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .action-btn-delete:hover {
            background-color: #c0392b;
        }
        
        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .table-actions {
                flex-direction: column;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="admin-header-content">
                <div class="admin-logo">
                    <i class="fas fa-cog"></i>
                    <span>PLMS-SYSTEM - Admin Panel</span>
                </div>
                <div class="admin-user-info">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo $_SESSION['user_name']; ?></span>
                        <a href="../logout.php" class="btn-logout">ออกจากระบบ</a>
                    <?php else: ?>
                        <a href="../login.php" class="btn-logout">เข้าสู่ระบบ</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>