<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = ''; 
$db   = 'db_wisata_purbalingga';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


$base_url = "http://localhost/wisata_purbalingga";
?>