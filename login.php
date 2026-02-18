<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="assets/img/123.png">

  <style>
    body {
      margin: 0;
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
    }

    .topbar {
      height: 90px;
      background-color: #003a8f;
      color: white;
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 0 15px;
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
    }

    #toggleSidebar {
      background: none;
      border: none;
      color: white;
      font-size: 1.8rem;
    }

   .brand-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      width: 45px;
    }

    .brand-text {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }

    .brand-main {
      font-weight: bold;
      font-size: 1.2rem;
    }

    .brand-sub {
      font-size: 0.85rem;
      opacity: 0.85;
    }

    .sidebar {
      width: 200px;
      background-color: #1a237e;
      position: fixed;
      top: 90px;
      left: -220px;
      bottom: 0;
      padding-top: 10px;
      transition: left 0.3s ease;
      z-index: 999;
    }

    .sidebar.show {
      left: 0;
    }

    .sidebar a {
      color: white;
      padding: 12px 20px;
      display: block;
      text-decoration: none;
    }

    .sidebar a:hover,
    .sidebar .active {
      background-color: #1565c0;
    }

    .content {
      padding-top: 130px;
      display: flex;
      justify-content: center;
    }

    .login-container {
      max-width: 450px;
      width: 100%;
      background: #fff;
      padding: 30px 25px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      animation: slideIn 0.8s ease forwards;
    }

    .login-logo {
      display: block;
      margin: 0 auto 20px;
      width: 150px;
      height: auto;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    footer {
      position: fixed;
      bottom: 10px;
      right: 640px;
      color: #555;
      font-size: 14px;
    }
  </style>
</head>

<body>

<div class="topbar">
  <button id="toggleSidebar"><i class="bi bi-list"></i></button>

  <div class="brand-title">
    <img src="assets/img/123.png" class="brand-logo">
    <div class="brand-text">
      <span class="brand-main">Peminjaman Barang</span>
      <span class="brand-sub">DISKOMINFOSTAN</span>
    </div>
  </div>
</div>

<div class="sidebar" id="sidebar">
  <a href="index.php"><i class="bi bi-grid-fill me-2"></i> Daftar Barang</a>
  <a href="login.php" class="active"><i class="bi bi-shield-lock-fill me-2"></i> Login</a>
</div>

<div class="container content">
  <div class="login-container">

    <img src="assets/img/logo.png" class="login-logo" alt="Logo">

    <h4 class="text-center mb-4">Login</h4>

    <form action="login_process.php" method="POST">
      <input type="hidden" name="aksi" value="login">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required placeholder="Masukkan Username..">
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required placeholder="Masukkan Password..">
      </div>

      <button type="submit" class="btn btn-primary w-100">
        Masuk
      </button>
      <div class="text-center mt-3">
  <span>Belum punya akun? </span>
  <a href="register.php">Daftar di sini</a>
</div>  
    </form>

  </div>
</div>

<footer>
  &copy; DISKOMINSTAN | 2026
</footer>

<?php
if (isset($_SESSION['message'])) {
  echo "<script>alert('".addslashes($_SESSION['message'])."');</script>";
  unset($_SESSION['message']);
}
?>

<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });
</script>

</body>
</html>
