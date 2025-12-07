<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Perpustakaan Mini</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-color: #fafafa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .book-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .book-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2.5rem;
      text-align: center;
      border: 1px dashed #d1d5db;
      border-radius: 1rem;
      background: #f9fafb;
      color: #6b7280;
    }
  </style>
</head>
<body class="min-h-screen">
  <!-- Header -->
  <header class="bg-white shadow-sm">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-red-900 text-white rounded-lg flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 2.5C6 1 3.5 1 1.5 2v10c2-.9 4-.9 6 .5V2.5zM8 2.5c2-1.5 4.5-1.5 6-.5v10c-2-.9-4-.9-6 .5V2.5z"/>
          </svg>
        </div>
        <span class="font-bold text-red-900 text-lg">Perpustakaan</span>
      </div>
      <div class="flex space-x-2">
        <a href="login.php" class="px-3 py-1.5 text-sm border border-red-900 text-red-900 rounded-lg hover:bg-red-50 transition">Login</a>
        <a href="register.php" class="px-3 py-1.5 text-sm bg-red-900 text-white rounded-lg hover:bg-red-800 transition">Daftar</a>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="py-8 bg-gradient-to-r from-red-50 to-white">
    <div class="container mx-auto px-4 text-center">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Temukan Buku Favoritmu</h1>
      <p class="text-gray-600 mb-6">Jelajahi koleksi buku terbaik kami</p>
      <div class="max-w-lg mx-auto">
        <form method="GET" class="relative">
          <input 
            type="text" 
            name="cari" 
            value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" 
            placeholder="Cari judul buku atau penulis..." 
            class="w-full px-4 py-2.5 pl-10 pr-4 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-900 focus:border-transparent"
          />
          <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.398 1.398l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
            </svg>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Buku -->
  <section class="py-8">
    <div class="container mx-auto px-4">
      <?php
      require 'config/db.php';

      $cari = $_GET['cari'] ?? '';

      $sql = "SELECT id, judul, penulis, cover_path, deskripsi 
              FROM buku 
              WHERE eksklusif_member = 0 AND jumlah_stok > 0";

      $params = [];
      $types = "";

      if (!empty($cari)) {
          $sql .= " AND (judul LIKE ? OR penulis LIKE ?)";
          $like = "%$cari%";
          $params = [$like, $like];
          $types = "ss";
      }

      $sql .= " ORDER BY judul";

      $stmt = $koneksi->prepare($sql);
      if (!empty($types)) {
          $stmt->bind_param($types, ...$params);
      }
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result && $result->num_rows > 0):
      ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php while ($buku = $result->fetch_assoc()): ?>
            <div class="book-card bg-white p-5 rounded-lg shadow">
              <div class="w-full h-48 bg-gray-100 rounded mb-4 flex items-center justify-center">
                <?php if ($buku['cover_path'] && file_exists($buku['cover_path'])): ?>
                  <img src="<?= htmlspecialchars($buku['cover_path']) ?>" class="w-full h-full object-cover rounded" />
                <?php else: ?>
                  <div class="text-gray-400">No Cover</div>
                <?php endif; ?>
              </div>
              <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($buku['judul']) ?></h3>
              <p class="text-gray-600 mt-1 text-sm">Penulis: <?= htmlspecialchars($buku['penulis']) ?></p>
              <div class="mt-3 flex justify-between items-center">
                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Tersedia</span>
                <a href="detail.php?id=<?= $buku['id'] ?>" class="text-red-900 text-sm font-medium hover:underline">Detail</a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <div class="text-5xl mb-4">📚</div>
          <h3 class="font-medium text-lg mb-1">Tidak ada buku ditemukan</h3>
          <p><?= empty($cari) ? 'Admin sedang menyiapkan koleksi. Kunjungi lagi nanti!' : 'Coba kata kunci lain.' ?></p>
        </div>
      <?php endif; ?>
    </div>
  </section>
</body>
</html>