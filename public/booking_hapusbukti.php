<?php
require_once '../config/db.php';
session_start();

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil ID Booking
$id_booking = $_GET['id'] ?? null;
$id_user = $_SESSION['user_id'];

if (!$id_booking) {
    exit("ID tidak ditemukan.");
}

// 1. Cari nama filenya dulu di database
$query = "SELECT bukti_bayar FROM booking_wisata WHERE id_booking = '$id_booking' AND id_user = '$id_user'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($data && !empty($data['bukti_bayar'])) {
    
    $nama_file = $data['bukti_bayar'];
    $lokasi_file = "../assets/bukti/" . $nama_file;

    // 2. Hapus file fisik dari folder (Unlink)
    if (file_exists($lokasi_file)) {
        unlink($lokasi_file);
    }

    // 3. Update database jadi NULL (Kosong)
    $update = "UPDATE booking_wisata SET bukti_bayar = NULL WHERE id_booking = '$id_booking'";
    
    if (mysqli_query($conn, $update)) {
        echo "<script>
                alert('Bukti pembayaran berhasil dihapus. Silakan upload ulang.');
                window.location.href = 'booking_detail.php?id_booking=$id_booking';
              </script>";
    } else {
        echo "<script>alert('Gagal update database.'); window.history.back();</script>";
    }

} else {
    echo "<script>alert('Data tidak ditemukan atau bukti sudah kosong.'); window.history.back();</script>";
}
?>