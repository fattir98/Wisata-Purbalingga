<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = "../public/wisata.php";
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user = $_SESSION['user_id'];
    $id_wisata = $_POST['id_wisata'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $tanggal = $_POST['tanggal_kunjung'];
    $jumlah = $_POST['jumlah_orang'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    $tanggal_sekarang = date('Y-m-d');
    if ($tanggal < $tanggal_sekarang) {
        echo "
            <script>
                alert('Tanggal kunjungan tidak boleh kurang dari hari ini.');
                window.history.back();
            </script>
        ";
        exit;
    }

    $w = mysqli_query($conn, "SELECT harga_tiket FROM wisata WHERE id_wisata='$id_wisata'");
    $wisata = mysqli_fetch_assoc($w);
    $harga = $wisata['harga_tiket'];
    $total = $harga * $jumlah;

    $status = 'pending';

    $query = "INSERT INTO booking_wisata 
        (id_user, id_wisata, nama, no_hp, tanggal_kunjung, jumlah_orang, total_harga, catatan, status, created_at)
        VALUES
        ('$id_user', '$id_wisata', '$nama', '$no_hp', '$tanggal', '$jumlah', '$total', '$catatan', '$status', NOW())";

    if (mysqli_query($conn, $query)) {
        $id_booking = mysqli_insert_id($conn);
        echo "
            <script>
                alert('Booking berhasil dikirim.');
                window.location.href = 'booking_detail.php?id_booking=$id_booking';
            </script>
        ";
    } else {
        echo "
            <script>
                alert('Booking gagal.');
                window.history.back();
            </script>
        ";
    }

} else {
    header("Location: ../public/index.php");
    exit;
}
