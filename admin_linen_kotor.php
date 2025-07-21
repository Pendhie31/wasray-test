<?php
require 'includes/auth_admin.php';
require 'includes/db.php';


$result = $conn->query("SELECT * FROM linen_kotor ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Linen Kotor - Admin</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f9f0f0ff;
      font-family: 'Segoe UI', sans-serif;
    }

    .layout {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 220px;
      background: rgba(245, 239, 238, 1);
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
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <h3 class="mb-3">Daftar Linen Kotor</h3>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header bg-danger">
        <h5 class="card-title text-white"><i class="fas fa-soap"></i> Linen Kotor Masuk</h5>
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
                    <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                    <td><?= htmlspecialchars($row['unit_name']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_linen']) ?></td>
                    <td><?= (int)$row['jumlah'] ?></td>
                    <td>
                      <?php if ($row['status'] === 'diproses'): ?>
                        <span class="badge badge-success">Sudah Diproses</span>
                      <?php else: ?>
                        <span class="badge badge-warning">Belum Diproses</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($row['status'] === 'belum'): ?>
                        <a href="proses_linen_kotor.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                          <i class="fas fa-check-circle"></i> Proses
                        </a>
                      <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-check"></i></button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center">Belum ada data linen kotor.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
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
