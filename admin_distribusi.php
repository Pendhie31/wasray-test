<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

$result = $conn->query("SELECT * FROM permintaan_linen WHERE status_distribusi = 'belum' ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html>
<head>
  <title>Distribusi Linen</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <div class="content-header">
      <div class="container-fluid">
        <h3 class="mb-3">Distribusi Linen</h3>

        <?php if (isset($_GET['msg'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="card">
          <div class="card-header bg-info">
            <h5 class="card-title text-white"><i class="fas fa-truck"></i> Daftar Permintaan Disetujui</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered table-striped m-0">
                <thead class="thead-dark">
                  <tr>
                    <th>Tanggal</th>
                    <th>Unit</th>
                    <th>Jenis Linen</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['unit_name']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_linen']) ?></td>
                        <td><?= htmlspecialchars($row['jumlah']) ?></td>
                        <td>
                          <?php if ($row['status_distribusi'] === 'sudah'): ?>
                            <span class="badge badge-success">Terkirim</span>
                          <?php else: ?>
                            <span class="badge badge-warning">Menunggu Kirim</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($row['status_distribusi'] !== 'sudah'): ?>
                            <a href="submit_linen_bersih.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                              <i class="fas fa-paper-plane"></i> Kirim
                            </a>
                          <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-check"></i></button>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center">Belum ada permintaan yang disetujui.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
