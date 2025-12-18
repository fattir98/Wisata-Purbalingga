<?php

require_once '../config/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    exit("Akses Ditolak.");
}


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
  
    $query = mysqli_query($conn, "DELETE FROM wisata WHERE id_wisata = '$id'");

    if ($query) {
       
        echo "<script>alert('Data berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='index.php';</script>";
    }
} else {

    header("Location: index.php");
}
?>