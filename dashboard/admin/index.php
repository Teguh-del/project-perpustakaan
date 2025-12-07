<?php
require '../../auth/auth_admin.php';
require '../../config/db.php';

// Ambil data statistik
$total_buku = $koneksi->query("SELECT COUNT(*) AS total FROM buku")->fetch_assoc()['total'];
$buku_dipinjam = $koneksi->query("SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'dipinjam'")->fetch_assoc()['total'];
$total_member = $koneksi->query("SELECT COUNT(*) AS total FROM users WHERE role = 'member'")->fetch_assoc()['total'];
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
<body class="min-h-screen flex bg-gray-50">
  <!-- Sidebar -->
  <aside class="bg-red-900 text-white w-64 min-h-screen p-6">
    <div class="flex items-center space-x-2 mb-8">
      <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
  <path d="M8 2.5C6 1 3.5 1 1.5 2v10c2-.9 4-.9 6 .5V2.5zM8 2.5c2-1.5 4.5-1.5 6-.5v10c-2-.9-4-.9-6 .5V2.5z"/>
</svg>
      </div>
      <span class="font-bold">Admin Panel</span>
    </div>
    <nav>
      <a href="index.php" class="block py-2 px-4 rounded-r-full bg-red-800 mb-1">Dashboard</a>
      <a href="buku.php" class="block py-2 px-4 rounded-r-full hover:bg-red-800 mb-1">Buku</a>
      <a href="anggota.php" class="block py-2 px-4 rounded-r-full hover:bg-red-800">Anggota</a>
      <div class="mt-8 pt-4 border-t border-red-800">
        <a href="../logout.php" class="text-sm block text-red-200 hover:text-white">Logout</a>
      </div>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <header class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Kelola perpustakaan Anda</p>
      </div>
      <a href="buku-tambah.php" class="bg-red-900 hover:bg-red-800 text-white py-2 px-4 rounded-lg text-sm">
        + Tambah Buku
      </a>
    </header>

  <!-- Statistik -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <!-- Total Buku -->
  <div class="bg-gradient-to-br from-red-900 to-red-800 text-white rounded-lg p-6 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-2">
  <!-- Left cover -->
  <path d="M12 4c-2.5-1.5-6.5-1-8 1v11c1.5-2 5.5-2.5 8-1.2" />
  <!-- Right cover -->
  <path d="M12 4c2.5-1.5 6.5-1 8 1v11c-1.5-2-5.5-2.5-8-1.2" />
  <!-- Middle fold -->
  <path d="M12 4v11.8" />
</svg>
    <h3 class="text-lg opacity-90">Total Buku</h3>
    <p class="text-2xl font-bold"><?= $total_buku ?></p>
  </div>

  <!-- Buku Dipinjam -->
  <div class="bg-gradient-to-br from-amber-800 to-amber-700 text-white rounded-lg p-6 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16" class="mx-auto mb-2">
      <path d="M16 8s-8-5.5-8-8-8 2.5-8 8 8 8 8 8 8-2.5 8-8zM1.173 8a13.133 13.133 0 011.66 2.043C4.12 11.332 5.88 12.5 8 12.5c2.12 0 3.878-1.168 5.168-2.457A13.133 13.133 0 0115 8s-8-5.5-8-8-8 2.5-8 8z"/>
    </svg>
    <h3 class="text-lg opacity-90">Buku Dipinjam</h3>
    <p class="text-2xl font-bold"><?= $buku_dipinjam ?></p>
  </div>

  <!-- Total Member -->
  <div class="bg-gradient-to-br from-stone-800 to-stone-700 text-white rounded-lg p-6 text-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16" class="mx-auto mb-2">
      <path d="M16 8s-8-5.5-8-8-8 2.5-8 8 8 8 8 8 8-2.5 8-8zM1.173 8a13.133 13.133 0 011.66 2.043C4.12 11.332 5.88 12.5 8 12.5c2.12 0 3.878-1.168 5.168-2.457A13.133 13.133 0 0115 8s-8-5.5-8-8-8 2.5-8 8z"/>
    </svg>
    <h3 class="text-lg opacity-90">Total Member</h3>
    <p class="text-2xl font-bold"><?= $total_member ?></p>
  </div>
</div>

    <!-- Manajemen Buku -->
    <section class="mb-8">
      <h2 class="text-xl font-semibold mb-4">Manajemen Buku</h2>
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php
            require '../../config/db.php';
            $result = $koneksi->query("SELECT * FROM buku ORDER BY judul");
            if ($result && $result->num_rows > 0):
              while ($buku = $result->fetch_assoc()):
            ?>
              <tr>
                <td class="px-6 py-4"><?= htmlspecialchars($buku['judul']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($buku['penulis']) ?></td>
                <td class="px-6 py-4">
                  <span class="inline-block px-2 py-1 rounded <?= $buku['jumlah_stok'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> text-xs font-medium">
                    <?= $buku['jumlah_stok'] ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <a href="buku-edit.php?id=<?= $buku['id'] ?>" class="text-blue-600 hover:underline mr-2">✏️</a>
                  <a href="#" class="text-red-600 hover:underline">🗑️</a>
                </td>
              </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Peminjaman -->
    <section>
      <h2 class="text-xl font-semibold mb-4">Peminjaman</h2>
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php
            $stmt = $koneksi->prepare("
              SELECT p.id, u.nama, b.judul, p.status
              FROM peminjaman p
              JOIN users u ON p.user_id = u.id
              JOIN buku b ON p.buku_id = b.id
              WHERE p.status IN ('menunggu', 'dipinjam')
              ORDER BY p.id DESC
            ");
            $stmt->execute();
            $data = $stmt->get_result();
            if ($data && $data->num_rows > 0):
              while ($p = $data->fetch_assoc()):
            ?>
              <tr>
                <td class="px-6 py-4"><?= htmlspecialchars($p['nama']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($p['judul']) ?></td>
                <td class="px-6 py-4">
                  <?php if ($p['status'] == 'menunggu'): ?>
                    <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Menunggu</span>
                  <?php else: ?>
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">Dipinjam</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <?php if ($p['status'] == 'menunggu'): ?>
                    <a href="konfirmasi-pinjam.php?id=<?= $p['id'] ?>&action=pinjam" class="bg-red-900 text-white px-3 py-1 rounded text-xs">Konfirmasi</a>
                  <?php else: ?>
                    <a href="konfirmasi-pinjam.php?id=<?= $p['id'] ?>&action=kembali" class="bg-blue-900 text-white px-3 py-1 rounded text-xs">Kembalikan</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>