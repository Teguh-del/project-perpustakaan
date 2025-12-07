<?php
require '../../auth/auth_admin.php';
require '../../config/db.php';

$error = '';
$buku_id = $_GET['id'] ?? null;

if (!is_numeric($buku_id)) {
    header("Location: buku.php?error=ID tidak valid.");
    exit;
}

// Ambil data buku
$stmt = $koneksi->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i", $buku_id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    header("Location: buku.php?error=Buku tidak ditemukan.");
    exit;
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $isbn = !empty($_POST['isbn']) ? trim($_POST['isbn']) : null;
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $stok = (int)($_POST['stok'] ?? 1);
    $eksklusif = isset($_POST['eksklusif']) ? 1 : 0;
    $kategori = in_array($_POST['kategori'] ?? 'komputer', ['komputer', 'novel']) ? $_POST['kategori'] : 'komputer';
    $cover_path = $buku['cover_path']; // pertahankan jika tidak diubah

    if (empty($judul) || empty($penulis) || $stok < 1) {
        $error = "Judul, penulis, dan stok wajib diisi.";
    } else {
        // Hapus cover lama jika diinginkan
        if (isset($_POST['hapus_cover']) && $buku['cover_path']) {
            $old_path = __DIR__ . '/../../' . $buku['cover_path'];
            if (file_exists($old_path)) unlink($old_path);
            $cover_path = null;
        }

        // Upload cover baru
        if (!empty($_FILES['cover']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = "Format cover hanya JPG/PNG.";
            } else {
                // Hapus cover lama
                if ($buku['cover_path'] && file_exists(__DIR__ . '/../../' . $buku['cover_path'])) {
                    unlink(__DIR__ . '/../../' . $buku['cover_path']);
                }
                // Simpan cover baru
                $new_name = 'cover_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $upload_dir = __DIR__ . '/../../uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $upload_dir . $new_name)) {
                    $cover_path = 'uploads/' . $new_name;
                } else {
                    $error = "Gagal mengunggah cover.";
                }
            }
        }

        if (empty($error)) {
            $stmt = $koneksi->prepare("UPDATE buku SET judul = ?, penulis = ?, isbn = ?, jumlah_stok = ?, eksklusif_member = ?, deskripsi = ?, cover_path = ?, kategori = ? WHERE id = ?");
            $stmt->bind_param("sssiisssi", $judul, $penulis, $isbn, $stok, $eksklusif, $deskripsi, $cover_path, $kategori, $buku_id);
            if ($stmt->execute()) {
                header("Location: buku.php?pesan=Buku berhasil diperbarui!");
                exit;
            } else {
                $error = "Gagal memperbarui buku.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Buku - Perpustakaan Mini</title>
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

  <main class="flex-1 p-6">
    <header class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Edit Buku</h1>
        <p class="text-gray-600">Perbarui informasi buku</p>
      </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Form -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Informasi Buku</h2>

        <?php if (!empty($error)): ?>
          <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Judul</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Penulis</label>
            <input type="text" name="penulis" value="<?= htmlspecialchars($buku['penulis']) ?>" required class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">ISBN (opsional)</label>
            <input type="text" name="isbn" value="<?= htmlspecialchars($buku['isbn']) ?>" class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <select name="kategori" class="w-full px-4 py-2 border rounded-lg">
              <option value="komputer" <?= $buku['kategori'] === 'komputer' ? 'selected' : '' ?>>Komputer</option>
              <option value="novel" <?= $buku['kategori'] === 'novel' ? 'selected' : '' ?>>Novel</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Sinopsis / Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($buku['deskripsi'] ?? '') ?></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Cover Buku (opsional)</label>
            <input type="file" name="cover" accept=".jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-red-900 file:text-white hover:file:bg-red-800" />
            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG (max 2MB)</p>
            <?php if ($buku['cover_path']): ?>
              <div class="mt-2 flex items-center">
                <img src="<?= htmlspecialchars($buku['cover_path']) ?>" class="w-16 h-16 object-cover rounded" />
                <label class="ml-2 flex items-center">
                  <input type="checkbox" name="hapus_cover" class="mr-1" />
                  <span class="text-sm text-red-600">Hapus cover</span>
                </label>
              </div>
            <?php endif; ?>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Stok</label>
            <input type="number" name="stok" min="1" value="<?= $buku['jumlah_stok'] ?>" required class="w-full px-4 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="flex items-center">
              <input type="checkbox" name="eksklusif" class="mr-2 h-4 w-4 text-red-900 rounded" <?= $buku['eksklusif_member'] ? 'checked' : '' ?> />
              <span class="text-gray-700 text-sm">Eksklusif Member</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">Hanya member yang dapat meminjam</p>
          </div>
          <div class="flex items-center justify-end gap-2">
            <a href="buku.php" class="text-gray-600">Batal</a>
            <button type="submit" class="bg-red-900 text-white px-4 py-2 rounded text-sm">Simpan Perubahan</button>
          </div>
        </form>
      </div>

      <!-- Preview Dinamis -->
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Preview</h2>
        <div class="space-y-4">
          <div>
            <p class="text-sm font-medium mb-2">Cover</p>
            <div id="preview-cover" class="w-full h-48 bg-gray-100 rounded flex items-center justify-center border">
              <?php if ($buku['cover_path']): ?>
                <img src="<?= htmlspecialchars($buku['cover_path']) ?>" class="w-full h-full object-cover rounded" id="current-cover" />
              <?php else: ?>
                <span class="text-gray-400 text-sm">Belum ada cover</span>
              <?php endif; ?>
            </div>
          </div>
          <div>
            <p id="preview-judul" class="font-bold"><?= htmlspecialchars($buku['judul']) ?></p>
            <p id="preview-penulis" class="text-gray-600 text-sm"><?= htmlspecialchars($buku['penulis']) ?></p>
          </div>
          <div>
            <p class="text-sm font-medium mb-1">Sinopsis</p>
            <p id="preview-sinopsis" class="text-sm text-gray-700"><?= htmlspecialchars($buku['deskripsi'] ?? 'Deskripsi buku akan muncul di sini.') ?></p>
          </div>
          <div class="text-xs text-gray-500">
            Stok: <span id="preview-stok"><?= $buku['jumlah_stok'] ?></span>
          </div>
          <div class="text-xs text-gray-500">
            Kategori: <span class="<?= $buku['kategori'] === 'komputer' ? 'text-blue-600' : 'text-purple-600' ?>"><?= ucfirst($buku['kategori']) ?></span>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const judulInput = document.querySelector('input[name="judul"]');
      const penulisInput = document.querySelector('input[name="penulis"]');
      const sinopsisInput = document.querySelector('textarea[name="deskripsi"]');
      const stokInput = document.querySelector('input[name="stok"]');
      const coverInput = document.querySelector('input[name="cover"]');

      const previewJudul = document.getElementById('preview-judul');
      const previewPenulis = document.getElementById('preview-penulis');
      const previewSinopsis = document.getElementById('preview-sinopsis');
      const previewStok = document.getElementById('preview-stok');

      function updatePreview() {
        previewJudul.textContent = judulInput.value || 'Judul Buku';
        previewPenulis.textContent = penulisInput.value || 'Nama Penulis';
        previewSinopsis.textContent = sinopsisInput.value || 'Deskripsi buku akan muncul di sini.';
        previewStok.textContent = stokInput.value || '1';
      }

      function updateCoverPreview() {
        const file = coverInput.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            document.getElementById('preview-cover').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded" />`;
          };
          reader.readAsDataURL(file);
        }
      }

      judulInput?.addEventListener('input', updatePreview);
      penulisInput?.addEventListener('input', updatePreview);
      sinopsisInput?.addEventListener('input', updatePreview);
      stokInput?.addEventListener('input', updatePreview);
      coverInput?.addEventListener('change', updateCoverPreview);
    });
  </script>
</body>
</html>