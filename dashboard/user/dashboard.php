<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../../config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $koneksi->prepare("SELECT nama, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Ambil SEMUA peminjaman: menunggu + dipinjam
$stmt2 = $koneksi->prepare("
    SELECT p.id, b.judul, b.penulis, p.tanggal_kembali, p.status
    FROM peminjaman p
    JOIN buku b ON p.buku_id = b.id
    WHERE p.user_id = ? AND p.status IN ('menunggu', 'dipinjam')
    ORDER BY p.id DESC
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$peminjaman = $stmt2->get_result();

// Rekomendasi
$rekomendasi = $koneksi->query("
    SELECT b.id, b.judul, b.penulis, b.cover_path
    FROM buku b
    JOIN peminjaman p ON b.id = p.buku_id
    GROUP BY b.id
    ORDER BY COUNT(*) DESC
    LIMIT 3
");

// Katalog
$role = $user['role'];
if ($role === 'member') {
    $katalog = $koneksi->query("SELECT id, judul, penulis, cover_path FROM buku WHERE jumlah_stok > 0 ORDER BY judul");
} else {
    $katalog = $koneksi->query("SELECT id, judul, penulis, cover_path FROM buku WHERE jumlah_stok > 0 AND eksklusif_member = 0 ORDER BY judul");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - Perpustakaan Mini</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50">
  <!-- Header -->
  <header class="bg-white shadow-sm border-b">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 1a2 2 0 0 1 2 2v2H6V3a2 2 0 0 1 2-2zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
            <path d="M6.5 9a2 2 0 0 1 3 0v6H6.5v-6z"/>
          </svg>
        </div>
        <div>
          <h2 class="text-sm font-medium">Halo, <?= htmlspecialchars($user['nama']) ?>!</h2>
          <p class="text-xs text-gray-500">Selamat datang kembali</p>
        </div>
      </div>
      <a href="../logout.php" class="border border-red-900 text-red-900 hover:bg-red-50 text-sm px-3 py-1 rounded-lg flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-1">
          <path d="M16 8A8 8 0 110 8a8 8 0 0116 0z"/>
          <path d="M7.5 7.5L6 6 4.5 7.5 6 9z"/>
          <path d="M10 6.5H6V8h4v-1.5z"/>
        </svg>
        Logout
      </a>
    </div>
  </header>

  <main class="container mx-auto px-4 py-6">
    <?php if (isset($_SESSION['pesan'])): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
        <?= htmlspecialchars($_SESSION['pesan']) ?>
      </div>
      <?php unset($_SESSION['pesan']); ?>
    <?php endif; ?>

    <!-- Status Anggota -->
    <div class="bg-gradient-to-br from-red-900 to-red-800 text-white rounded-xl p-6 shadow mb-6">
      <div class="flex justify-between items-start">
        <div>
          <h3 class="font-bold text-lg">Status Anggota</h3>
          <p class="text-sm opacity-90">Akun: <?= ucfirst($user['role']) ?></p>
        </div>
        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-2-2v-4h2v4l2 2H10zm6-2l-2-2V8h2v6l2 2H16z"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
      <div class="flex overflow-x-auto gap-2 pb-1">
        <button data-tab="riwayat" class="px-4 py-2 bg-red-900 text-white rounded-lg text-sm font-medium tab-btn">Riwayat</button>
        <button data-tab="rekomendasi" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium tab-btn">Rekomendasi</button>
        <button data-tab="katalog" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium tab-btn">Katalog</button>
      </div>
    </div>

    <!-- Riwayat -->
    <section id="riwayat" class="tab-content">
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Riwayat Peminjaman</h3>
        <?php if ($peminjaman && $peminjaman->num_rows > 0): ?>
          <?php while ($pinjam = $peminjaman->fetch_assoc()): ?>
            <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0">
              <div>
                <h4 class="font-medium"><?= htmlspecialchars($pinjam['judul']) ?></h4>
                <?php if ($pinjam['status'] === 'menunggu'): ?>
                  <p class="text-sm text-gray-600">Jatuh tempo: Belum dikonfirmasi</p>
                <?php else: ?>
                  <p class="text-sm text-gray-600">Jatuh tempo: <?= $pinjam['tanggal_kembali'] ?></p>
                <?php endif; ?>
                <span class="text-xs font-medium px-2 py-0.5 rounded <?= 
                  $pinjam['status'] === 'menunggu' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' 
                ?>">
                  <?= ucfirst($pinjam['status']) ?>
                </span>
              </div>
              <span class="text-xs text-gray-500">Konfirmasi oleh admin</span>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-gray-500">Belum ada peminjaman.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Rekomendasi -->
    <section id="rekomendasi" class="tab-content hidden">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if ($rekomendasi && $rekomendasi->num_rows > 0): ?>
          <?php while ($buku = $rekomendasi->fetch_assoc()): ?>
            <div class="bg-white rounded-xl shadow overflow-hidden">
              <div class="h-48 bg-gray-100 flex items-center justify-center">
                <?php if ($buku['cover_path'] && file_exists('../../' . $buku['cover_path'])): ?>
                  <img src="../../<?= htmlspecialchars($buku['cover_path']) ?>" class="w-full h-full object-cover" />
                <?php else: ?>
                  <div class="text-gray-400 text-center p-4">No Cover</div>
                <?php endif; ?>
              </div>
              <div class="p-4">
                <h4 class="font-bold"><?= htmlspecialchars($buku['judul']) ?></h4>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($buku['penulis']) ?></p>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="col-span-3 text-center text-gray-500">Belum ada rekomendasi.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Katalog -->
    <section id="katalog" class="tab-content hidden">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if ($katalog && $katalog->num_rows > 0): ?>
          <?php while ($buku = $katalog->fetch_assoc()): ?>
            <div class="bg-white rounded-xl shadow overflow-hidden">
              <div class="h-48 bg-gray-100 flex items-center justify-center">
                <?php if ($buku['cover_path'] && file_exists('../../' . $buku['cover_path'])): ?>
                  <img src="../../<?= htmlspecialchars($buku['cover_path']) ?>" class="w-full h-full object-cover" />
                <?php else: ?>
                  <div class="text-gray-400 text-center p-4">No Cover</div>
                <?php endif; ?>
              </div>
              <div class="p-4">
                <h4 class="font-bold"><?= htmlspecialchars($buku['judul']) ?></h4>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($buku['penulis']) ?></p>
                <div class="mt-3">
                  <a href="pinjam.php?id=<?= $buku['id'] ?>" class="w-full bg-red-900 text-white text-center py-2 rounded-lg text-sm block">
                    Pinjam
                  </a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="col-span-3 text-center text-gray-500">Tidak ada buku tersedia.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const tabBtns = document.querySelectorAll('.tab-btn');
      const tabContents = document.querySelectorAll('.tab-content');

      tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          tabBtns.forEach(b => b.classList.remove('bg-red-900', 'text-white'));
          tabBtns.forEach(b => b.classList.add('border-gray-300', 'text-gray-700'));

          btn.classList.remove('border-gray-300', 'text-gray-700');
          btn.classList.add('bg-red-900', 'text-white');

          tabContents.forEach(content => content.classList.add('hidden'));
          const tabId = btn.getAttribute('data-tab');
          document.getElementById(tabId).classList.remove('hidden');
        });
      });
    });
  </script>
</body>
</html>