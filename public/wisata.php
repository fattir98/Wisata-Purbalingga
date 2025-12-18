<?php 
require_once '../config/db.php';
require_once '../template/header.php';

// 🟦 Ambil parameter search & lokasi user
$search_keyword = "";
$where_clause = "";

$userLat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$userLng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

if (isset($_GET['q'])) {
    $search_keyword = mysqli_real_escape_string($conn, $_GET['q']);
    $where_clause = "WHERE nama LIKE '%$search_keyword%' 
                     OR kecamatan LIKE '%$search_keyword%' 
                     OR kategori LIKE '%$search_keyword%'";
}

// 🟦 Ambil data wisata
$query = mysqli_query($conn, "SELECT * FROM wisata $where_clause ORDER BY nama ASC");
?>

<div class="container mt-5 mb-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0">Daftar Wisata</h2>
            <p class="text-muted mb-0 small">
                <?php if($search_keyword): ?>
                    Menampilkan hasil pencarian: "<strong><?= htmlspecialchars($search_keyword) ?></strong>"
                <?php else: ?>
                    Menampilkan semua destinasi wisata
                <?php endif; ?>
            </p>
        </div>

        <!-- 🟦 Form Pencarian + Hidden Lokasi User -->
        <div class="col-md-6 mt-3 mt-md-0">
            <form action="" method="GET" class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control"
                    placeholder="Cari nama, kecamatan, atau kategori..."
                    value="<?= htmlspecialchars($search_keyword) ?>">

                <!-- Lokasi user (auto diisi via JS) -->
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">

                <?php if($search_keyword): ?>
                    <a href="wisata.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Reset
                    </a>
                <?php else: ?>
                    <button class="btn btn-primary" type="submit">Cari</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if(mysqli_num_rows($query) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query)): ?>

            <?php 
                // 🟦 Hitung jarak user → wisata (Haversine)
                $distance = null;

                if ($userLat !== null && $userLng !== null) {
                    $wLat = floatval($row['latitude']);
                    $wLng = floatval($row['longitude']);

                    if ($wLat && $wLng) {
                        $earthRadius = 6371;

                        $dLat = deg2rad($wLat - $userLat);
                        $dLng = deg2rad($wLng - $userLng);

                        $a = sin($dLat/2) * sin($dLat/2) +
                             cos(deg2rad($userLat)) * cos(deg2rad($wLat)) *
                             sin($dLng/2) * sin($dLng/2);

                        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

                        $distance = $earthRadius * $c;
                    }
                }

                //  Foto wisata (lokal/URL)
                $img = $row['foto'] ? $row['foto'] : 'default.jpg';
                $src = (strpos($img, 'http') === 0)
                    ? $img 
                    : '../assets/img/' . $img;
            ?>

            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm border-0 hover-effect">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-light text-dark shadow-sm"><?= $row['kategori'] ?></span>
                    </div>

                    <img src="<?= $src ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="<?= $row['nama'] ?>">

                    <div class="card-body p-3">
                        <h6 class="card-title fw-bold text-truncate"><?= htmlspecialchars($row['nama']) ?></h6>

                        <p class="card-text small text-secondary mb-1">
                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($row['kecamatan']) ?>
                        </p>

                        <p class="card-text small text-success fw-bold">
                            <?= $row['harga_tiket'] == 0 ? "Gratis" : "Rp " . number_format($row['harga_tiket'],0,',','.') ?>
                        </p>

                        <!-- 🟦 Jarak pengguna -->
                        <?php if ($distance !== null): ?>
                            <p class="small text-primary fw-bold mb-1">
                                <i class="bi bi-send"></i>
                                <?= number_format($distance, 2) ?> km dari lokasi Anda
                            </p>
                        <?php endif; ?>

                        <!-- 🟦 Tombol Lihat -->
                        <a href="detail.php?id=<?= $row['id_wisata'] ?>" 
                           class="btn btn-sm btn-primary w-100 mt-2">
                           Lihat
                        </a>

                        <!-- 🟦 Tombol Booking -->
                        <a href="booking.php?id=<?= $row['id_wisata'] ?>" 
                           class="btn btn-sm btn-success w-100 mt-2">
                           Booking
                        </a>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-1 text-muted mb-3"></i>
                <h4 class="text-muted">Tidak ditemukan wisata.</h4>
                <a href="wisata.php" class="btn btn-primary mt-2">Lihat Semua</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>

<!-- 🟦 AUTO AMBIL LOKASI USER -->
<script>
navigator.geolocation.getCurrentPosition(
    function(pos) {
        document.getElementById("lat").value = pos.coords.latitude;
        document.getElementById("lng").value = pos.coords.longitude;
    },
    function(err) {
        console.log("Lokasi tidak bisa diakses:", err);
    }
);
</script>
