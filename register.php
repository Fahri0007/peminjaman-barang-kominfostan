<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi</title>

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
    .brand-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .brand-logo { width: 45px; }
    .brand-text {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .brand-main { font-weight: bold; font-size: 1.2rem; }
    .brand-sub { font-size: 0.85rem; opacity: 0.85; }
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
    }
    .login-logo {
      display: block;
      margin: 0 auto 20px;
      width: 150px;
    }
  </style>
</head>

<body>

<div class="topbar">
  <div class="brand-title">
    <img src="assets/img/123.png" class="brand-logo">
    <div class="brand-text">
      <span class="brand-main">Peminjaman Barang</span>
      <span class="brand-sub">DISKOMINFOSTAN</span>
    </div>
  </div>
</div>

<div class="container content">
  <div class="login-container">

    <img src="assets/img/logo.png" class="login-logo">

    <h4 class="text-center mb-4">Registrasi</h4>

<form action="login_process.php" method="POST">
  <input type="hidden" name="aksi" value="register">

  <div class="row">

    <div class="col-md-6 mb-3">
      <label>Nama Lengkap</label>
      <input type="text" name="nama" class="form-control" required placeholder="Masukkan Nama..">
    </div>

    <div class="col-md-6 mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" required placeholder="Masukkan Username..">
    </div>

    <div class="col-md-6 mb-3">
      <label>Unit Kerja</label>
      <input type="text" name="unit_kerja" class="form-control" required placeholder="Masukkan Unit Kerja..">
    </div>

    <div class="col-md-6 mb-3">
      <label>Nama Dinas</label>
      <input type="text" name="nama_dinas" class="form-control" required placeholder="Masukkan Dinas..">
    </div>

    <div class="col-12 mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required placeholder="Masukkan Password..">
    </div>

  </div>

  <button class="btn btn-primary w-100">Daftar</button>

  <div class="text-center mt-3">
    <span>Sudah punya akun? </span>
    <a href="login.php">Login di sini</a>
  </div>
</form>
  </div>
</div>

</body>
</html>
