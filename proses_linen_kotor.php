<?php
require 'includes/auth_admin.php';
require 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_linen_kotor.php?msg=ID tidak valid");
    exit;
}

$conn->query("UPDATE linen_kotor SET status = 'diproses' WHERE id = $id");

header("Location: admin_linen_kotor.php?msg=Linen kotor berhasil ditandai sebagai diproses");
exit;
?>
