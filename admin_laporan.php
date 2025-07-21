<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

// --- Data Permintaan Linen (Bulanan) ---
$monthlyQuery = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(jumlah) as total 
    FROM permintaan_linen 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
    GROUP BY bulan
");

$monthlyLabels = $monthlyData = [];
while ($row = $monthlyQuery->fetch_assoc()) {
    $monthlyLabels[] = $row['bulan'];
    $monthlyData[] = $row['total'];
}
$totalMonthly = array_sum($monthlyData);

// --- Data Linen Kotor (Bulanan) ---
$kotorMonthly = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(jumlah) as total 
    FROM linen_kotor 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
    GROUP BY bulan
");

$kotorLabels = $kotorData = [];
while ($row = $kotorMonthly->fetch_assoc()) {
    $kotorLabels[] = $row['bulan'];
    $kotorData[] = $row['total'];
}
$totalKotorMonthly = array_sum($kotorData);

// --- Data Pemakaian Chemical (Bulanan) ---
$chemicalQuery = $conn->query("
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, SUM(jumlah_pakai) AS total 
    FROM penggunaan_chemical 
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) 
    GROUP BY bulan
");

$chemicalLabels = $chemicalData = [];
while ($row = $chemicalQuery->fetch_assoc()) {
    $chemicalLabels[] = $row['bulan'];
    $chemicalData[] = $row['total'];
}
$totalChemical = array_sum($chemicalData);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Laporan Admin</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/adminlte/plugins/bootstrap/css/bootstrap.min.css">
  <style>
    .small-box .inner h3 {
      color: #000 !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Statistik Mingguan</h2>

    <div class="row mb-4">
      <div class="col-md-4">
        <div class="alert alert-info">
          <strong><i class="fas fa-truck"></i> Permintaan Bulan Ini:</strong> <?= $totalMonthly ?> linen
        </div>
      </div>
      <div class="col-md-4">
        <div class="alert alert-success">
          <strong><i class="fas fa-calendar-alt"></i> Permintaan Bulan Ini:</strong> <?= $totalMonthly ?> linen
        </div>
      </div>
      <div class="col-md-4">
        <div class="alert alert-warning">
          <strong><i class="fas fa-trash-alt"></i> Linen Kotor Bulan Ini:</strong> <?= $totalKotorMonthly ?> linen
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Permintaan Bulanan Chart -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <i class="fas fa-chart-bar"></i> Permintaan Linen (Bulanan)
          </div>
          <div class="card-body">
            <canvas id="permintaanChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Permintaan Bulanan Line Chart -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-success text-white">
            <i class="fas fa-chart-line"></i> Permintaan Linen (Bulanan)
          </div>
          <div class="card-body">
            <canvas id="permintaanChart2"></canvas>
          </div>
        </div>
      </div>

      <!-- Linen Kotor Bulanan Chart -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-warning text-dark">
            <i class="fas fa-dumpster"></i> Linen Kotor (Bulanan)
          </div>
          <div class="card-body">
            <canvas id="kotorChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Pemakaian Chemical -->
      <div class="col-md-4 mt-4">
        <div class="card">
          <div class="card-header bg-danger text-white">
            <i class="fas fa-vial"></i> Pemakaian Chemical (ml)
          </div>
          <div class="card-body">
            <canvas id="chemicalChart"></canvas>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- JS -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Permintaan Linen - Bar
  new Chart(document.getElementById('permintaanChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($monthlyLabels) ?>,
      datasets: [{
        label: 'Permintaan Linen',
        data: <?= json_encode($monthlyData) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)'
      }]
    }
  });

  // Permintaan Linen - Line
  new Chart(document.getElementById('permintaanChart2').getContext('2d'), {
    type: 'line',
    data: {
      labels: <?= json_encode($monthlyLabels) ?>,
      datasets: [{
        label: 'Permintaan Linen Bulanan',
        data: <?= json_encode($monthlyData) ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.3)',
        borderColor: 'rgba(75, 192, 192, 1)',
        fill: true,
        tension: 0.4
      }]
    }
  });

  // Linen Kotor
  new Chart(document.getElementById('kotorChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($kotorLabels) ?>,
      datasets: [{
        label: 'Linen Kotor',
        data: <?= json_encode($kotorData) ?>,
        backgroundColor: 'rgba(255, 206, 86, 0.5)',
        borderColor: 'orange',
        borderWidth: 2
      }]
    }
  });

  // Pemakaian Chemical
  new Chart(document.getElementById('chemicalChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($chemicalLabels) ?>,
      datasets: [{
        label: 'Chemical (ml)',
        data: <?= json_encode($chemicalData) ?>,
        backgroundColor: 'rgba(255, 99, 132, 0.5)',
        borderColor: 'red',
        borderWidth: 2
      }]
    }
  });
</script>

</body>
</html>
