<?php 
require_once '../template/header.php'; 

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='container mt-5'>Data tidak ditemukan.</div>";
    require_once '../template/footer.php';
    exit;
}

// Update Views
mysqli_query($conn, "UPDATE wisata SET views = views + 1 WHERE id_wisata = '$id'");

$query = mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata = '$id'");
$data = mysqli_fetch_assoc($query);
if (!$data) exit;

// URL WhatsApp
$wa_url = "#";
if ($data['wa_number']) {
    $text = "Halo, saya tertarik berkunjung ke " . $data['nama'] . ". Bisa minta info lebih lanjut?";
    $wa_url = "https://wa.me/" . $data['wa_number'] . "?text=" . urlencode($text);
}
?>

<div class="container mt-5 mb-5">
    <a href="javascript:history.back()" class="btn btn-link text-decoration-none mb-3 ps-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div class="row g-4">
        <div class="col-md-6">
            <?php 
                $img = $data['foto'] ? $data['foto'] : 'default.jpg'; 
                $src = (strpos($img, 'http') === 0) ? $img : $base_url . '/assets/img/' . $img;
            ?>
            <img src="<?= $src ?>" class="img-fluid rounded-3 shadow w-100" alt="<?= $data['nama'] ?>">
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold mb-2"><?= htmlspecialchars($data['nama']) ?></h1>
            <div class="mb-3">
                <span class="badge bg-primary me-2"><?= $data['kategori'] ?></span>
                <span class="badge bg-secondary me-2"><i class="bi bi-geo-alt me-1"></i><?= $data['kecamatan'] ?></span>
                <span class="badge bg-warning text-dark"><i class="bi bi-eye me-1"></i><?= $data['views'] ?> Views</span>
            </div>
            
            <h3 class="text-success fw-bold mb-4">
                <?= $data['harga_tiket'] == 0 ? "Gratis Masuk" : "Rp " . number_format($data['harga_tiket'], 0, ',', '.') ?>
            </h3>

            <p class="lead fs-6"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
            <hr class="my-4">
            
            <div class="row mb-4">
                <div class="col-12 mb-2"><strong><i class="bi bi-pin-map me-2"></i>Alamat:</strong> <br/><?= htmlspecialchars($data['alamat']) ?></div>
                <div class="col-12"><strong><i class="bi bi-stars me-2"></i>Fasilitas:</strong> <br/><?= htmlspecialchars($data['fasilitas']) ?></div>
            </div>

            <div class="d-grid gap-2 d-md-block">
                <?php if($data['link_maps']): ?>
                    <a href="<?= $data['link_maps'] ?>" target="_blank" class="btn btn-outline-danger me-md-2 btn-lg">
                        <i class="bi bi-map me-2"></i> Google Maps
                    </a>
                <?php endif; ?>
                <?php if($data['wa_number']): ?>
                    <a href="<?= $wa_url ?>" target="_blank" class="btn btn-success btn-lg">
                        <i class="bi bi-whatsapp me-2"></i> Hubungi WhatsApp
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>