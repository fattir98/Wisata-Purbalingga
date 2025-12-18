<?php
require_once __DIR__ . '/../config/db.php';
// Tentukan link Beranda berdasarkan role
$beranda_link = '../public/index.php'; // default untuk visitor atau belum login
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
    $beranda_link = '../admin/index.php';
}
?>
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $beranda_link ?>">
        <i class="bi bi-geo-alt-fill me-2"></i>Wisata Purbalingga
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- Menu utama -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="<?= $beranda_link ?>">Beranda</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../public/wisata.php">Daftar Wisata</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../public/about.php">Profil</a>
        </li>
      </ul>

      <!-- Menu kanan -->
      <ul class="navbar-nav align-items-lg-center">

        <!-- Search Mobile -->
        <li class="nav-item d-lg-none">
             <a class="nav-link" href="../public/wisata.php">
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
                      <!-- Menu Admin -->
                      <li>
                        <a class="dropdown-item" href="../admin/booking_admin.php">
                          <i class="bi me-2"></i> Booking Admin
                        </a>
                      </li>
                  <?php else: ?>
                      <!-- Menu Pengunjung -->
                      <li><span class="dropdown-item-text text-muted small">Status: Pengunjung</span></li>
                      <li>
                        <a class="dropdown-item" href="../public/booking_list.php">
                          <i class="bi bi-journal-text me-2"></i> Booking Saya
                        </a>
                      </li>
                  <?php endif; ?>

                  <li><hr class="dropdown-divider"/></li>

                  <li>
                      <a class="dropdown-item text-danger" href="../auth/loguot.php"
                         onclick="return confirm('Anda yakin ingin logout?');">
                          Logout
                      </a>
                  </li>
              </ul>
          </li>
        <?php else: ?>
          <!-- Jika Belum Login -->
          <li class="nav-item">
              <a class="btn btn-outline-light btn-sm ms-lg-2 rounded-pill px-3" href="../auth/login.php">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main>
