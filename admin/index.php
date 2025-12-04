<?php
require_once '../template/header.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    echo "<script>window.location='".$base_url."/auth/login.php';</script>";
    exit;
}

$q_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM wisata");
$d_count = mysqli_fetch_assoc($q_count);
$q_views = mysqli_query($conn, "SELECT SUM(views) as total_views FROM wisata");
$d_views = mysqli_fetch_assoc($q_views);
?>
<div class="container mt-5">
    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Wisata</h5>
                    <p class="card-text display-4 fw-bold"><?= $d_count['total'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Views</h5>
                    <p class="card-text display-4 fw-bold"><?= $d_views['total_views'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
    <a href="wisata_list.php" class="btn btn-dark mt-3">Kelola Data Wisata &rarr;</a>
</div>
<?php require_once '../template/footer.php'; ?>