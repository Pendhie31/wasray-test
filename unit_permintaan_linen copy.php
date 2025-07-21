<?php
require 'includes/auth_unit.php';
require 'includes/db.php';

$jenisLinenOptions = ['laken', 'Stik', 'S Bantal', 'Selimut','Bedcover','Handuk','Perlak','Baju','Celana','jas pasien','Duk','Waslap','L tangan'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Permintaan Linen</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <style>
    .remove-btn { cursor: pointer; color: red; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Sidebar -->
  <?php include 'includes/template_sidebar_unit.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content pt-4">
      <div class="container-fluid">
        <h3 class="mb-4">Permintaan Linen</h3>

        <?php if (isset($_GET['msg'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

      <form action="submit_permintaan.php" method="POST" id="linenForm">
  <div id="formRepeater">
    <div class="row align-items-end linen-group mb-2">
      <div class="col-md-5">
        <label>Jenis Linen</label>
        <select name="jenis_linen[]" class="form-control" required>
          <option value="">-- Pilih Jenis --</option>
          <?php foreach ($jenisLinenOptions as $jenis): ?>
            <option value="<?= htmlspecialchars($jenis) ?>"><?= htmlspecialchars($jenis) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label>Jumlah</label>
        <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" required>
      </div>

      <div class="col-md-3">
        <label class="d-block">&nbsp;</label>
        <button type="button" class="btn btn-danger remove-row d-none w-100">
          <i class="fas fa-trash"></i> Hapus
        </button>
      </div>
    </div>
  </div>

  <button type="button" class="btn btn-secondary mb-3" id="addRow"><i class="fas fa-plus"></i> Tambah Item</button><br>
  <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Kirim Permintaan</button>
</form>

      </div>
    </section>
  </div>

</div>

<!-- Scripts -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
<!-- jQuery Wajib Disertakan Dulu -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Script Tambah Item -->
<script>
  $(document).ready(function () {
    $('#addRow').on('click', function () {
      let clone = $('.linen-group:first').clone();
      clone.find('input, select').val('');
      clone.find('.remove-row').removeClass('d-none');
      $('#formRepeater').append(clone);
    });

    $(document).on('click', '.remove-row', function () {
      $(this).closest('.linen-group').remove();
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#addRow').on('click', function () {
      let clone = $('.linen-group:first').clone();
      clone.find('input, select').val('');
      clone.find('.remove-row').removeClass('d-none');
      $('#formRepeater').append(clone);
    });

    $(document).on('click', '.remove-row', function () {
      $(this).closest('.linen-group').remove();
    });
  });
</script>

</body>
</html>
