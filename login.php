<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Selamat Datang Kembali</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(135deg, #f9f7f7 0%, #f0f0f0 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg w-full max-w-md">
    <div class="flex justify-center mb-6">
      <div class="w-12 h-12 bg-red-900 text-white rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
  <path d="M8 2.5C6 1 3.5 1 1.5 2v10c2-.9 4-.9 6 .5V2.5zM8 2.5c2-1.5 4.5-1.5 6-.5v10c-2-.9-4-.9-6 .5V2.5z"/>
</svg>
      </div>
    </div>

    <h2 class="text-center text-gray-700 text-xl font-semibold mb-1">Selamat Datang Kembali</h2>
    <p class="text-center text-gray-500 text-sm mb-6">Masuk ke akun Anda</p>

    <?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require 'config/db.php';
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $koneksi->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: dashboard/admin/index.php");
                } else {
                    header("Location: dashboard/user/dashboard.php");
                }
                exit;
            }
        }
        $error = "Email atau password salah!";
    }
    ?>

    <?php if (!empty($error)): ?>
      <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" required 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent" />
      </div>
      <div>
        <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
        <div class="relative">
          <input type="password" name="password" required 
                 class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent"
                 placeholder="••••••••" />
          <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword(this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
  <path d="M16 8s-3.5-6-8-6-8 6-8 6 3.5 6 8 6 8-6 8-6z"/>
  <path d="M8 5a3 3 0 1 1 0 6A3 3 0 0 1 8 5z"/>
</svg>

          </button>
        </div>
      </div>
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <label class="flex items-center">
          <input type="checkbox" name="remember" class="mr-2 h-4 w-4 text-red-900 focus:ring-red-900 border-gray-300 rounded" />
          <span class="text-gray-700 text-sm">Ingat saya</span>
        </label>
        <a href="#" class="text-sm text-red-900 hover:underline">Lupa password?</a>
      </div>
      <button type="submit" class="w-full bg-red-900 hover:bg-red-800 text-white font-medium py-2.5 rounded-lg transition duration-200">
        Masuk Sekarang
      </button>
    </form>

    <div class="mt-6 text-center">
      <p class="text-gray-600 text-sm">
        Belum punya akun? <a href="register.php" class="text-red-900 font-medium hover:underline">Daftar sekarang</a>
      </p>
    </div>

    <div class="mt-4 text-center">
      <a href="index.php" class="text-xs text-gray-500 hover:underline">Kembali ke beranda</a>
    </div>
  </div>

  <script>
    function togglePassword(btn) {
  const input = btn.previousElementSibling;
  const isShow = input.type === "password";

  input.type = isShow ? "text" : "password";

  btn.innerHTML = isShow
    ? `<!-- eye-slash -->
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
        <path d="M1 1L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M8 3c-4 0-7 5-7 5s3 5 7 5 7-5 7-5-3-5-7-5z"/>
        <circle cx="8" cy="8" r="2.5" fill="none" stroke="currentColor" stroke-width="1"/>
        <circle cx="8" cy="8" r="1.3" fill="currentColor"/>
      </svg>`
    : `<!-- eye -->
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
        <path d="M8 3c-4 0-7 5-7 5s3 5 7 5 7-5 7-5-3-5-7-5z"/>
        <circle cx="8" cy="8" r="2.5" fill="none" stroke="currentColor" stroke-width="1"/>
        <circle cx="8" cy="8" r="1.3" fill="currentColor"/>
      </svg>`;
}
  </script>
</body>
</html>