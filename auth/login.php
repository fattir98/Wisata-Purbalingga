<?php
// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php'; 

// Jika sudah login, lempar keluar
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../public/index.php");
    }
    exit;
}

$error = "";
$active_tab = 'visitor'; // default tab

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['login_type'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Tentukan role yang dicari di database
    $role_dicari = ($type === 'admin') ? 'admin' : 'pengunjung';
    if ($type === 'admin') $active_tab = 'admin';

    // Query User
    $query = "SELECT * FROM users WHERE email='$email' AND role='$role_dicari' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // 🔥 CEK PASSWORD (Mendukung Hash & Plaintext) 🔥
        // 1. Coba verifikasi hash dulu (untuk akun baru)
        // 2. Kalau gagal, coba cek plaintext (untuk akun lama/admin manual)
        $password_cocok = false;

        if (password_verify($password, $row['password'])) {
            $password_cocok = true;
        } elseif ($row['password'] === $password) {
            $password_cocok = true;
        }

        if ($password_cocok) {
            // Login Berhasil
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['user_nama'] = $row['nama'];
            $_SESSION['user_role'] = $row['role'];

            // 🔥 LOGIKA REDIRECT PINTAR 🔥
            if (isset($_SESSION['redirect_to'])) {
                $tujuan = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']); // Hapus jejak
                header("Location: " . $tujuan);
            } else {
                // Redirect Default
                if ($row['role'] === 'admin') {
                    header("Location: ../admin/index.php");
                } else {
                    header("Location: ../public/index.php");
                }
            }
            exit;

        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak terdaftar sebagai " . ucfirst($role_dicari);
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

<div class="card shadow border-0 rounded-4" style="width: 420px;">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 rounded-top-4">
        <h4 class="text-center fw-bold mb-4">Masuk ke Akun</h4>
        <ul class="nav nav-pills nav-fill mb-3">
            <li class="nav-item">
                <button class="nav-link <?= $active_tab=='visitor'?'active fw-bold':'' ?>" data-bs-toggle="tab" data-bs-target="#visitor">Pengunjung</button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?= $active_tab=='admin'?'active fw-bold':'' ?>" data-bs-toggle="tab" data-bs-target="#admin">Admin</button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4 pt-0">
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small text-center rounded-3 mb-3">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="tab-content">
            
            <div class="tab-pane fade <?= $active_tab=='visitor'?'show active':'' ?>" id="visitor">
                <form method="POST">
                    <input type="hidden" name="login_type" value="visitor">

                    <div class="mb-3">
                        <label class="form-label small text-muted">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="" required>
                    </div>

                    <button class="btn btn-primary w-100 py-2 fw-bold">Masuk Sekarang</button>

                    <p class="mt-4 small text-center text-muted">
                        Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold">Daftar dulu yuk!</a>
                    </p>
                </form>
            </div>

            <div class="tab-pane fade <?= $active_tab=='admin'?'show active':'' ?>" id="admin">
                <form method="POST">
                    <input type="hidden" name="login_type" value="admin">

                    <div class="alert alert-warning py-2 small border-0 text-center mb-3">
                        <i class="bi bi-lock-fill"></i> Area Khusus Pengelola
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Email Admin</label>
                        <input type="email" name="email" class="form-control" placeholder="" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Password Admin</label>
                        <input type="password" name="password" class="form-control" placeholder="" required>
                    </div>

                    <button class="btn btn-dark w-100 py-2 fw-bold">Login Admin</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>