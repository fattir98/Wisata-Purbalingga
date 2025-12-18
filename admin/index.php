<?php
// Panggil header
require_once '../template/header.php';

// CEK KEAMANAN: Tendang jika bukan admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    echo "<script>alert('Anda bukan Admin!'); window.location='../auth/login.php';</script>";
    exit;
}

// LOGIKA HAPUS DATA
if (isset($_GET['hapus_id'])) {
    $id_hapus = $_GET['hapus_id'];
    $hapus = mysqli_query($conn, "DELETE FROM wisata WHERE id_wisata='$id_hapus'");
    if($hapus) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='index.php';</script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi"></i> Dashboard Admin</h2>
        <a href="wisata_form.php" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah Wisata
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Foto</th>
                            <th>Nama Wisata</th>
                            <th>Kecamatan</th>
                            <th>Harga</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($conn, "SELECT * FROM wisata ORDER BY id_wisata DESC");
                        while($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center">
                                <?php if($row['foto']): ?>
                                    <img src="<?= (strpos($row['foto'], 'http') === 0) ? $row['foto'] : '../assets/img/'.$row['foto'] ?>" width="60" height="40" style="object-fit: cover;" class="rounded">
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($row['kecamatan']) ?></td>
                            <td>Rp <?= number_format($row['harga_tiket'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="wisata_form.php?id=<?= $row['id_wisata'] ?>" class="btn btn-sm btn-warning text-white me-1"><i class="bi bi-pencil-square"></i></a>
                                <a href="index.php?hapus_id=<?= $row['id_wisata'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>