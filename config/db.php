<?php
// 🔥 PERBAIKAN: Cek dulu status session biar gak error "Notice"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$pass = ''; 
$db   = 'db_wisata_purbalingga';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set Zona Waktu ke WIB (Penting buat fitur booking 24 jam)
date_default_timezone_set('Asia/Jakarta'); 
mysqli_query($conn, "SET time_zone = '+07:00'"); 

$base_url = "http://localhost/wisata_purbalingga";
?>