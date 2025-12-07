<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Buku - Perpustakaan Mini</title>
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
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-2-2v-4h2v4l2 2H10zm6-2l-2-2V8h2v6l2 2H16z"/>
        </svg>
      </div>
      <span class="font-bold">Admin Panel</span>
    </div>
    <nav>
      <a href="index.php" class="block py-2 px-4 rounded-r-full hover:bg-red-800 mb-1">Dashboard</a>
      <a href="buku.php" class="block py-2 px-4 rounded-r-full bg-red-800 mb-1">Buku</a>
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
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pustaka</h1>
        <p class="text-gray-600">Kelola koleksi buku perpustakaan</p>
      </div>
      <a href="buku-tambah.php" class="bg-red-900 hover:bg-red-800 text-white py-2 px-4 rounded-lg text-sm">
        + Tambah Buku
      </a>
    </header>

    <!-- Form Pencarian & Filter -->
    <form method="GET" class="mb-4 flex gap-2">
      <div class="relative flex-1">
        <input 
          type="text" 
          name="cari" 
          value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" 
          placeholder="Cari judul atau penulis..." 
          class="w-full px-4 py-2 border rounded-lg pl-10"
        >
        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c-.212.212-.45.392-.713.544-.263.152-.544.263-.835.333-.291.07-.595.094-.904.084-.31-.01-.615-.057-.904-.134-.29-.076-.565-.193-.835-.333-.27-.152-.45-.392-.713-.544a6.5 6.5 0 0 0-1.397-1.398l-.001.001zm-1.397-1.398a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9z"/>
          </svg>
        </div>
      </div>
      <select name="kategori" class="px-4 py-2 border rounded-lg">
        <option value="">Semua Kategori</option>
        <option value="komputer" <?= (($_GET['kategori'] ?? '') === 'komputer') ? 'selected' : '' ?>>Komputer</option>
        <option value="novel" <?= (($_GET['kategori'] ?? '') === 'novel') ? 'selected' : '' ?>>Novel</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg">Filter</button>
    </form>

    <!-- Tabel Buku -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Info</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php
          require '../../config/db.php';

          $cari = $_GET['cari'] ?? '';
          $kategori = $_GET['kategori'] ?? '';

          $sql = "SELECT * FROM buku WHERE 1=1";
          $params = [];
          $types = "";

          if (!empty($cari)) {
              $sql .= " AND (judul LIKE ? OR penulis LIKE ?)";
              $like = "%$cari%";
              $params[] = $like;
              $params[] = $like;
              $types .= "ss";
          }

          if (!empty($kategori)) {
              $sql .= " AND kategori = ?";
              $params[] = $kategori;
              $types .= "s";
          }

          $sql .= " ORDER BY judul";
          $stmt = $koneksi->prepare($sql);

          if (!empty($types)) {
              $stmt->bind_param($types, ...$params);
          }

          $stmt->execute();
          $result = $stmt->get_result();

          if ($result && $result->num_rows > 0):
            while ($buku = $result->fetch_assoc()):
              $warna_kategori = $buku['kategori'] === 'novel' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';
          ?>
            <tr>
              <td class="px-6 py-4">
                <p class="font-medium"><?= htmlspecialchars($buku['judul']) ?></p>
                <p class="text-gray-600 text-sm"><?= htmlspecialchars($buku['penulis']) ?></p>
              </td>
              <td class="px-6 py-4">
                <span class="inline-block px-2 py-1 rounded <?= $warna_kategori ?> text-xs font-medium">
                  <?= ucfirst($buku['kategori']) ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-block px-2 py-1 rounded <?= $buku['jumlah_stok'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> text-xs font-medium">
                  <?= $buku['jumlah_stok'] ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-block px-2 py-1 rounded <?= $buku['jumlah_stok'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> text-xs font-medium">
                  <?= $buku['jumlah_stok'] > 0 ? 'Tersedia' : 'Habis' ?>
                </span>
              </td>
              <td class="px-6 py-4">
                <a href="buku-edit.php?id=<?= $buku['id'] ?>" class="text-blue-600 hover:underline mr-2">✏️</a>
                <a href="buku-hapus.php?id=<?= $buku['id'] ?>" 
                   class="text-red-600 hover:underline ml-2"
                   onclick="return confirm('Yakin hapus buku ini?')">
                  🗑️
                </a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada buku ditemukan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>