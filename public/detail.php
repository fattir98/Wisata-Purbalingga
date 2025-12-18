<?php
require_once '../template/header.php';

// Ambil ID wisata
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo "<div class='container mt-5'>Data wisata tidak ditemukan.</div>";
    require_once '../template/footer.php';
    exit;
}

// Update views
$stmt = $conn->prepare("UPDATE wisata SET views = views + 1 WHERE id_wisata = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Ambil data wisata
$stmt = $conn->prepare("SELECT * FROM wisata WHERE id_wisata = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    echo "<div class='container mt-5'>Data wisata tidak ditemukan.</div>";
    require_once '../template/footer.php';
    exit;
}

// WhatsApp
$wa_url = "#";
if ($data['wa_number']) {
    $text = "Halo, saya tertarik berkunjung ke " . $data['nama'] . ". Bisa minta info lebih lanjut?";
    $wa_url = "https://wa.me/" . $data['wa_number'] . "?text=" . urlencode($text);
}

// Cek login
$is_logged_in = isset($_SESSION['user_role']);

// Google Maps API Key
$api_key = 'AIzaSyAY0A9Wrh6CwlkYjplBLCNkHEUw3BG6t60';
?>

<div class="container mt-5 mb-5">
    <a href="javascript:history.back()" class="btn btn-link text-decoration-none mb-3 ps-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>

    <div class="row g-4">
        
        <div class="col-md-6">
            <?php
            
            $list_foto = [];

            // A. Foto Utama (dari tabel wisata)
            if (!empty($data['foto'])) {
                $img = $data['foto'];
                // Cek apakah link http atau file lokal
                $src = (strpos($img, 'http') === 0) ? $img : '../assets/img/' . $img;
                
                $list_foto[] = [
                    'src' => $src,
                    'alt' => 'Foto Utama'
                ];
            } else {
                // Foto Default jika kosong
                $list_foto[] = ['src' => '../assets/img/default.jpg', 'alt' => 'Default'];
            }

            // B. Foto Galeri (dari tabel wisata_galeri)
            $q_galeri = mysqli_query($conn, "SELECT * FROM wisata_galeri WHERE id_wisata = '$id'");
            while ($g = mysqli_fetch_assoc($q_galeri)) {
                $list_foto[] = [
                    'src' => '../assets/img_galeri/' . $g['nama_foto'],
                    'alt' => 'Galeri Wisata'
                ];
            }
            ?>

            <div id="sliderWisata" class="carousel slide shadow rounded-3 overflow-hidden" data-bs-ride="carousel">
                
                <div class="carousel-indicators">
                    <?php foreach($list_foto as $index => $foto): ?>
                        <button type="button" data-bs-target="#sliderWisata" 
                                data-bs-slide-to="<?= $index ?>" 
                                class="<?= $index === 0 ? 'active' : '' ?>"
                                aria-label="Slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">
                    <?php foreach($list_foto as $index => $foto): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= $foto['src'] ?>" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="<?= $foto['alt'] ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($list_foto) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#sliderWisata" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#sliderWisata" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                <?php endif; ?>
            </div>
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

            <p class="lead fs-6"><?= nl2br(htmlspecialchars($data['deskripsi'] ?? '')) ?></p>
            <hr class="my-4">

            <div class="row mb-4">
                <div class="col-12 mb-2"><strong><i class="bi bi-pin-map me-2"></i>Alamat:</strong> <br/><?= htmlspecialchars($data['alamat']) ?></div>
                <div class="col-12"><strong><i class="bi bi-stars me-2"></i>Fasilitas:</strong> <br/><?= htmlspecialchars($data['fasilitas']) ?></div>
            </div>

            <input type="hidden" id="lat_user">
            <input type="hidden" id="lng_user">

            <div class="mb-3">
                <div id="jarak" class="small text-primary fw-bold">Mencari lokasi Anda...</div>
            </div>

            <div class="d-grid gap-2 d-md-block">
                <a href="booking.php?id=<?= $data['id_wisata'] ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-calendar-check me-2"></i> Booking Tempat
                </a>
            </div>

        </div>
    </div>

    <div class="mt-5">
        <iframe
            width="100%"
            height="300"
            style="border:0"
            loading="lazy"
            allowfullscreen
            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAY0A9Wrh6CwlkYjplBLCNkHEUw3BG6t60&q=<?= urlencode($data['nama']); ?>">
        </iframe>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>

<script>
navigator.geolocation.getCurrentPosition(
    function(pos) {
        const latUser = pos.coords.latitude;
        const lngUser = pos.coords.longitude;

        document.getElementById("lat_user").value = latUser;
        document.getElementById("lng_user").value = lngUser;

        const latDest = <?= floatval($data['latitude']) ?>;
        const lngDest = <?= floatval($data['longitude']) ?>;

        // Estimasi cepat pakai Haversine
        function haversine(lat1, lon1, lat2, lon2) {
            const R = 6371; // km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2)**2 +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2)**2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        const distanceQuick = haversine(latUser, lngUser, latDest, lngDest) * 1.25; 
        document.getElementById("jarak").innerHTML = "Estimasi jarak: <b>" + distanceQuick.toFixed(2) + " km</b>...";

   
        const directionsService = new google.maps.DirectionsService();
        directionsService.route({
            origin: {lat: latUser, lng: lngUser},
            destination: {lat: latDest, lng: lngDest},
            travelMode: 'DRIVING'
        }, function(result, status) {
            if (status === 'OK') {
                const distanceMeters = result.routes[0].legs[0].distance.value;
                const distanceKm = distanceMeters / 1000;
                document.getElementById("jarak").innerHTML = "Jarak dari lokasi Anda: <b>" + distanceKm.toFixed(2) + " km</b>";
            } else {
                console.log("DirectionsService gagal: ", status);
            }
        });

    },
    function(err) {
        console.log("Lokasi tidak diizinkan:", err);
        document.getElementById("jarak").innerHTML = "Tidak dapat mengambil lokasi pengguna.";
    }
);
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= $api_key ?>"></script>