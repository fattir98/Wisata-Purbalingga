<?php
require_once '../template/header.php';
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') exit;

$id = '';
$nama = ''; $deskripsi = ''; $alamat = ''; $kecamatan = ''; $kategori = 'Alam';
$harga_tiket = 0; $fasilitas = ''; $link_maps = ''; $wa_number = ''; $foto = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $q = mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata='$id'");
    $d = mysqli_fetch_assoc($q);
    // Isi variabel dengan data DB ...
    $nama = $d['nama']; $deskripsi = $d['deskripsi']; $alamat = $d['alamat']; 
    $kecamatan = $d['kecamatan']; $kategori = $d['kategori']; $harga_tiket = $d['harga_tiket'];
    $fasilitas = $d['fasilitas']; $link_maps = $d['link_maps']; $wa_number = $d['wa_number'];
    $foto = $d['foto'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data POST ...
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kategori = $_POST['kategori'];
    $harga_tiket = $_POST['harga_tiket'];
    $fasilitas = mysqli_real_escape_string($conn, $_POST['fasilitas']);
    $link_maps = mysqli_real_escape_string($conn, $_POST['link_maps']);
    $wa_number = mysqli_real_escape_string($conn, $_POST['wa_number']);
    
    // Cek Foto (Upload atau URL)
    $nama_foto = $foto;
    
    // Jika user mengisi URL foto manual
    if (!empty($_POST['foto_url'])) {
        $nama_foto = $_POST['foto_url'];
    }
    // Jika user upload file (prioritas lebih tinggi dari URL manual)
    if (!empty($_FILES['foto_upload']['name'])) {
        $nama_file = $_FILES['foto_upload']['name'];
        $tmp = $_FILES['foto_upload']['tmp_name'];
        $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_foto = time() . "." . $ext;
        move_uploaded_file($tmp, "../assets/img/" . $nama_foto);
    }

    if ($id) {
        $sql = "UPDATE wisata SET nama='$nama', deskripsi='$deskripsi', alamat='$alamat', kecamatan='$kecamatan', kategori='$kategori', harga_tiket='$harga_tiket', fasilitas='$fasilitas', link_maps='$link_maps', wa_number='$wa_number', foto='$nama_foto' WHERE id_wisata='$id'";
    } else {
        $sql = "INSERT INTO wisata (nama, deskripsi, alamat, kecamatan, kategori, harga_tiket, fasilitas, link_maps, wa_number, foto) VALUES ('$nama', '$deskripsi', '$alamat', '$kecamatan', '$kategori', '$harga_tiket', '$fasilitas', '$link_maps', '$wa_number', '$nama_foto')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Berhasil disimpan'); window.location='wisata_list.php';</script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= $id ? 'Edit' : 'Tambah' ?> Data Wisata</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Wisata</label>
                        <input type="text" name="nama" class="form-control" value="<?= $nama ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="Alam" <?= $kategori=='Alam'?'selected':'' ?>>Alam</option>
                            <option value="Keluarga" <?= $kategori=='Keluarga'?'selected':'' ?>>Keluarga</option>
                            <option value="Edukasi" <?= $kategori=='Edukasi'?'selected':'' ?>>Edukasi</option>
                            <option value="Sejarah" <?= $kategori=='Sejarah'?'selected':'' ?>>Sejarah</option>
                            <option value="Kuliner" <?= $kategori=='Kuliner'?'selected':'' ?>>Kuliner</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-control" value="<?= $kecamatan ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Harga Tiket</label>
                        <input type="number" name="harga_tiket" class="form-control" value="<?= $harga_tiket ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Link Maps</label>
                        <input type="text" name="link_maps" class="form-control" value="<?= $link_maps ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>No WA (628...)</label>
                        <input type="text" name="wa_number" class="form-control" value="<?= $wa_number ?>">
                    </div>
                    <div class="col-12 mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= $deskripsi ?></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"><?= $alamat ?></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label>Fasilitas</label>
                        <textarea name="fasilitas" class="form-control" rows="2"><?= $fasilitas ?></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label>Foto (Upload File ATAU URL)</label>
                        <?php if($foto): ?>
                            <div class="mb-2"><img src="<?= (strpos($foto, 'http') === 0) ? $foto : '../assets/img/'.$foto ?>" width="100"></div>
                        <?php endif; ?>
                        
                        <div class="input-group mb-2">
                            <span class="input-group-text">Upload</span>
                            <input type="file" name="foto_upload" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">URL</span>
                            <input type="text" name="foto_url" class="form-control" placeholder="https://..." value="<?= (strpos($foto, 'http') === 0) ? $foto : '' ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="wisata_list.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php require_once '../template/footer.php'; ?>l