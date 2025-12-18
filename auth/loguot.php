<?php
require_once '../config/db.php';
session_start();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

// Redirect ke index.php di folder public menggunakan path relatif
header("Location: ../public/index.php");
exit;
?>
