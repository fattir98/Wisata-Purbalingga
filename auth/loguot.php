<?php
require_once '../config/db.php';
session_destroy();
header("Location: " . $base_url . "/public/index.php");
exit;
?>