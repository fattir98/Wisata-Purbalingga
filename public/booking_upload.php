<?php
require_once '../config/db.php';
session_start();

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti'])) {
    
    $id_booking = $_POST['id_booking'];
    $id_user = $_SESSION['user_id'];
    
    // Ambil info file
    $nama_file = $_FILES['bukti']['name'];
    $tmp_file = $_FILES['bukti']['tmp_name'];
    $error = $_FILES['bukti']['error'];
    $ukuran = $_FILES['bukti']['size'];

    // 1. Cek Error Upload
    if ($error === 4) {
        echo "<script>alert('Pilih gambar dulu!'); window.history.back();</script>";
        exit;
    }

    // 2. Cek Ekstensi Gambar 
    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiFile = explode('.', $nama_file);
    $ekstensiFile = strtolower(end($ekstensiFile));

    if (!in_array($ekstensiFile, $ekstensiValid)) {
        echo "<script>alert('Yang anda upload bukan gambar!'); window.history.back();</script>";
        exit;
    }

    // 3. Cek Ukuran (Maksimal 2MB)
    if ($ukuran > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar (Max 2MB)!'); window.history.back();</script>";
        exit;
    }

    // 4. Lolos Validasi -> Generate Nama Baru & Upload
    $nama_baru = "BUKTI-" . $id_booking . "-" . uniqid() . "." . $ekstensiFile;
    $tujuan = "../assets/bukti/" . $nama_baru;

    if (move_uploaded_file($tmp_file, $tujuan)) {
        
        // Update Database
        $query = "UPDATE booking_wisata SET bukti_bayar = '$nama_baru' 
                  WHERE id_booking = '$id_booking' AND id_user = '$id_user'";
        
        mysqli_query($conn, $query);

        echo "<script>
                alert('Terima kasih! Bukti pembayaran berhasil dikirim.');
                window.location.href = 'booking_detail.php?id_booking=$id_booking';
              </script>";
    } else {
        echo "<script>alert('Gagal mengupload file ke server.'); window.history.back();</script>";
    }
}
?>