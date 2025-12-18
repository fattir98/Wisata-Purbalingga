<?php
require_once '../config/db.php';
require_once '../template/header.php';

// 🔐 Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 🔍 Ambil semua booking milik user
$query = "SELECT b.*, w.nama AS nama_wisata
          FROM booking_wisata b
          JOIN wisata w ON b.id_wisata = w.id_wisata
          WHERE b.id_user = '$user_id'
          ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-5 mb-5">

    <h2 class="fw-bold mb-4">Daftar Booking Saya</h2>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">Kamu belum pernah melakukan booking.</div>
        <div style="height: 200px;"></div>
    <?php else: ?>
        <div class="list-group shadow-sm">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <a href="booking_detail.php?id_booking=<?= $row['id_booking'] ?>" 
                   class="list-group-item list-group-item-action py-3">

                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 fw-semibold">
                            <?= htmlspecialchars($row['nama_wisata']) ?>
                        </h5>

                        <span class="badge 
                            <?= $row['status'] == 'pending' ? 'bg-warning text-dark' : '' ?>
                            <?= $row['status'] == 'approved' ? 'bg-success' : '' ?>
                            <?= $row['status'] == 'cancelled' ? 'bg-danger' : '' ?>
                        ">
                            <?= $row['status'] ?>
                        </span>
                    </div>

                    <p class="mb-1 text-muted">
                        Tanggal kunjung: <?= $row['tanggal_kunjung'] ?>
                    </p>

                    <small>Jumlah orang: <?= $row['jumlah_orang'] ?></small>
                </a>
            <?php endwhile; ?>
        </div>
        <div style="height: 200px;"></div>
    <?php endif; ?>

</div>

<?php require_once '../template/footer.php'; ?>
