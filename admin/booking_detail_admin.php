<?php
require_once '../config/db.php';

// Cek Session Admin
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../template/header.php';

$id_booking = $_GET['id_booking'] ?? null;
if (!$id_booking) {
    echo "<div class='container mt-5 alert alert-danger'>ID booking tidak ditemukan!</div>";
    require_once '../template/footer.php';
    exit;
}

// 🔥 LOGIKA UPDATE STATUS (PERBAIKAN KATA KUNCI)
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status']; 
    $update = mysqli_query($conn, "UPDATE booking_wisata SET status='$status_baru' WHERE id_booking='$id_booking'");
    
    if ($update) {
        echo "<script>
                alert('Status berhasil diubah menjadi: " . strtoupper($status_baru) . "'); 
                window.location.href='booking_detail_admin.php?id_booking=$id_booking';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal update database.');</script>";
    }
}

// Ambil data
$query = "SELECT b.*, w.nama AS nama_wisata, w.harga_tiket, u.nama AS nama_user
          FROM booking_wisata b
          JOIN wisata w ON b.id_wisata = w.id_wisata
          JOIN users u ON b.id_user = u.id_user
          WHERE b.id_booking = '$id_booking'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) exit("Data tidak ditemukan.");
?>

<div class="container mt-5 mb-5" style="max-width: 800px;"> 
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0 fw-bold"><i class="bi bi-file-text me-2"></i>Detail Data</h5>
        </div>
        <div class="card-body p-4">
            <table class="table table-borderless align-middle">
                <tr>
                    <td class="text-muted" width="150">Nama Wisata</td>
                    <td class="fw-bold fs-5">: <?= htmlspecialchars($data['nama_wisata']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Pemesan</td>
                    <td>: <?= htmlspecialchars($data['nama_user']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">No HP</td>
                    <td>: <?= htmlspecialchars($data['no_hp']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Tanggal Kunjung</td>
                    <td>: <?= date('d F Y', strtotime($data['tanggal_kunjung'])) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Jumlah Orang</td>
                    <td>: <?= $data['jumlah_orang'] ?> Orang</td>
                </tr>
                <tr>
                    <td class="text-muted">Total Harga</td>
                    <td class="fw-bold text-success fs-4">
                        : Rp <?= number_format($data['total_harga'], 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Status</td>
                    <td>: 
                        <?php 
                            // 🔥 PERBAIKAN LOGIKA WARNA & STATUS 🔥
                            $st = $data['status'];
                            // Cek apakah 'approved' atau 'confirmed' (untuk jaga-jaga)
                            if ($st == 'approved' || $st == 'confirmed') {
                                $bg = 'success';
                                $label = 'APPROVED';
                            } elseif ($st == 'rejected' || $st == 'cancelled') {
                                $bg = 'danger';
                                $label = 'REJECTED';
                            } else {
                                $bg = 'warning';
                                $label = 'PENDING';
                            }
                        ?>
                        <span class="badge bg-<?= $bg ?> p-2"><?= $label ?></span>
                    </td>
                </tr>
            </table>

            <?php if ($data['status'] == 'pending'): ?>
                <hr>
                <div class="alert alert-info small mb-3">
                    <i class="bi bi-arrow-down-circle"></i> Cek bukti transfer di bawah sebelum konfirmasi.
                </div>
                <form method="POST" class="d-flex gap-2">
                    <input type="hidden" name="update_status" value="1">
                    
                    <button type="submit" name="status" value="approved" class="btn btn-success flex-fill py-2 fw-bold" onclick="return confirm('Terima pembayaran?')">
                        <i class="bi bi-check-lg"></i> Terima (Approve)
                    </button>
                    
                    <button type="submit" name="status" value="rejected" class="btn btn-danger flex-fill py-2 fw-bold" onclick="return confirm('Tolak pesanan?')">
                        <i class="bi bi-x-lg"></i> Tolak (Cancel)
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-warning text-dark rounded-top-4">
            <h5 class="mb-0 fw-bold"><i class="bi bi-image me-2"></i>Bukti Pembayaran User</h5>
        </div>
        <div class="card-body text-center p-4">
            
            <?php if (!empty($data['bukti_bayar'])): ?>
                <div class="mb-3 bg-light p-3 rounded border" style="display: inline-block;">
                    <img src="../assets/bukti/<?= $data['bukti_bayar'] ?>" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-width: 400px; max-height: 500px;" 
                         alt="Bukti Transfer">
                </div>
                
                <div class="mt-2">
                    <a href="../assets/bukti/<?= $data['bukti_bayar'] ?>" target="_blank" class="btn btn-outline-dark">
                        <i class="bi bi-zoom-in"></i> Buka Gambar Full (Tab Baru)
                    </a>
                </div>

            <?php else: ?>
                <div class="py-5 text-muted">
                    <i class="bi bi-image-alt" style="font-size: 5rem; opacity: 0.3;"></i>
                    <p class="mt-3 fw-bold fs-5">Belum ada bukti upload</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php require_once '../template/footer.php'; ?>