<?php
require '../../auth/auth_admin.php';
require '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['pesan'] = "ID tidak valid.";
    header("Location: index.php");
    exit;
}

$pinjam_id = (int)$_GET['id'];
$action = $_GET['action'] ?? 'pinjam';

$stmt = $koneksi->prepare("
    SELECT p.id, p.user_id, p.buku_id, p.status, u.role
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $pinjam_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    $_SESSION['pesan'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$koneksi->autocommit(false);
$success = false;

if ($action === 'pinjam' && $data['status'] === 'menunggu') {
    // Tentukan durasi berdasarkan role
    $durasi = ($data['role'] === 'member') ? 14 : 7;
    
    // Hitung tanggal kembali
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime("+$durasi days", strtotime($tanggal_pinjam)));

    // Update peminjaman: isi status, tanggal_pinjam, tanggal_kembali, durasi_hari
    $stmt1 = $koneksi->prepare("UPDATE peminjaman SET status = 'dipinjam', tanggal_pinjam = ?, tanggal_kembali = ?, durasi_hari = ? WHERE id = ?");
    $stmt1->bind_param("ssii", $tanggal_pinjam, $tanggal_kembali, $durasi, $pinjam_id);
    $ok1 = $stmt1->execute();

    // Kurangi stok buku
    $stmt2 = $koneksi->prepare("UPDATE buku SET jumlah_stok = jumlah_stok - 1 WHERE id = ?");
    $stmt2->bind_param("i", $data['buku_id']);
    $ok2 = $stmt2->execute();

    $success = $ok1 && $ok2;

} elseif ($action === 'kembali' && $data['status'] === 'dipinjam') {
    // Hanya isi tanggal_kembali = hari ini saat pengembalian
    $stmt1 = $koneksi->prepare("UPDATE peminjaman SET status = 'dikembalikan', tanggal_kembali = CURDATE() WHERE id = ?");
    $stmt1->bind_param("i", $pinjam_id);
    $ok1 = $stmt1->execute();

    $stmt2 = $koneksi->prepare("UPDATE buku SET jumlah_stok = jumlah_stok + 1 WHERE id = ?");
    $stmt2->bind_param("i", $data['buku_id']);
    $ok2 = $stmt2->execute();

    $success = $ok1 && $ok2;
}

if ($success) {
    $koneksi->commit();
    $_SESSION['pesan'] = "Berhasil diperbarui!";
} else {
    $koneksi->rollback();
    $_SESSION['pesan'] = "Gagal memperbarui.";
}
$koneksi->autocommit(true);

header("Location: index.php");
exit;
?><?php
require '../../auth/auth_admin.php';
require '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['pesan'] = "ID tidak valid.";
    header("Location: index.php");
    exit;
}

$pinjam_id = (int)$_GET['id'];
$action = $_GET['action'] ?? 'pinjam';

$stmt = $koneksi->prepare("
    SELECT p.id, p.user_id, p.buku_id, p.status, u.role
    FROM peminjaman p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $pinjam_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    $_SESSION['pesan'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

$koneksi->autocommit(false);
$success = false;

if ($action === 'pinjam' && $data['status'] === 'menunggu') {
    // Tentukan durasi berdasarkan role
    $durasi = ($data['role'] === 'member') ? 14 : 7;
    
    // Hitung tanggal kembali
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime("+$durasi days", strtotime($tanggal_pinjam)));

    // Update peminjaman: isi status, tanggal_pinjam, tanggal_kembali, durasi_hari
    $stmt1 = $koneksi->prepare("UPDATE peminjaman SET status = 'dipinjam', tanggal_pinjam = ?, tanggal_kembali = ?, durasi_hari = ? WHERE id = ?");
    $stmt1->bind_param("ssii", $tanggal_pinjam, $tanggal_kembali, $durasi, $pinjam_id);
    $ok1 = $stmt1->execute();

    // Kurangi stok buku
    $stmt2 = $koneksi->prepare("UPDATE buku SET jumlah_stok = jumlah_stok - 1 WHERE id = ?");
    $stmt2->bind_param("i", $data['buku_id']);
    $ok2 = $stmt2->execute();

    $success = $ok1 && $ok2;

} elseif ($action === 'kembali' && $data['status'] === 'dipinjam') {
    // Hanya isi tanggal_kembali = hari ini saat pengembalian
    $stmt1 = $koneksi->prepare("UPDATE peminjaman SET status = 'dikembalikan', tanggal_kembali = CURDATE() WHERE id = ?");
    $stmt1->bind_param("i", $pinjam_id);
    $ok1 = $stmt1->execute();

    $stmt2 = $koneksi->prepare("UPDATE buku SET jumlah_stok = jumlah_stok + 1 WHERE id = ?");
    $stmt2->bind_param("i", $data['buku_id']);
    $ok2 = $stmt2->execute();

    $success = $ok1 && $ok2;
}

if ($success) {
    $koneksi->commit();
    $_SESSION['pesan'] = "Berhasil diperbarui!";
} else {
    $koneksi->rollback();
    $_SESSION['pesan'] = "Gagal memperbarui.";
}
$koneksi->autocommit(true);

header("Location: index.php");
exit;
?>