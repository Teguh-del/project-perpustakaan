<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Buku - Perpustakaan Mini</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-color: #fafafa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
  </style>
</head>
<body class="min-h-screen">
  <!-- Header -->
  <header class="bg-white shadow-sm">
    <div class="container mx-auto px-4 py-3">
      <a href="index.php" class="text-red-900 hover:underline flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-1">
          <path d="M16 8A8 8 0 110 8a8 8 0 0116 0z"/>
          <path d="M7.5 7.5L6 6 4.5 7.5 6 9z"/>
          <path d="M10 6.5H6V8h4v-1.5z"/>
        </svg>
        Kembali ke Beranda
      </a>
    </div>
  </header>

  <!-- Detail Buku -->
  <main class="container mx-auto px-4 py-8">
    <?php
    require 'config/db.php';
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo '<p class="text-center text-red-600">Buku tidak ditemukan.</p>';
        exit;
    }

    $id = (int)$_GET['id'];
    $stmt = $koneksi->prepare("SELECT judul, penulis, isbn, deskripsi, jumlah_stok, kategori, cover_path FROM buku WHERE id = ? AND eksklusif_member = 0");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $buku = $stmt->get_result()->fetch_assoc();

    if (!$buku) {
        echo '<p class="text-center text-red-600">Buku tidak ditemukan atau tidak tersedia untuk umum.</p>';
        exit;
    }
    ?>

    <div class="bg-white rounded-xl shadow p-6 md:p-8 max-w-4xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Cover -->
        <div class="md:col-span-1">
          <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center">
            <?php if ($buku['cover_path'] && file_exists($buku['cover_path'])): ?>
              <img src="<?= htmlspecialchars($buku['cover_path']) ?>" class="w-full h-full object-cover rounded-lg" />
            <?php else: ?>
              <div class="text-gray-400 text-center">No Cover</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Info -->
        <div class="md:col-span-2">
          <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($buku['judul']) ?></h1>
          <p class="text-gray-600 mt-1">Penulis: <?= htmlspecialchars($buku['penulis']) ?></p>
          
          <?php if ($buku['isbn']): ?>
            <p class="text-gray-600">ISBN: <?= htmlspecialchars($buku['isbn']) ?></p>
          <?php endif; ?>

          <div class="mt-4">
            <span class="inline-block px-3 py-1 rounded-full <?= $buku['jumlah_stok'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> text-sm font-medium">
              <?= $buku['jumlah_stok'] > 0 ? 'Tersedia (' . $buku['jumlah_stok'] . ')' : 'Habis' ?>
            </span>
            <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium ml-2">
              <?= ucfirst($buku['kategori'] ?? '–') ?>
            </span>
          </div>

          <!-- Sinopsis -->
          <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Sinopsis</h2>
            <p class="text-gray-700"><?= nl2br(htmlspecialchars($buku['deskripsi'] ?? 'Tidak ada sinopsis.')) ?></p>
          </div>

          <!-- Aksi -->
          <div class="mt-6">
            <?php if ($buku['jumlah_stok'] > 0): ?>
              <a href="login.php" class="bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-lg text-sm">
                Login untuk Pinjam
              </a>
            <?php else: ?>
              <span class="text-gray-500 text-sm">Buku sedang tidak tersedia</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>