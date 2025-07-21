<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

// Ambil daftar unit unik dari permintaan_linen
$units = $conn->query("SELECT DISTINCT unit_name FROM permintaan_linen ORDER BY unit_name");

// Tangani filter unit jika dipilih
$filterUnit = isset($_GET['unit']) ? $_GET['unit'] : '';

// Query data permintaan linen harian
$sql = "SELECT unit_name, jenis_linen, SUM(jumlah) as total, DATE(tanggal_distribusi) as tanggal
        FROM permintaan_linen";

if ($filterUnit != '') {
    $sql .= " WHERE unit_name = '" . $conn->real_escape_string($filterUnit) . "'";
}

$sql .= " GROUP BY unit_name, jenis_linen, tanggal ORDER BY tanggal DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Rekap Permintaan Linen Harian</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <section class="content-header">
      <h3>Rekap Permintaan Linen Harian</h3>
    </section>

    <section class="content">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title m-0"><i class="fas fa-clipboard-list"></i> Data Permintaan</h5>
        </div>
        <div class="card-body">
          <form method="GET" class="form-inline mb-3">
            <label for="unit" class="mr-2">Filter Unit:</label>
            <select name="unit" id="unit" class="form-control mr-2">
              <option value="">Semua Unit</option>
              <?php while ($row = $units->fetch_assoc()) { ?>
                <option value="<?= $row['unit_name'] ?>" <?= $row['unit_name'] == $filterUnit ? 'selected' : '' ?>>
                  <?= $row['unit_name'] ?>
                </option>
              <?php } ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-hover text-sm">
              <thead class="thead-dark">
                <tr>
                  <th>Tanggal</th>
                  <th>Unit</th>
                  <th>Jenis Linen</th>
                  <th class="text-center">Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                  <tr>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= $row['unit_name'] ?></td>
                    <td><?= $row['jenis_linen'] ?></td>
                    <td class="text-center"><?= $row['total'] ?></td>
                  </tr>
                <?php } ?>
                <?php if ($result->num_rows == 0): ?>
                  <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </section>
  </div>
</div>

<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
