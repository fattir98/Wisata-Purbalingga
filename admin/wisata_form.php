<?php
require_once '../template/header.php';

// Cek Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    echo "<script>window.location='../auth/login.php';</script>";
    exit;
}

// ==================================================================
// 🔥 FUNGSI BANTUAN UNTUK MENGURUS FOTO GALERI (UPLOAD & UPDATE) 🔥
// ==================================================================
function processGalleryPhoto($conn, $id_wisata, $fileInputName, $idGaleriPostName, $oldPhotoPostName) {
    // Cek apakah ada file baru yang diupload di input ini?
    if (!empty($_FILES[$fileInputName]['name'])) {
        $id_galeri = $_POST[$idGaleriPostName] ?? ''; 
        $foto_lama = $_POST[$oldPhotoPostName] ?? ''; 

        $nama_file = $_FILES[$fileInputName]['name'];
        $tmp_file = $_FILES[$fileInputName]['tmp_name'];
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_baru = "GALERI-" . time() . "-" . uniqid() . "." . $ext;
        $path_baru = "../assets/img_galeri/" . $nama_baru;

        if (move_uploaded_file($tmp_file, $path_baru)) {
            if ($foto_lama && file_exists("../assets/img_galeri/" . $foto_lama)) {
                unlink("../assets/img_galeri/" . $foto_lama);
            }
            if ($id_galeri) {
                mysqli_query($conn, "UPDATE wisata_galeri SET nama_foto='$nama_baru' WHERE id_galeri='$id_galeri'");
            } else {
                mysqli_query($conn, "INSERT INTO wisata_galeri (id_wisata, nama_foto) VALUES ('$id_wisata', '$nama_baru')");
            }
        }
    }
}
// ==================================================================

// Inisialisasi variabel
$id = '';
$nama = ''; $deskripsi = ''; $alamat = ''; $kecamatan = ''; $kategori = 'Alam';
$harga_tiket = 0; $fasilitas = ''; $link_maps = ''; $wa_number = ''; 
$latitude = ''; $longitude = '';
$foto_lama = ''; 
$galeri_photos = [
    ['id_galeri' => '', 'nama_foto' => ''], 
    ['id_galeri' => '', 'nama_foto' => ''] 
];

// --- AMBIL DATA EDIT ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata='$id'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $nama = $data['nama'];
        $deskripsi = $data['deskripsi'];
        $alamat = $data['alamat'];
        $kecamatan = $data['kecamatan'];
        $kategori = $data['kategori'];
        $harga_tiket = $data['harga_tiket'];
        $fasilitas = $data['fasilitas'];
        $link_maps = $data['link_maps'];
        $wa_number = $data['wa_number'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        $foto_lama = $data['foto']; 

        $q_galeri = mysqli_query($conn, "SELECT * FROM wisata_galeri WHERE id_wisata='$id' LIMIT 2");
        $counter = 0;
        while($row_galeri = mysqli_fetch_assoc($q_galeri)) {
            if(isset($galeri_photos[$counter])) {
                $galeri_photos[$counter] = $row_galeri;
            }
            $counter++;
        }
    }
}

