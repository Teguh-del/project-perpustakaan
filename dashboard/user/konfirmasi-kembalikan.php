<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['pesan'] = "ID tidak valid.";
    header("Location: dashboard.php");
    exit;
}

$pinjam_id = (int)$_GET['id'];

// Pastikan peminjaman milik user & status = dipinjam
$stmt = $koneksi->prepare("SELECT user_id, buku_id FROM peminjaman WHERE id = ? AND status = 'dipinjam'");
$stmt->bind_param("i", $pinjam_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data || $data['user_id'] != $_SESSION['user_id']) {
    $_SESSION['pesan'] = "Tidak berhak.";
    header("Location: dashboard.php");
    exit;
}

// Update status & stok
$koneksi->query("UPDATE peminjaman SET status = 'dikembalikan', tanggal_kembali = CURDATE() WHERE id = $pinjam_id");
$koneksi->query("UPDATE buku SET jumlah_stok = jumlah_stok + 1 WHERE id = {$data['buku_id']}");

$_SESSION['pesan'] = "Buku berhasil dikembalikan!";
header("Location: dashboard.php");
exit;
?>