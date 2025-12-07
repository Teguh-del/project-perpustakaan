<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Anggota - Perpustakaan Mini</title>
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
      <a href="buku.php" class="block py-2 px-4 rounded-r-full hover:bg-red-800 mb-1">Buku</a>
      <a href="anggota.php" class="block py-2 px-4 rounded-r-full bg-red-800">Anggota</a>
      <div class="mt-8 pt-4 border-t border-red-800">
        <a href="../logout.php" class="text-sm block text-red-200 hover:text-white">Logout</a>
      </div>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <header class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Data Anggota</h1>
        <p class="text-gray-600">Kelola akses mahasiswa</p>
      </div>
      <div class="flex gap-2">
        <div class="relative">
          <input type="text" placeholder="Cari Mahasiswa..." class="px-4 py-2 border rounded-lg pl-10">
          <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c-.212.212-.45.392-.713.544-.263.152-.544.263-.835.333-.291.07-.595.094-.904.084-.31-.01-.615-.057-.904-.134-.29-.076-.565-.193-.835-.333-.27-.152-.45-.392-.713-.544a6.5 6.5 0 0 0-1.397-1.398l-.001.001zm-1.397-1.398a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9z"/>
            </svg>
          </div>
        </div>
        <div class="flex">
          <button class="px-3 py-2 bg-gray-200 text-gray-800 rounded-l text-sm">Semua</button>
          <button class="px-3 py-2 bg-blue-100 text-blue-800 text-sm">Aktif</button>
          <button class="px-3 py-2 bg-gray-100 text-gray-800 rounded-r text-sm">Diblokir</button>
        </div>
      </div>
    </header>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Profil</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Prodi</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Pinjaman Aktif</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php
          require '../../config/db.php';
          $result = $koneksi->query("SELECT * FROM users WHERE role = 'member'");
          while ($user = $result->fetch_assoc()):
          ?>
            <tr>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M8 1a2 2 0 0 1 2 2v2H6V3a2 2 0 0 1 2-2zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                      <path d="M6.5 9a2 2 0 0 1 3 0v6H6.5v-6z"/>
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium"><?= htmlspecialchars($user['nama']) ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">Informatika</td>
              <td class="px-6 py-4">
                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">2 Buku</span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Aktif</span>
              </td>
              <td class="px-6 py-4">
                <a href="#" class="text-red-900 text-sm">Detail</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>