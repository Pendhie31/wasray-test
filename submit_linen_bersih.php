<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: admin_distribusi.php?msg=ID tidak valid");
    exit;
}

// Ambil data permintaan
$stmt = $conn->prepare("SELECT * FROM permintaan_linen WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: admin_distribusi.php?msg=Permintaan tidak ditemukan");
    exit;
}

$jenis = $conn->real_escape_string($data['jenis_linen']);
$jumlah = (int)$data['jumlah'];
$unit = $conn->real_escape_string($data['unit_name']);

// Cek stok gudang cukup
$stokCheck = $conn->query("SELECT jumlah FROM stok_gudang WHERE jenis_linen = '$jenis'");
$stokRow = $stokCheck->fetch_assoc();

if (!$stokRow || $stokRow['jumlah'] < $jumlah) {
    header("Location: admin_distribusi.php?msg=Stok gudang tidak mencukupi");
    exit;
}

// Kurangi stok gudang
$conn->query("UPDATE stok_gudang SET jumlah = jumlah - $jumlah WHERE jenis_linen = '$jenis'");

// Tambahkan ke stok unit
$cek = $conn->query("SELECT * FROM stok_unit WHERE unit_name = '$unit' AND jenis_linen = '$jenis'");
if ($cek->num_rows > 0) {
    $conn->query("UPDATE stok_unit SET jumlah = jumlah + $jumlah WHERE unit_name = '$unit' AND jenis_linen = '$jenis'");
} else {
    $conn->query("INSERT INTO stok_unit (unit_name, jenis_linen, jumlah) VALUES ('$unit', '$jenis', $jumlah)");
}

// Update status permintaan
$conn->query("UPDATE permintaan_linen SET status_distribusi = 'sudah', tanggal_distribusi = NOW() WHERE id = $id");

header("Location: admin_distribusi.php?msg=Linen berhasil dikirim ke $unit");
exit;
?>
