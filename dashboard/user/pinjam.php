<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['pesan'] = "Buku tidak valid.";
    header("Location: dashboard.php");
    exit;
}

$buku_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Cek apakah buku ada
$stmt = $koneksi->prepare("SELECT jumlah_stok, eksklusif_member FROM buku WHERE id = ?");
$stmt->bind_param("i", $buku_id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    $_SESSION['pesan'] = "Buku tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}

// Cek akses eksklusif
if ($buku['eksklusif_member']) {
    $stmt2 = $koneksi->prepare("SELECT role FROM users WHERE id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $user = $stmt2->get_result()->fetch_assoc();
    if ($user['role'] !== 'member') {
        $_SESSION['pesan'] = "Buku ini hanya untuk member.";
        header("Location: dashboard.php");
        exit;
    }
}

// Cek duplikat peminjaman
$stmt3 = $koneksi->prepare("SELECT id FROM peminjaman WHERE user_id = ? AND buku_id = ? AND status IN ('menunggu', 'dipinjam')");
$stmt3->bind_param("ii", $user_id, $buku_id);
$stmt3->execute();
if ($stmt3->get_result()->num_rows > 0) {
    $_SESSION['pesan'] = "Anda sudah memesan atau meminjam buku ini.";
    header("Location: dashboard.php");
    exit;
}

// Cek stok vs peminjaman aktif
$stmt4 = $koneksi->prepare("SELECT COUNT(*) AS total FROM peminjaman WHERE buku_id = ? AND status IN ('menunggu', 'dipinjam')");
$stmt4->bind_param("i", $buku_id);
$stmt4->execute();
$aktif = $stmt4->get_result()->fetch_assoc()['total'];

if ($aktif >= $buku['jumlah_stok']) {
    $_SESSION['pesan'] = "Maaf, buku ini sedang penuh.";
    header("Location: dashboard.php");
    exit;
}

// Ambil role untuk durasi
$stmt5 = $koneksi->prepare("SELECT role FROM users WHERE id = ?");
$stmt5->bind_param("i", $user_id);
$stmt5->execute();
$user = $stmt5->get_result()->fetch_assoc();
$role = $user['role'];
$durasi = ($role === 'member') ? 14 : 7;

// Buat reservasi
$stmt6 = $koneksi->prepare("INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, status, durasi_hari) VALUES (?, ?, CURDATE(), 'menunggu', ?)");
$stmt6->bind_param("iii", $user_id, $buku_id, $durasi);

if ($stmt6->execute()) {
    $_SESSION['pesan'] = "Reservasi berhasil! Silakan datang ke perpustakaan untuk mengambil buku.";
} else {
    $_SESSION['pesan'] = "Gagal membuat reservasi.";
}

header("Location: dashboard.php");
exit;
?>