<?php require_once __DIR__ . '/../config/db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Purbalingga</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $base_url ?>/public/index.php">
        <i class="bi bi-geo-alt-fill me-2"></i>Wisata Purbalingga
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/public/index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/public/wisata.php">Daftar Wisata</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>/public/about.php">Tentang</a></li>
      </ul>
      <ul class="navbar-nav align-items-lg-center">
        <!-- Search Mobile -->
        <li class="nav-item d-lg-none">
             <a class="nav-link" href="<?= $base_url ?>/public/wisata.php">
                <i class="bi bi-search me-1"></i> Cari Wisata
             </a>
        </li>

        <?php if (isset($_SESSION['user_role'])): ?>
            <!-- Jika Login (Admin atau Visitor) -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> Halo, <?= htmlspecialchars($_SESSION['user_nama']) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/admin/index.php">Dashboard Admin</a></li>
                    <?php else: ?>
                        <li><span class="dropdown-item-text text-muted small">Status: Pengunjung</span></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"/></li>
                    <li><a class="dropdown-item text-danger" href="<?= $base_url ?>/auth/logout.php">Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <!-- Jika Belum Login -->
            <li class="nav-item">
                <a class="btn btn-outline-light btn-sm ms-lg-2 rounded-pill px-3" href="<?= $base_url ?>/auth/login.php">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main>