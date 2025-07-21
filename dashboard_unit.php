<?php
require 'includes/auth_unit.php';
require 'includes/db.php';

$unit_name = $_SESSION['username'] ?? '';

// Ambil data ringkasan
$jumlah_permintaan = $conn->query("SELECT COUNT(*) as total FROM permintaan_linen WHERE unit_name = '$unit_name'")->fetch_assoc()['total'] ?? 0;
$jumlah_linen_kotor = $conn->query("SELECT COUNT(*) as total FROM linen_kotor WHERE unit_name = '$unit_name'")->fetch_assoc()['total'] ?? 0;
$jumlah_stok_unit = $conn->query("SELECT SUM(jumlah) as total FROM stok_unit WHERE unit_name = '$unit_name'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Unit</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?php include 'includes/template_sidebar_unit.php'; ?>

  <div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <h3 class="mb-2">Dashboard Unit: <strong><?= htmlspecialchars($unit_name) ?></strong></h3>
      </div>
    </div>

    <!-- Konten Utama -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- Total Permintaan -->
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $jumlah_permintaan ?></h3>
                <p>Total Permintaan</p>
              </div>
              <div class="icon">
                <i class="fas fa-paper-plane"></i>
              </div>
              <a href="unit_riwayat_permintaan.php" class="small-box-footer">
                Lihat Riwayat <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <!-- Linen Kotor -->
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $jumlah_linen_kotor ?></h3>
                <p>Linen Kotor Dilaporkan</p>
              </div>
              <div class="icon">
                <i class="fas fa-soap"></i>
              </div>
              <a href="input_linen_kotor.php" class="small-box-footer">
                Laporkan Lagi <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <!-- Stok Unit -->
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $jumlah_stok_unit ?></h3>
                <p>Total Stok Unit</p>
              </div>
              <div class="icon">
                <i class="fas fa-boxes"></i>
              </div>
              <a href="unit_riwayat_permintaan.php" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</div>

<!-- Scripts -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
