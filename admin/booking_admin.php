<?php
require_once '../config/db.php';
// 🔐 Cek admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../public/index.php");
    exit;
}

require_once '../template/header.php';

// 🔍 Ambil filter status dari GET
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// 🔹 Buat query
$query = "SELECT b.*, w.nama AS nama_wisata, u.nama AS nama_user
          FROM booking_wisata b
          JOIN wisata w ON b.id_wisata = w.id_wisata
          JOIN users u ON b.id_user = u.id_user";

if ($status != '') {
    $query .= " WHERE b.status='$status'";
}

$query .= " ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-5 mb-5">
    <h2 class="fw-bold mb-4">Dashboard Booking Admin</h2>

    <div class="mb-3">
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select w-auto">
                <option value="">Semua Status</option>
                <option value="pending" <?= $status=='pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $status=='approved' ? 'selected' : '' ?>>Approved</option>
                <option value="cancelled" <?= $status=='cancelled' ? 'selected' : '' ?>>Canceled</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    

    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">Tidak ada booking yang ditemukan.</div>
        <div style="height: 200px;"></div>
    <?php else: ?>
        <div class="list-group shadow-sm">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <a href="booking_detail_admin.php?id_booking=<?= $row['id_booking'] ?>" 
                   class="list-group-item list-group-item-action py-3">

                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1 fw-semibold"><?= htmlspecialchars($row['nama_wisata']) ?></h5>
                        <span class="badge 
                            <?= $row['status']=='pending' ? 'bg-warning text-dark' : '' ?>
                            <?= $row['status']=='approved' ? 'bg-success' : '' ?>
                            <?= $row['status']=='cancelled' ? 'bg-danger' : '' ?>
                        "><?= $row['status'] ?></span>
                    </div>

                    <p class="mb-1 text-muted">
                        Pemesan: <?= htmlspecialchars($row['nama_user']) ?> | Tanggal kunjung: <?= $row['tanggal_kunjung'] ?>
                    </p>

                    <small>Jumlah orang: <?= $row['jumlah_orang'] ?></small>
                </a>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>
<div style="height: 200px;"></div>

<?php require_once '../template/footer.php'; ?>