// --- SIMPAN DATA (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kategori = $_POST['kategori'];
    $harga_tiket = $_POST['harga_tiket'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $fasilitas = mysqli_real_escape_string($conn, $_POST['fasilitas']);
    $link_maps = mysqli_real_escape_string($conn, $_POST['link_maps']);
    $wa_number = mysqli_real_escape_string($conn, $_POST['wa_number']);
    $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);

    // 1. FOTO UTAMA
    $nama_foto_utama = $_POST['foto_lama']; 
    if (!empty($_FILES['foto_upload']['name'])) {
        $nama_file = $_FILES['foto_upload']['name'];
        $tmp_file = $_FILES['foto_upload']['tmp_name'];
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_foto_utama = time() . "_" . uniqid() . "." . $ext;
        $path_utama = "../assets/img/" . $nama_foto_utama;

        if(move_uploaded_file($tmp_file, $path_utama)) {
             if ($_POST['foto_lama'] && file_exists("../assets/img/" . $_POST['foto_lama'])) {
                unlink("../assets/img/" . $_POST['foto_lama']);
            }
        } else {
             $nama_foto_utama = $_POST['foto_lama'];
        }
    }

    // 2. QUERY UTAMA
    if ($id) {
        $sql = "UPDATE wisata SET 
        nama='$nama', deskripsi='$deskripsi', alamat='$alamat', 
        kecamatan='$kecamatan', kategori='$kategori', harga_tiket='$harga_tiket', 
        fasilitas='$fasilitas', link_maps='$link_maps', wa_number='$wa_number',
        latitude='$latitude', longitude='$longitude',
        foto='$nama_foto_utama'
        WHERE id_wisata='$id'";
        $tindakan = "diupdate";
    } else {
        $sql = "INSERT INTO wisata 
        (nama, deskripsi, alamat, kecamatan, kategori, harga_tiket, fasilitas, link_maps, wa_number, latitude, longitude, views, foto) 
        VALUES 
        ('$nama', '$deskripsi', '$alamat', '$kecamatan', '$kategori', '$harga_tiket', '$fasilitas', '$link_maps', '$wa_number', '$latitude', '$longitude', 0, '$nama_foto_utama')";
        $tindakan = "disimpan";
    }

    if (mysqli_query($conn, $sql)) {
        $id_wisata_fix = $id ? $id : mysqli_insert_id($conn);
        // 3. PROSES GALERI
        processGalleryPhoto($conn, $id_wisata_fix, 'foto_galeri_1', 'id_galeri_1', 'foto_galeri_lama_1');
        processGalleryPhoto($conn, $id_wisata_fix, 'foto_galeri_2', 'id_galeri_2', 'foto_galeri_lama_2');

        echo "<script>alert('Data berhasil $tindakan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data utama!');</script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i><?= $id ? 'Edit Data Wisata' : 'Tambah Wisata Baru' ?></h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="foto_lama" value="<?= $foto_lama ?>">
                <input type="hidden" name="id_galeri_1" value="<?= $galeri_photos[0]['id_galeri'] ?>">
                <input type="hidden" name="foto_galeri_lama_1" value="<?= $galeri_photos[0]['nama_foto'] ?>">
                <input type="hidden" name="id_galeri_2" value="<?= $galeri_photos[1]['id_galeri'] ?>">
                <input type="hidden" name="foto_galeri_lama_2" value="<?= $galeri_photos[1]['nama_foto'] ?>">

                <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Informasi Umum</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Wisata</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="Alam" <?= $kategori=='Alam'?'selected':'' ?>>Alam</option>
                            <option value="Keluarga" <?= $kategori=='Keluarga'?'selected':'' ?>>Keluarga</option>
                            <option value="Edukasi" <?= $kategori=='Edukasi'?'selected':'' ?>>Edukasi</option>
                            <option value="Sejarah" <?= $kategori=='Sejarah'?'selected':'' ?>>Sejarah</option>
                            <option value="Religi" <?= $kategori=='Religi'?'selected':'' ?>>Religi</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($kecamatan) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga Tiket (Rp)</label>
                        <input type="number" name="harga_tiket" class="form-control" value="<?= $harga_tiket ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Link Maps (URL)</label>
                        <input type="text" name="link_maps" class="form-control" value="<?= htmlspecialchars($link_maps) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No WA (Contoh: 628...)</label>
                        <input type="text" name="wa_number" class="form-control" value="<?= htmlspecialchars($wa_number) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($latitude ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($longitude ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($alamat) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($deskripsi) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Fasilitas</label>
                        <textarea name="fasilitas" class="form-control" rows="2"><?= htmlspecialchars($fasilitas) ?></textarea>
                    </div>
                </div>

                <h5 class="text-primary fw-bold mb-3 border-bottom pb-2 mt-5">Pengaturan Galeri Foto</h5>
                <div class="row g-4">
                    
                    <div class="col-md-4">
                        <div class="card h-100 border bg-light">
                            <div class="card-header fw-bold text-center bg-primary text-white">1. Foto Utama (Cover)</div>
                            <div class="card-body text-center">
                                <?php if($foto_lama): ?>
                                    <img src="../assets/img/<?= $foto_lama ?>" class="img-thumbnail mb-2 shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                <?php else: ?>
                                    <div class="alert alert-secondary py-4 mb-2">Belum ada foto</div>
                                <?php endif; ?>
                                <input type="file" name="foto_upload" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">*Foto ini muncul di halaman depan.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 border bg-light">
                            <div class="card-header fw-bold text-center bg-success text-white">2. Galeri Tambahan 1</div>
                            <div class="card-body text-center">
                                <?php if(!empty($galeri_photos[0]['nama_foto'])): ?>
                                    <img src="../assets/img_galeri/<?= $galeri_photos[0]['nama_foto'] ?>" class="img-thumbnail mb-2 shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                <?php else: ?>
                                    <div class="alert alert-secondary py-4 mb-2">Belum ada foto</div>
                                <?php endif; ?>
                                <input type="file" name="foto_galeri_1" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">*Foto tambahan untuk slider.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 border bg-light">
                            <div class="card-header fw-bold text-center bg-success text-white">3. Galeri Tambahan 2</div>
                            <div class="card-body text-center">
                                <?php if(!empty($galeri_photos[1]['nama_foto'])): ?>
                                    <img src="../assets/img_galeri/<?= $galeri_photos[1]['nama_foto'] ?>" class="img-thumbnail mb-2 shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                <?php else: ?>
                                    <div class="alert alert-secondary py-4 mb-2">Belum ada foto</div>
                                <?php endif; ?>
                                <input type="file" name="foto_galeri_2" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">*Foto tambahan untuk slider.</small>
                            </div>
                        </div>
                    </div>

                </div>

                <hr class="my-5">
                
                <div class="d-flex justify-content-end gap-2 pb-3">
                    <a href="index.php" class="btn btn-secondary px-4 py-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow">
                        <i class="bi bi-save me-2"></i> Simpan Semua Data
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once '../template/footer.php'; ?> 