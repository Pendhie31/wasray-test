<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user']['username'])) {
  die('Akses ditolak. Silakan login sebagai unit.');
}

$username = $_SESSION['user']['username'];

// Ambil daftar jenis linen dari database
$jenisLinenList = [];
$res = $conn->query("SELECT nama_jenis FROM jenis_linen ORDER BY nama_jenis ASC");
while ($row = $res->fetch_assoc()) {
  $jenisLinenList[] = $row['nama_jenis'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jenis_arr = $_POST['jenis_linen'];
  $jumlah_arr = $_POST['jumlah'];

  $stmt_linen = $conn->prepare("INSERT INTO linen_kotor (unit_name, jenis_linen, jumlah, created_at, status) VALUES (?, ?, ?, NOW(), 'belum')");

  foreach ($jenis_arr as $i => $jenis) {
    $jenis = trim($jenis);
    $jumlah = (int)$jumlah_arr[$i];

    if ($jenis !== '' && $jumlah > 0) {
      $stmt_linen->bind_param("ssi", $username, $jenis, $jumlah);
      $stmt_linen->execute();

      // Tambah ke stok gudang
      $cek = $conn->prepare("SELECT id FROM stok_gudang WHERE jenis_linen = ?");
      $cek->bind_param("s", $jenis);
      $cek->execute();
      $cek->store_result();

      if ($cek->num_rows > 0) {
        $update = $conn->prepare("UPDATE stok_gudang SET jumlah = jumlah + ? WHERE jenis_linen = ?");
        $update->bind_param("is", $jumlah, $jenis);
        $update->execute();
      } else {
        $insert = $conn->prepare("INSERT INTO stok_gudang (jenis_linen, jumlah) VALUES (?, ?)");
        $insert->bind_param("si", $jenis, $jumlah);
        $insert->execute();
      }
    }
  }

  header("Location: input_linen_kotor.php?msg=Data berhasil dikirim");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Input Linen Kotor</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <style>
    .remove-btn { cursor: pointer; color: red; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'includes/template_sidebar_unit.php'; ?>

  <div class="content-wrapper p-4">
    <h3>Input Linen Kotor</h3>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card mt-4 col-md-10">
      <div class="card-body">
        <form method="POST">
          <div id="linen-fields">
            <div class="form-row mb-2 linen-entry">
              <div class="col">
                <select name="jenis_linen[]" class="form-control" required>
                  <option value="">-- Pilih Jenis Linen --</option>
                  <?php foreach ($jenisLinenList as $jenis): ?>
                    <option value="<?= htmlspecialchars($jenis) ?>"><?= htmlspecialchars($jenis) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col">
                <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-danger btn-remove"><i class="fas fa-times"></i></button>
              </div>
            </div>
          </div>
          <button type="button" id="add-row" class="btn btn-secondary mb-3"><i class="fas fa-plus"></i> Tambah Baris</button>
          <br>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
<script>
  const dropdownHTML = `<?php
    $html = '<option value="">-- Pilih Jenis Linen --</option>';
    foreach ($jenisLinenList as $jenis) {
      $html .= '<option value="' . htmlspecialchars($jenis) . '">' . htmlspecialchars($jenis) . '</option>';
    }
    echo addslashes($html);
  ?>`;

  document.getElementById('add-row').addEventListener('click', function () {
    const container = document.getElementById('linen-fields');
    const newRow = document.createElement('div');
    newRow.className = 'form-row mb-2 linen-entry';
    newRow.innerHTML = `
      <div class="col">
        <select name="jenis_linen[]" class="form-control" required>${dropdownHTML}</select>
      </div>
      <div class="col">
        <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-danger btn-remove"><i class="fas fa-times"></i></button>
      </div>`;
    container.appendChild(newRow);
  });

  document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-remove')) {
      const row = e.target.closest('.linen-entry');
      if (document.querySelectorAll('.linen-entry').length > 1) {
        row.remove();
      }
    }
  });
</script>
</body>
</html>
