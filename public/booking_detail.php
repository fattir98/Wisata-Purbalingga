<?php
require_once '../template/header.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    exit("Harus login.");
}

$id_booking = $_GET['id_booking'] ?? null;
if (!$id_booking) {
    exit("ID booking tidak ditemukan.");
}

$query = "SELECT b.*, w.nama AS nama_wisata, w.harga_tiket
          FROM booking_wisata b
          JOIN wisata w ON b.id_wisata = w.id_wisata
          WHERE b.id_booking = '$id_booking'
          AND b.id_user = '{$_SESSION['user_id']}'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    exit("Data tidak ditemukan.");
}

$isGratis = ($data['harga_tiket'] == 0);

// =========================
// DURASI 24 JAM
// =========================
$waktu_booking = strtotime($data['created_at']);
$sekarang = time();
$batas_detik = 24 * 60 * 60;
$selisih = $sekarang - $waktu_booking;

// 24 jam untuk pembatalan booking berbayar
$bisa_batal_berbayar = ($selisih <= $batas_detik);

// hitung sisa waktu upload
$sisa_upload_dalam_detik = $batas_detik - $selisih;
$sisa_upload_jam = max(0, floor($sisa_upload_dalam_detik / 3600));
$sisa_upload_menit = max(0, floor(($sisa_upload_dalam_detik % 3600) / 60));
?>

<div class="container mt-5 mb-5">

    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-3">Detail Booking</h2>

            <div class="mb-2"><strong>Wisata:</strong> <?= htmlspecialchars($data['nama_wisata']) ?></div>
            <div class="mb-2"><strong>Nama:</strong> <?= htmlspecialchars($data['nama']) ?></div>
            <div class="mb-2"><strong>No HP:</strong> <?= htmlspecialchars($data['no_hp']) ?></div>
            <div class="mb-2"><strong>Tanggal Kunjung:</strong> <?= $data['tanggal_kunjung'] ?></div>
            <div class="mb-2"><strong>Jumlah Orang:</strong> <?= $data['jumlah_orang'] ?></div>

            <div class="mb-2">
                <strong>Status:</strong>
                <span class="badge bg-warning text-dark"><?= $data['status'] ?></span>
            </div>

            <div class="mb-3">
                <strong>Catatan:</strong><br>
                <?= nl2br(htmlspecialchars($data['catatan'])) ?>
            </div>

            <?php if (!$isGratis): ?>

                <hr>
                <h5 class="fw-bold">Rincian Pembayaran</h5>

                <?php
                    $total = $data['total_harga'];
                    $dp = $total * 0.3;
                    $sisa = $total - $dp;
                ?>

                <div class="row mb-3">
                    <div class="col-6">Total Harga</div>
                    <div class="col-6 text-end fw-bold">Rp <?= number_format($total, 0, ',', '.') ?></div>

                    <div class="col-6 text-danger fw-bold">DP (30%)</div>
                    <div class="col-6 text-end fw-bold text-danger">Rp <?= number_format($dp, 0, ',', '.') ?></div>

                    <div class="col-6 text-muted small">Sisa Bayar</div>
                    <div class="col-6 text-end text-muted small">Rp <?= number_format($sisa, 0, ',', '.') ?></div>
                </div>

                <?php if (empty($data['bukti_bayar'])): ?>

                    <div class="alert alert-warning">
                        Upload bukti pembayaran DP maksimal dalam waktu <b>1×24 jam sejak booking dibuat</b>.<br>
                        <?php if ($sisa_upload_jam > 0 || $sisa_upload_menit > 0): ?>
                            Sisa waktu: <b><?= $sisa_upload_jam ?> jam <?= $sisa_upload_menit ?> menit</b>.
                        <?php else: ?>
                            <span class="text-danger fw-bold">Waktu upload telah habis.</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($sisa_upload_dalam_detik > 0): ?>
                        <form action="booking_upload.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_booking" value="<?= $data['id_booking'] ?>">
                            <div class="input-group">
                                <input type="file" name="bukti" class="form-control" required>
                                <button class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger text-center">
                            Anda tidak dapat mengupload bukti karena batas waktu sudah habis.
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info">
                        Bukti pembayaran sudah dikirim dan menunggu verifikasi admin.
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <hr>
                <div class="alert alert-info">
                    Wisata ini gratis. Booking menunggu konfirmasi admin.
                </div>

            <?php endif; ?>

            <?php if ($data['status'] == 'pending'): ?>

                <?php if ($isGratis || $bisa_batal_berbayar): ?>
                    <a href="booking_cancel.php?id_booking=<?= $data['id_booking'] ?>"
                       class="btn btn-danger w-100 mt-3"
                       onclick="return confirm('Yakin ingin membatalkan booking?')">
                        Batalkan Booking
                    </a>
                <?php else: ?>
                    <div class="alert alert-secondary text-center mt-3">
                        Batas waktu pembatalan sudah habis.
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>
