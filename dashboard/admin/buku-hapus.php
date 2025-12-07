<?php
require '../../auth/auth_admin.php';
require '../../config/db.php';

$buku_id = $_GET['id'] ?? null;
if (!is_numeric($buku_id)) {
    header("Location: buku.php?error=ID tidak valid.");
    exit;
}

// Hapus cover
$stmt = $koneksi->prepare("SELECT cover_path FROM buku WHERE id = ?");
$stmt->bind_param("i", $buku_id);
$stmt->execute();
$cover = $stmt->get_result()->fetch_assoc();

if ($cover && $cover['cover_path'] && file_exists(__DIR__ . '/../../' . $cover['cover_path'])) {
    unlink(__DIR__ . '/../../' . $cover['cover_path']);
}

// Hapus dari database
$stmt2 = $koneksi->prepare("DELETE FROM buku WHERE id = ?");
$stmt2->bind_param("i", $buku_id);
$stmt2->execute();

header("Location: buku.php?pesan=Buku berhasil dihapus.");
exit;
?>