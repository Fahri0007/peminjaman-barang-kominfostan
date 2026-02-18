<?php
session_start();
require_once 'database.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $stok    = intval($_POST['stok']);
    $kondisi = mysqli_real_escape_string($conn, $_POST['kondisi']);
    $lokasi  = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $satuan  = mysqli_real_escape_string($conn, $_POST['satuan']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $is_serial = isset($_POST['is_serial']) ? (int)$_POST['is_serial'] : 0;
    mysqli_query($conn, "
    INSERT INTO barang 
    (nama_barang, stok, kondisi, lokasi, satuan, deskripsi, is_serial)
    VALUES 
    ('$nama', $stok, '$kondisi', '$lokasi', '$satuan', '$deskripsi', $is_serial)
");
    $_SESSION['message'] = 'Barang berhasil ditambahkan!';
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Barang</title>
<link rel="icon" href="assets/img/123.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    margin: 0;
    background-color: #f8f9fa;
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

.brand-logo {
    width: 45px;
    height: 45px;
    object-fit: cover;
    padding: 3px;
}

.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}

.brand-main { font-weight: bold; font-size: 1.2rem; }
.brand-sub { font-size: 0.85rem; opacity: 0.85; }

#toggleSidebar {
    background: none;
    border: none;
    color: white;
    font-size: 1.8rem;
    cursor: pointer;
}

.auth-links { margin-left: auto; }

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

.sidebar.show { left: 0; }
.sidebar a { color: white; padding: 12px 20px; display: block; text-decoration: none; }
.sidebar a:hover, .sidebar .active { background-color: #1565c0; }

.container.content {
    padding: 110px 20px 20px 20px; 
}

.form-container {
    max-width: 600px;
}
</style>
</head>
<body>

<div class="topbar">
    <button id="toggleSidebar"><i class="bi bi-list"></i></button>
    <div class="brand-title">
        <img src="assets/img/123.png" class="brand-logo" alt="Logo">
        <div class="brand-text">
            <span class="brand-main">Peminjaman Barang</span>
            <span class="brand-sub">DISKOMINFOSTAN</span>
        </div>
    </div>
    <div class="auth-links">
<?= htmlspecialchars($_SESSION['nama']); ?>
        <a href="logout.php" class="btn btn-sm btn-light ms-3">Logout</a>
    </div>
</div>

<div class="sidebar" id="sidebar">
  <a href="index.php"><i class="bi bi-grid-fill me-2"></i> Daftar Barang</a>
  <a href="peminjaman.php"><i class="bi bi-arrow-left-right me-2"></i> Peminjaman</a>
  <a href="pengembalian.php"><i class="bi bi-arrow-return-left me-2"></i> Pengembalian</a>
  <?php if($_SESSION['role'] === 'admin'): ?>
  <a href="dashboard.php"><i class="bi bi-bar-chart-line-fill me-2"></i> Dashboard</a>
  <a href="serial.php"><i class="bi bi-upc-scan me-2"></i> Serial Barang</a>
  <?php endif; ?>
</div>

<div class="container content">
    <h3>Tambah Barang</h3>
    <div class="form-container mt-3">
    <form method="POST">
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Kondisi</label>
            <input type="text" name="kondisi" class="form-control">
        </div>
        <div class="mb-3">
  <label class="form-label">Lokasi Barang</label>
  <input type="text" name="lokasi" class="form-control" required>
</div>

<div class="mb-3">
  <label class="form-label">Deskripsi</label>
  <textarea name="deskripsi" class="form-control"></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Gunakan Nomor Seri?</label>
    <select name="is_serial" class="form-control" required>
        <option value="1">Ya</option>
        <option value="0">Tidak</option>
    </select>
</div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
const toggleBtn = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
</script>

</body>
</html>
