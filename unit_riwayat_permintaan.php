<?php
require 'includes/auth_unit.php';
require 'includes/db.php';

// Ambil username dari session
$username = $_SESSION['user']['username'];

// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT * FROM permintaan_linen WHERE unit_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Riwayat Permintaan Linen</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'includes/template_sidebar_unit.php'; ?>

  <div class="content-wrapper p-4">
    <h3>Riwayat Permintaan Linen</h3>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card mt-4">
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-dark">
            <tr>
              <th>Tanggal</th>
              <th>Jenis Linen</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th>Distribusi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                  <td><?= htmlspecialchars($row['jenis_linen']) ?></td>
                  <td><?= (int)$row['jumlah'] ?></td>
                  <td>
                    <?php
                      switch ($row['status']) {
                        case 'pending':
                          echo '<span class="badge badge-warning">Menunggu</span>';
                          break;
                        case 'approved':
                          echo '<span class="badge badge-success">Disetujui</span>';
                          break;
                        case 'rejected':
                          echo '<span class="badge badge-danger">Ditolak</span>';
                          break;
                        default:
                          echo '<span class="badge badge-secondary">-</span>';
                      }
                    ?>
                  </td>
                  <td>
                    <?php if ($row['status_distribusi'] === 'sudah'): ?>
                      <span class="badge badge-info">Terkirim</span>
                    <?php else: ?>
                      <span class="badge badge-light">Belum</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">Belum ada data permintaan.</td></tr>
            <?php endif; ?>
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
