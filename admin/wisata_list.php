<?php
// Panggil koneksi database
require_once '../config/db.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    exit("Akses Ditolak.");
}

// Cek apakah ada ID yang dikirim
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Perintah SQL Hapus
    $query = mysqli_query($conn, "DELETE FROM wisata WHERE id_wisata = '$id'");

    if ($query) {
        // Jika berhasil, kembali ke index.php dengan pesan sukses
        echo "<script>alert('Data berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='index.php';</script>";
    }
} else {
    // Jika tidak ada ID, kembalikan ke index
    header("Location: index.php");
}
?>