<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $update = $conn->prepare("UPDATE permintaan_linen SET status_distribusi = 'sudah', tanggal_distribusi = NOW() WHERE id = ? AND status = 'approved'");
    $update->bind_param("i", $id);
    $update->execute();

    // Ambil data permintaan
    $data = $conn->query("SELECT * FROM permintaan_linen WHERE id = $id")->fetch_assoc();
    $unit = $data['unit_name'];
    $jenis = $data['jenis_linen'];
    $jumlah = $data['jumlah'];

    // Cek apakah stok unit sudah ada
    $cek = $conn->query("SELECT * FROM stock_unit WHERE unit_name = '$unit' AND jenis_linen = '$jenis'");
    if ($cek->num_rows > 0) {
        $conn->query("UPDATE stock_unit SET jumlah = jumlah + $jumlah WHERE unit_name = '$unit' AND jenis_linen = '$jenis'");
    } else {
        $conn->query("INSERT INTO stock_unit (unit_name, jenis_linen, jumlah) VALUES ('$unit', '$jenis', $jumlah)");
    }

    header("Location: admin_distribusi.php?msg=Distribusi sukses");
    exit;
}
