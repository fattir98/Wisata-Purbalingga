<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = ''; // Sesuaikan dengan password XAMPP kamu (default kosong)
$db   = 'db_wisata_purbalingga';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Base URL (Sesuaikan dengan nama folder di htdocs)
$base_url = "http://localhost/wisata_purbalingga";
?>