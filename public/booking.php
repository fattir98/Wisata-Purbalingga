<?php 
// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; // Load DB dulu biar bisa akses $_SESSION di header nanti

// 🟦 CEK LOGIN & SIMPAN JEJAK (PERBAIKAN UTAMA DI SINI)
if (!isset($_SESSION['user_role'])) {
    
    // 🔥 1. Simpan Link Booking ini ke Session
    //    Supaya nanti Login tau harus balikin user ke sini
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    
    // 🔥 2. Lempar ke Login
    header("Location: ../auth/login.php");
    exit;
}

// Cek jika ADMIN mencoba booking (Opsional: Admin biasanya gak booking)
if ($_SESSION['user_role'] === 'admin') {
    echo "<script>alert('Admin tidak perlu booking. Silakan masuk ke Dashboard.'); window.location='../admin/index.php';</script>";
    exit;
}

require_once '../template/header.php';

$id = $_GET['id'] ?? null;
if(!$id){
    echo "<div class='container mt-5'>Data wisata tidak ditemukan.</div>";
    require_once '../template/footer.php';
    exit;
}

$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata = '$id'");
$data = mysqli_fetch_assoc($wisata);

if(!$data){
    echo "<div class='container mt-5'>Data wisata tidak ditemukan.</div>";
    require_once '../template/footer.php';
    exit;
}

// data user (buat ngisi otomatis form)
$id_user = $_SESSION['user_id'];
// Pastikan variabel koneksi ($conn) sudah tersedia dari db.php
$user = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'");
$userData = mysqli_fetch_assoc($user);

// 🔥 BAGIAN BARU: AMBIL TANGGAL YANG SUDAH DIBOOKING
// Kita cari tanggal yang statusnya BUKAN 'cancelled' (artinya pending/confirmed)
$query_cek = mysqli_query($conn, "SELECT tanggal_kunjung FROM booking_wisata 
                                  WHERE id_wisata = '$id' 
                                  AND status != 'cancelled'");

$tanggal_booked = [];
while ($tgl = mysqli_fetch_assoc($query_cek)) {
    $tanggal_booked[] = $tgl['tanggal_kunjung'];
}

// Ubah array ke JSON biar bisa dibaca JavaScript
$json_booked = json_encode($tanggal_booked);
?>

<div class="container mt-5 mb-5">
    <a href="javascript:history.back()" class="btn btn-link text-decoration-none mb-3 ps-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-3">Booking Tempat</h2>
            <p class="text-muted">Wisata: <strong><?= htmlspecialchars($data['nama']) ?></strong></p>
            <hr>

            <form action="booking_proses.php" method="POST">
                <input type="hidden" name="id_wisata" value="<?= $data['id_wisata'] ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama" 
                           class="form-control"
                           value="<?= htmlspecialchars($userData['nama'] ?? '') ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor HP</label>
                    <input type="text" name="no_hp" 
                           class="form-control"
                           placeholder="Contoh: 08123456789"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Kunjungan</label>
                    
                    <input type="date" name="tanggal_kunjung" id="inputTanggal" class="form-control" required>
                    
                    <small id="pesanError" class="text-danger fw-bold mt-2" style="display:none;">
                        <i class="bi bi-x-circle-fill"></i> Mohon maaf, tanggal ini sudah penuh/dipesan orang lain.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah Orang</label>
                    <input type="number" name="jumlah_orang" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                    <i class="bi bi-check2-circle me-2"></i> Kirim Booking
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Ambil data dari PHP
    const daftarFull = <?= $json_booked ?>;

    // 2. Ambil elemen
    const inputTgl = document.getElementById('inputTanggal');
    const pesan = document.getElementById('pesanError');
    const tombol = document.querySelector('button[type="submit"]');

    // 3. Pasang penjaga
    inputTgl.addEventListener('input', function() {
        const tglPilihan = this.value;

        // Cek apakah tanggal ada di daftar blacklist?
        if (daftarFull.includes(tglPilihan)) {
            // Jika ada: Hapus input, Munculkan pesan, Matikan tombol
            this.value = ''; 
            pesan.style.display = 'block';
            alert('Tanggal ' + tglPilihan + ' sudah tidak tersedia. Silakan pilih hari lain.');
            if(tombol) tombol.disabled = true;
        } else {
            // Jika aman
            pesan.style.display = 'none';
            if(tombol) tombol.disabled = false;
        }
    });

    // 4. Mencegah tanggal masa lalu (biar gak pilih kemarin)
    const hariIni = new Date().toISOString().split('T')[0];
    inputTgl.setAttribute('min', hariIni);
</script>

<?php require_once '../template/footer.php'; ?>