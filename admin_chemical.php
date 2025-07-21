<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

// Tambah Stok
if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
  $nama = $_POST['nama_sabun'];
  $jumlah_liter = (float) $_POST['jumlah_ml']; // dari input
  $jumlah = (int) ($jumlah_liter * 1000); // konversi ke ml

  if ($nama && $jumlah > 0) {
    $cek = $conn->prepare("SELECT id FROM stok_chemical WHERE nama_sabun = ?");
    $cek->bind_param("s", $nama);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
      $stmt = $conn->prepare("UPDATE stok_chemical SET jumlah_ml = jumlah_ml + ? WHERE nama_sabun = ?");
      $stmt->bind_param("is", $jumlah, $nama);
    } else {
      $stmt = $conn->prepare("INSERT INTO stok_chemical (nama_sabun, jumlah_ml) VALUES (?, ?)");
      $stmt->bind_param("si", $nama, $jumlah);
    }
    $stmt->execute();
    header("Location: admin_chemical.php?msg=Stok berhasil ditambahkan");
    exit;
  }
}

// Kurangi Stok (Pemakaian)
if (isset($_POST['aksi']) && $_POST['aksi'] === 'pakai') {
  $nama = $_POST['nama_sabun'];
  $pakai = (int) $_POST['jumlah_pakai'];

  $stmt = $conn->prepare("UPDATE stok_chemical SET jumlah_ml = jumlah_ml - ? WHERE nama_sabun = ? AND jumlah_ml >= ?");
  $stmt->bind_param("isi", $pakai, $nama, $pakai);
  if ($stmt->execute()) {
    $log = $conn->prepare("INSERT INTO penggunaan_chemical (nama_sabun, jumlah_pakai) VALUES (?, ?)");
    $log->bind_param("si", $nama, $pakai);
    $log->execute();
    header("Location: admin_chemical.php?msg=Stok dikurangi");
    exit;
  }
}

$stok = $conn->query("SELECT * FROM stok_chemical ORDER BY nama_sabun ASC");
$riwayat = $conn->query("SELECT * FROM penggunaan_chemical ORDER BY tanggal DESC LIMIT 10");

$nama_sabun_list = [
  'Aldet', 'Laudet', 'Omaxs', 'MD Pine', 'MC Bleach',
  'N Sour', 'M Soft', 'E951'
];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stok Chemical</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <h3 class="mb-4">Manajemen Stok Chemical</h3>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="row">
      <!-- TABEL STOK -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">Stok Sabun Saat Ini</h5>
          </div>
          <div class="card-body p-0">
            <table class="table table-bordered mb-0">
              <thead>
                <tr><th>Nama Sabun</th><th>Jumlah (liter)</th></tr>
              </thead>
              <tbody>
                <?php while ($row = $stok->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['nama_sabun']) ?></td>
                  <td><?= number_format($row['jumlah_ml'] / 1000, 2) ?> L</td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- FORM TAMBAH DAN PAKAI -->
      <div class="col-md-6">
        <!-- Tambah -->
        <div class="card mb-3">
          <div class="card-header bg-success text-white"><strong>Tambah Stok</strong></div>
          <form method="post" class="card-body">
            <input type="hidden" name="aksi" value="tambah">
            <div class="form-group">
              <label>Nama Sabun</label>
              <select name="nama_sabun" class="form-control" required>
                <option value="">-- Pilih Sabun --</option>
                <?php foreach ($nama_sabun_list as $s): ?>
                  <option value="<?= $s ?>"><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Jumlah (liter)</label>
              <input type="number" name="jumlah_ml" class="form-control" required min="0.1" step="0.1">
            </div>
            <button type="submit" class="btn btn-success btn-block">+ Tambah</button>
          </form>
        </div>

        <!-- Pakai -->
        <div class="card">
          <div class="card-header bg-warning text-white"><strong>Kurangi Stok (Pemakaian)</strong></div>
          <form method="post" class="card-body">
            <input type="hidden" name="aksi" value="pakai">
            <div class="form-group">
              <label>Nama Sabun</label>
              <select name="nama_sabun" class="form-control" required>
                <option value="">-- Pilih Sabun --</option>
                <?php foreach ($nama_sabun_list as $s): ?>
                  <option value="<?= $s ?>"><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Jumlah (ml)</label>
              <input type="number" name="jumlah_pakai" class="form-control" required min="1">
            </div>
            <button type="submit" class="btn btn-warning btn-block">- Kurangi</button>
          </form>
        </div>
      </div>
    </div>

    <!-- RIWAYAT -->
    <div class="card mt-4">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Riwayat Pemakaian Terakhir</h5>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped table-bordered mb-0">
          <thead><tr><th>Waktu</th><th>Nama Sabun</th><th>Jumlah Pakai (ml)</th></tr></thead>
          <tbody>
            <?php while ($row = $riwayat->fetch_assoc()): ?>
            <tr>
              <td><?= $row['tanggal'] ?></td>
              <td><?= $row['nama_sabun'] ?></td>
              <td><?= number_format($row['jumlah_pakai']) ?> ml</td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
