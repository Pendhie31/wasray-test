<?php
require 'includes/auth_unit.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit = $_SESSION['user']['username'];
    $jenisArray = $_POST['jenis_linen'];
    $jumlahArray = $_POST['jumlah'];

    $stmt = $conn->prepare("INSERT INTO permintaan_linen (unit_name, jenis_linen, jumlah, status, status_distribusi, created_at) VALUES (?, ?, ?, 'approved', 'belum', NOW())");

    foreach ($jenisArray as $i => $jenis) {
        $jumlah = (int)$jumlahArray[$i];

        // Validasi input
        if (!empty($jenis) && $jumlah > 0) {
            $stmt->bind_param("ssi", $unit, $jenis, $jumlah);
            $stmt->execute();
        }
    }

    header("Location: unit_riwayat_permintaan.php?msg=Sukses");
    exit;
}
?>
