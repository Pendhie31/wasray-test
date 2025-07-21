<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

// Ambil total permintaan per unit
$units = [];
$totals = [];

$sql = "SELECT unit_name, SUM(jumlah) as total FROM permintaan_linen GROUP BY unit_name";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $units[] = $row['unit_name'];
    $totals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Statistik Permintaan Linen</title>
  <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'includes/template_sidebar_admin.php'; ?>

  <div class="content-wrapper p-4">
    <h3>Statistik Permintaan Linen Semua Unit</h3>

    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        Grafik Permintaan per Unit
      </div>
      <div class="card-body">
        <canvas id="unitChart" height="120"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- JS & Chart -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/adminlte/dist/js/adminlte.min.js"></script>

<script>
  const ctx = document.getElementById('unitChart').getContext('2d');
  const unitChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($units) ?>,
      datasets: [{
        label: 'Total Permintaan Linen',
        data: <?= json_encode($totals) ?>,
        backgroundColor: 'rgba(40, 167, 69, 0.6)',
        borderColor: 'rgba(40, 167, 69, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      indexAxis: 'y', // Jika ingin horizontal bar chart, ubah jadi 'y'
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
</script>

</body>
</html>
