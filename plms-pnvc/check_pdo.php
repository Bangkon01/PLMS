<?php
/**
 * PDO MySQL Driver Diagnostic Script
 * ตรวจสอบ PDO MySQL Driver และการตั้งค่า
 */

echo "=== PDO MySQL Driver Diagnostic ===\n\n";

// 1. Check if PDO extension is loaded
echo "1. PDO Extension Status:\n";
echo "   - PDO Loaded: " . (extension_loaded('pdo') ? "✓ Yes" : "✗ No") . "\n";
echo "   - PDO MySQL: " . (extension_loaded('pdo_mysql') ? "✓ Yes" : "✗ No") . "\n";
echo "   - MySQLi: " . (extension_loaded('mysqli') ? "✓ Yes" : "✗ No") . "\n";
echo "   - MySQLnd: " . (extension_loaded('mysqlnd') ? "✓ Yes" : "✗ No") . "\n\n";

// 2. Check available PDO drivers
echo "2. Available PDO Drivers:\n";
$drivers = PDO::getAvailableDrivers();
if (!empty($drivers)) {
    foreach ($drivers as $driver) {
        echo "   - " . $driver . "\n";
    }
} else {
    echo "   ✗ No PDO drivers available\n";
}
echo "\n";

// 3. Check PHP configuration
echo "3. PHP Configuration:\n";
echo "   - PHP Version: " . PHP_VERSION . "\n";
echo "   - PHP SAPI: " . php_sapi_name() . "\n";
echo "   - Extension Dir: " . ini_get('extension_dir') . "\n";
echo "   - Loaded config: " . php_ini_loaded_file() . "\n\n";

// 4. Test connection
echo "4. Database Connection Test:\n";
try {
    $dsn = "mysql:host=localhost;dbname=mysql;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "   ✓ Connection successful!\n";
    echo "   - Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "   ✗ Connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Recommendations
echo "5. Recommendations:\n";
if (!extension_loaded('pdo_mysql')) {
    echo "   ⚠ PDO MySQL is not loaded. Check php.ini configuration.\n";
    echo "   - Ensure 'extension=php_pdo_mysql.dll' is uncommented in php.ini\n";
    echo "   - PHP configuration file: " . php_ini_loaded_file() . "\n";
}

if (extension_loaded('pdo_mysql')) {
    echo "   ✓ PDO MySQL is properly configured and ready to use.\n";
}

// Check for duplicate extensions in loaded modules
$modules = get_loaded_extensions();
$mysqli_count = array_reduce($modules, function($count, $ext) {
    return $count + ($ext === 'mysqli' ? 1 : 0);
}, 0);

if ($mysqli_count > 1) {
    echo "   ⚠ Warning: mysqli extension appears multiple times in php.ini\n";
    echo "   - Check php.ini for duplicate 'extension=mysqli' lines\n";
}

echo "\n=== End of Diagnostic ===\n";
?>
