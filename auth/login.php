<?php
require_once '../config/db.php';

// Jika sudah login, redirect
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: " . $base_url . "/admin/index.php");
    } else {
        header("Location: " . $base_url . "/public/index.php");
    }
    exit;
}

$error = "";
// Default tab active
$active_tab = 'visitor'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['login_type'];
    
    if ($type == 'admin') {
        // LOGIN ADMIN (Cek Database)
        $active_tab = 'admin';
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password' AND role='admin'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['user_nama'] = $row['nama'];
            $_SESSION['user_role'] = 'admin';
            header("Location: " . $base_url . "/admin/index.php");
            exit;
        } else {
            $error = "Email atau password admin salah.";
        }
    } else {
        // LOGIN PENGUNJUNG (Simulasi Session)
        $active_tab = 'visitor';
        $nama = htmlspecialchars($_POST['nama_visitor']);
        if(!empty($nama)){
            $_SESSION['user_nama'] = $nama;
            $_SESSION['user_role'] = 'visitor'; // Role khusus visitor
            header("Location: " . $base_url . "/public/index.php");
            exit;
        } else {
            $error = "Nama harus diisi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Wisata Purbalingga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card shadow border-0" style="width: 400px;">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <ul class="nav nav-tabs card-header-tabs w-100" id="myTab" role="tablist">
            <li class="nav-item flex-fill text-center">
                <button class="nav-link <?= $active_tab=='visitor'?'active fw-bold':'' ?>" id="visitor-tab" data-bs-toggle="tab" data-bs-target="#visitor" type="button">Pengunjung</button>
            </li>
            <li class="nav-item flex-fill text-center">
                <button class="nav-link <?= $active_tab=='admin'?'active fw-bold':'' ?>" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button">Admin</button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-4">
        <?php if($error): ?>
            <div class="alert alert-danger small"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="tab-content" id="myTabContent">
            <!-- Form Visitor -->
            <div class="tab-pane fade <?= $active_tab=='visitor'?'show active':'' ?>" id="visitor">
                <h4 class="text-center mb-3">Halo, Kanca Plesir!</h4>
                <p class="text-center text-muted small mb-4">Masukan nama agar kami bisa menyapa Anda.</p>
                <form method="POST">
                    <input type="hidden" name="login_type" value="visitor">
                    <div class="mb-3">
                        <label>Nama Anda</label>
                        <input type="text" name="nama_visitor" class="form-control" placeholder="Contoh: Budi" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Masuk Sekarang</button>
                </form>
            </div>

            <!-- Form Admin -->
            <div class="tab-pane fade <?= $active_tab=='admin'?'show active':'' ?>" id="admin">
                <h4 class="text-center mb-3">Login Admin</h4>
                <form method="POST">
                    <input type="hidden" name="login_type" value="admin">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login Dashboard</button>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="<?= $base_url ?>/public/index.php" class="text-decoration-none small">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>