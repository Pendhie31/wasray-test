<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

// Ambil semua unit unik untuk dropdown filter
$units = $conn->query("SELECT DISTINCT unit_name FROM stok_unit ORDER BY unit_name ASC");

// Cek apakah ada filter unit dipilih
$selectedUnit = isset($_GET['unit']) ? $_GET['unit'] : '';

// Query stok linen
if ($selectedUnit) {
    $stmt = $conn->prepare("SELECT * FROM stok_unit WHERE unit_name = ? ORDER BY jenis_linen ASC");
    $stmt->bind_param("s", $selectedUnit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM stok_unit ORDER BY unit_name ASC, jenis_linen ASC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Stok Linen Unit - Admin</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }

    .layout {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 220px;
      background: #f4f4f4;
      padding: 20px;
    }

    .sidebar h3 {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .sidebar ul {
      list-style: none;
      padding-left: 0;
    }

    .sidebar ul li {
      margin-bottom: 10px;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: #333;
      display: block;
    }

    .sidebar ul li a:hover {
      font-weight: bold;
      color: #00796b;
    }

    .content {
      flex: 1;
      padding: 30px;
      background-color: #ffffff;
    }

    .card-header {
      background-color: #00695c !important;
    }

    .card-title {
      font-weight: bold;
    }

    .badge-jumlah {
      background-color: #e0f7fa;
      color: #004d40;
      font-size: 1rem;
      padding: 6px 10px;
      border-radius: 6px;
    }

    .table th {
      background-color: #00796b;
      color: #fff;
    }

    .form-inline label {
      margin-right: 10px;
    }

    .alert {
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="layout">

  <!-- Sidebar -->
<div style="width: 200px; float: left; background: #f4f4f4; height: 100vh; padding: 20px;">
    <h3>Laundry Rsu Bhakti Asih</h3>
    <ul style="list-style: none; padding-left: 0;">
        <li><a href="dashboard_admin.php">Dashboard</a></li>
        <li><a href="admin_distribusi.php">Distribusi Linen</a></li>
        <li><a href="admin_linen_kotor.php">Daftar Linen Kotor</a></li>
        <li><a href="stok_gudang.php">Stok Gudang</a></li>
        <li><a href="admin_stock_unit.php">Stok per Unit</a></li>
        <li><a href="admin_chemical.php">Pemakaian Chemical</a></li>
        <li><a href="admin_laporan.php">Rekap Harian</a></li>
        <li><a href="admin_rekap_distribusi.php">Rekap Distribusi</a></li>
        <li><a href="admin_statistik.php">Statistik Permintaan</a></li>
        <li><a href="logout.php" style="color: red;">Logout</a></li>
    </ul>
</div>

  <!-- Konten Utama -->
  <div class="content">
    <h3 class="mb-3"><i class="fas fa-warehouse"></i> Stok Linen per Unit</h3>

    <!-- Filter Unit -->
    <form method="GET" class="form-inline mb-3">
      <label class="font-weight-bold">Filter Unit:</label>
      <select name="unit" class="form-control mx-2" onchange="this.form.submit()">
        <option value="">-- Semua Unit --</option>
        <?php while ($u = $units->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($u['unit_name']) ?>" <?= $selectedUnit == $u['unit_name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['unit_name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <?php if ($selectedUnit): ?>
        <a href="admin_stock_unit.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </form>

    <!-- Pesan -->
    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <!-- Data Stok -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title text-white"><i class="fas fa-layer-group"></i> Data Stok Unit</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-striped m-0">
            <thead>
              <tr>
                <th>Unit</th>
                <th>Jenis Linen</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['unit_name']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_linen']) ?></td>
                    <td><span class="badge-jumlah"><?= htmlspecialchars($row['jumlah']) ?></span></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center">Tidak ada data untuk unit ini.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- Script -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
