<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

// Proses tambah stok
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = trim($_POST['jenis_linen']);
    $jumlah = (int)$_POST['jumlah'];

    if ($jenis !== '' && $jumlah > 0) {
        $cek = $conn->prepare("SELECT id FROM stok_gudang WHERE jenis_linen = ?");
        $cek->bind_param("s", $jenis);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE stok_gudang SET jumlah = jumlah + ? WHERE jenis_linen = ?");
            $stmt->bind_param("is", $jumlah, $jenis);
        } else {
            $stmt = $conn->prepare("INSERT INTO stok_gudang (jenis_linen, jumlah) VALUES (?, ?)");
            $stmt->bind_param("si", $jenis, $jumlah);
        }

        if ($stmt->execute()) {
            header("Location: admin_stok_gudang.php?msg=Stok berhasil ditambahkan");
            exit;
        } else {
            $error = "Gagal menambahkan stok.";
        }
    } else {
        $error = "Input tidak valid.";
    }
}

$result = $conn->query("SELECT * FROM stok_gudang ORDER BY jenis_linen ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stok Gudang</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <style>
    .table td, .table th, .card .inner h3, .card .inner p {
      color: black !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <div class="container-fluid">
      <h3 class="mb-3">Stok Linen Gudang</h3>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
      <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header bg-info d-flex justify-content-between align-items-center">
          <h5 class="card-title text-white mb-0"><i class="fas fa-boxes"></i> Daftar Stok Gudang</h5>
          <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Stok
          </button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-bordered table-striped m-0">
              <thead class="thead-dark">
                <tr>
                  <th>Jenis Linen</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['jenis_linen']) ?></td>
                      <td><?= number_format($row['jumlah']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="2" class="text-center">Belum ada data stok.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Modal Tambah -->
      <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <form method="post" class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTambahLabel">Tambah Stok Linen</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Jenis Linen</label>
                <input type="text" name="jenis_linen" class="form-control text-dark" required>
              </div>
              <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control text-dark" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
          </form>
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
