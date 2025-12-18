<?php
require_once '../template/header.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    exit("Harus login untuk melihat booking!");
}

$id_booking = $_GET['id_booking'] ?? null;
if (!$id_booking) {
    exit("ID booking tidak ditemukan!");
}

$query = "SELECT b.*, w.nama AS nama_wisata, w.harga_tiket 
          FROM booking_wisata b 
          JOIN wisata w ON b.id_wisata = w.id_wisata
          WHERE b.id_booking = '$id_booking'
          AND b.id_user = '{$_SESSION['user_id']}'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    exit("Data booking tidak ditemukan atau Anda tidak punya akses.");
}

// Logika Waktu Pembatalan (24 Jam)
$waktu_booking = strtotime($data['created_at']); 
$waktu_sekarang = time(); 
$batas_detik = 24 * 60 * 60; 
$selisih = $waktu_sekarang - $waktu_booking;
$bisa_batal = ($selisih <= $batas_detik);
?>

<div class="container mt-5 mb-5">
    <a href="javascript:history.back()" class="btn btn-link text-decoration-none mb-3 ps-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-3">Detail Booking</h2>
            <p class="text-muted">Wisata:
                <strong><?= htmlspecialchars($data['nama_wisata']) ?></strong>
            </p>
            <hr>

            <div class="mb-2"><strong>Nama Pemesan:</strong> <?= htmlspecialchars($data['nama']) ?></div>
            <div class="mb-2"><strong>No HP:</strong> <?= htmlspecialchars($data['no_hp']) ?></div>
            <div class="mb-2"><strong>Tanggal Kunjung:</strong> <?= $data['tanggal_kunjung'] ?></div>
            <div class="mb-2"><strong>Jumlah Orang:</strong> <?= $data['jumlah_orang'] ?></div>
            <div class="mb-2"><strong>Status:</strong> 
                <span class="badge bg-warning text-dark"><?= $data['status'] ?></span>
            </div>

            <div class="mb-2"><strong>Catatan:</strong><br>
                <?= nl2br(htmlspecialchars($data['catatan'])) ?>
            </div>

            <hr>
            
            <h5 class="fw-bold">Rincian Pembayaran</h5>
            
            <?php 
                // Hitung DP 30%
                $total_harga = $data['total_harga']; 
                $persen_dp = 0.3; 
                $nominal_dp = $total_harga * $persen_dp;
                $sisa_bayar = $total_harga - $nominal_dp;
            ?>

            <div class="row mb-4">
                <div class="col-6">Total Harga Tiket</div>
                <div class="col-6 text-end fw-bold">Rp <?= number_format($total_harga, 0, ',', '.') ?></div>
                
                <div class="col-6 text-danger fw-bold">Uang Muka (DP 30%)</div>
                <div class="col-6 text-end fw-bold text-danger">Rp <?= number_format($nominal_dp, 0, ',', '.') ?></div>
                
                <div class="col-6 text-muted small">Sisa Bayar (di Lokasi)</div>
                <div class="col-6 text-end text-muted small">Rp <?= number_format($sisa_bayar, 0, ',', '.') ?></div>
            </div>

            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-wallet2"></i> Konfirmasi Pembayaran</h6>

                    <?php if (empty($data['bukti_bayar'])): ?>
                        <p class="small text-muted mb-3">
                            Harap transfer DP sebesar <strong>Rp <?= number_format($nominal_dp, 0, ',', '.') ?></strong> ke:
                            <br>
                            <strong>BCA 123-456-789</strong> (Wisata Purbalingga)
                            <br>
                            <span class="text-danger">*Booking akan otomatis batal jika tidak membayar dalam 24 jam.</span>
                        </p>

                        <form action="booking_upload.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_booking" value="<?= $data['id_booking'] ?>">
                            
                            <div class="input-group">
                                <input type="file" name="bukti" class="form-control" accept="image/*" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Kirim Bukti
                                </button>
                            </div>
                            <small class="text-muted fst-italic mt-1 d-block">Format: JPG, PNG (Max 2MB)</small>
                        </form>

                    <?php else: ?>
                        <div class="alert alert-info py-2 mb-0 border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-2"></i> 
                                <div>
                                    <strong>Bukti Terkirim!</strong><br>
                                    <span class="small">Admin sedang memverifikasi</span>
                                </div>
                            </div>
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <a href="../assets/bukti/<?= $data['bukti_bayar'] ?>" target="_blank" class="btn btn-sm btn-outline-info flex-fill">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>

                                <?php if ($data['status'] == 'pending'): ?>
                                    <a href="booking_hapus_bukti.php?id=<?= $data['id_booking'] ?>" 
                                       class="btn btn-sm btn-outline-danger flex-fill"
                                       onclick="return confirm('Yakin ingin menghapus bukti ini? Anda harus upload ulang nanti.')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($data['status'] == 'pending'): ?>
                
                <?php if ($bisa_batal): ?>
                    <a href="booking_cancel.php?id_booking=<?= $data['id_booking'] ?>" 
                       class="btn btn-danger mt-3 w-100"
                       onclick="return confirm('Yakin ingin membatalkan? Uang DP tidak dapat dikembalikan.')">
                       Batalkan Booking
                    </a>
                    
                    <?php 
                        $sisa = $batas_detik - $selisih;
                        $jam = floor($sisa / 3600);
                        $menit = floor(($sisa % 3600) / 60);
                    ?>
                    <small class="text-danger d-block text-center mt-2">
                        *Sisa waktu pembatalan: <?= $jam ?> jam <?= $menit ?> menit lagi.
                    </small>

                <?php else: ?>
                    <div class="alert alert-secondary mt-3 text-center">
                        <i class="bi bi-x-circle"></i> Maaf, batas waktu pembatalan (1x24 Jam) sudah habis.
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>