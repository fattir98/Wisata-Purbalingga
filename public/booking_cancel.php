<?php
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    exit("Harus login untuk membatalkan booking!");
}

$id_booking = $_GET['id_booking'] ?? null;

if (!$id_booking) {
    exit("ID booking tidak ditemukan!");
}

// pastikan booking ini milik user yang login
$check = mysqli_query($conn,
    "SELECT * FROM booking_wisata 
     WHERE id_booking='$id_booking'
     AND id_user='{$_SESSION['user_id']}'"
);

if (mysqli_num_rows($check) == 0) {
    exit("Booking tidak ditemukan atau Anda tidak punya akses.");
}

// update status
mysqli_query($conn,
    "UPDATE booking_wisata 
     SET status='cancelled'
     WHERE id_booking='$id_booking'"
);

echo "
<script>
    alert('Booking berhasil dibatalkan!');
    window.location.href = 'booking_detail.php?id_booking=$id_booking';
</script>
";
