<?php
require 'includes/auth_admin.php';
require 'includes/db.php';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$clause_distribusi = '';
if ($from && $to) {
  $clause_distribusi = "AND created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'";
}

// Filtered total distribusi
$total_distribusi_filtered = $conn->query("SELECT COUNT(*) as total FROM permintaan_linen WHERE status_distribusi = 'sudah' $clause_distribusi")->fetch_assoc()['total'];

// Total data keseluruhan (tanpa filter)
$total_permintaan = $conn->query("SELECT COUNT(*) as total FROM permintaan_linen")->fetch_assoc()['total'];
$total_stok = $conn->query("SELECT SUM(jumlah) as total FROM stok_gudang")->fetch_assoc()['total'];
$total_kotor = $conn->query("SELECT SUM(jumlah) as total FROM linen_kotor")->fetch_assoc()['total'];
?>


<!DOCTYPE html>
<html>
<head>
  <title>Laundry Rsu Bhakti Asih</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- CSS -->
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/custom.css?v=<?= time(); ?>">


  <style>
    body {
      background-color: #f4f6f9;
    }
    .content-wrapper {
      background-color: #ffffff;
      border-radius: 8px;
      padding: 20px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<?php include 'includes/template_sidebar_admin.php'; ?>
<div class="content-wrapper p-4">
    <section class="content-header">
      <div class="container-fluid">
        <h3 class="mb-3">Laundry Rsu Bhakti Asih</h3>

        <!-- Filter Form -->
        <form class="row g-3 mb-4" method="get">
  <div class="col-md-3">
    <label for="from" class="form-label">Dari:</label>
    <input type="date" class="form-control" id="from" name="from" value="<?= htmlspecialchars($from) ?>" required>
  </div>
  <div class="col-md-3">
    <label for="to" class="form-label">Sampai:</label>
    <input type="date" class="form-control" id="to" name="to" value="<?= htmlspecialchars($to) ?>" required>
  </div>
  <div class="col-md-3 d-flex align-items-end">
    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter"></i> Filter</button>
    <a href="admin_laporan.php" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
  </div>
</form>


        <!-- Info Boxes -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $total_permintaan ?></h3>
                <p>Total Permintaan</p>
              </div>
              <div class="icon">
                <i class="fas fa-clipboard-list"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $total_distribusi_filtered ?></h3>
                <p>Distribusi Terkirim (Filter)</p>
              </div>
              <div class="icon">
                <i class="fas fa-truck"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $total_stok ?></h3>
                <p>Total Stok Gudang</p>
              </div>
              <div class="icon">
                <i class="fas fa-boxes"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $total_kotor ?></h3>
                <p>Total Linen Kotor</p>
              </div>
              <div class="icon">
                <i class="fas fa-soap"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Table Distribusi Detail -->
        <div class="card mt-4">
          <div class="card-header bg-secondary text-white">
            <h5 class="card-title"><i class="fas fa-table"></i> Daftar Distribusi Terkirim</h5>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-striped m-0">
              <thead class="thead-dark">
                <tr>
                  <th>Tanggal</th>
                  <th>Unit</th>
                  <th>Jenis Linen</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query_detail = "SELECT * FROM permintaan_linen WHERE status_distribusi = 'sudah' $clause_distribusi ORDER BY created_at DESC";
                $result_detail = $conn->query($query_detail);

                if ($result_detail->num_rows > 0):
                  while ($row = $result_detail->fetch_assoc()):
                ?>
                  <tr>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['unit_name']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_linen']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah']) ?></td>
                  </tr>
                <?php endwhile; else: ?>
                  <tr><td colspan="4" class="text-center">Tidak ada data distribusi dalam rentang tanggal tersebut.</td></tr>
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
