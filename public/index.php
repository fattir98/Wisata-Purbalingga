<?php 
require_once '../template/header.php'; 
// Ambil 6 wisata trending
$query = mysqli_query($conn, "SELECT * FROM wisata ORDER BY views DESC LIMIT 6");
?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Wisata Purbalingga</h1>
        <p class="lead mb-4">Temukan keindahan alam, edukasi, dan sejarah di Kota Perwira.</p>
        
        <!-- Search Bar Hero -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form action="wisata.php" method="GET" class="d-flex bg-white p-2 rounded-pill shadow-lg">
                    <input type="text" name="q" class="form-control border-0 rounded-pill ps-3 shadow-none" placeholder="Cari tempat wisata..." required>
                    <button class="btn btn-primary rounded-pill px-4" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-4">
            <a href="wisata.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Lihat Semua Daftar</a>
        </div>
    </div>
</div>

<!-- Trending Section -->
<div class="container mb-5">
    <h3 class="mb-4 border-bottom pb-2"><i class="bi bi-fire text-danger me-2"></i>Wisata Paling Trending</h3>
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($query)): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 hover-effect">
                <?php $img = $row['foto'] ? $row['foto'] : 'default.jpg'; ?>
                <!-- Cek jika foto URL eksternal (https) atau lokal -->
                <?php $src = (strpos($img, 'http') === 0) ? $img : $base_url . '/assets/img/' . $img; ?>
                
                <img src="<?= $src ?>" class="card-img-top" alt="<?= $row['nama'] ?>">
                <div class="card-body">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($row['nama']) ?></h5>
                    <p class="card-text text-secondary mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($row['kecamatan']) ?>
                    </p>
                    <p class="card-text fw-bold text-success mb-3">
                        <?= $row['harga_tiket'] == 0 ? "Gratis" : "Rp " . number_format($row['harga_tiket'],0,',','.') ?>
                    </p>
                    <a href="detail.php?id=<?= $row['id_wisata'] ?>" class="btn btn-outline-primary w-100 rounded-pill">Lihat Detail</a>
                </div>
                <div class="card-footer bg-white text-muted small border-0">
                    <i class="bi bi-eye me-1"></i> <?= $row['views'] ?> dilihat
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>