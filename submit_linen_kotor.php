<?php
require 'includes/auth_unit.php';
require 'includes/db.php';

$jenis = $_POST['jenis_linen'] ?? '';
$jumlah = (int) ($_POST['jumlah'] ?? 0);
$unit = $_POST['unit_name'] ?? '';

if ($jenis && $jumlah > 0 && $unit) {
    $stmt = $conn->prepare("INSERT INTO linen_kotor (unit_name, jenis_linen, jumlah, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssi", $unit, $jenis, $jumlah);
    
    if ($stmt->execute()) {
        header("Location: input_linen_kotor.php?msg=Linen kotor berhasil dikirim.");
        exit;
    } else {
        header("Location: input_linen_kotor.php?msg=Gagal menyimpan data.");
        exit;
    }
} else {
    header("Location: input_linen_kotor.php?msg=Data tidak lengkap.");
    exit;
}
