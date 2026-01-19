<?php
require 'config.php';

$newPassword = '123456';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->execute([$hash, 'admin']);

echo "just reset pass<br>";
echo $hash;