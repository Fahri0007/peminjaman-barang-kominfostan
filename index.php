<?php 
session_start(); 
require_once 'database.php';
?>
<!-- fitur baru ditambahkan -->
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Daftar Barang</title>

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
      cursor: pointer;
    }

    .brand-title {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo {
      height: 50px;
      width: auto;
      object-fit: contain;
    }

    .brand-text {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }

    .brand-main { font-weight: bold; font-size: 1.2rem; }
    .brand-sub { font-size: 0.85rem; opacity: 0.85; }

    .auth-links { margin-left:auto; }

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

    .sidebar.show { left:0; }

    .sidebar a {
      color:white;
      padding:12px 20px;
      display:block;
      text-decoration:none;
    }

    .sidebar a:hover, .sidebar .active { background-color:#1565c0; }

    .content {
      padding: 110px 20px 20px 20px;
    }

    table th, table td {
      vertical-align: middle;
    }
    .search-box { margin-bottom:15px; max-width:300px; position:relative; }
    .search-box i { position:absolute; top:10px; left:10px; color:#666; }
    .search-box input { padding-left:30px; }
  </style>
</head>
<body>
<!-- fitur kedua -->

<!-- TOPBAR -->
<div class="topbar">
  <button id="toggleSidebar"><i class="bi bi-list"></i></button>

  <div class="brand-title">
    <img src="assets/img/123.png" class="brand-logo">
    <div class="brand-text">
      <span class="brand-main">Daftar Barang</span>
      <span class="brand-sub">DISKOMINFOSTAN</span>
    </div>
  </div>

  <div class="auth-links">
    <?php if (isset($_SESSION['login'])): ?>
      <?= htmlspecialchars($_SESSION['nama']); ?>
      <a href="logout.php" class="btn btn-sm btn-light ms-3">Logout</a>
    <?php else: ?>
      Guest
      <a href="login.php" class="btn btn-sm btn-light ms-3">Login</a>
    <?php endif; ?>
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <a href="index.php"><i class="bi bi-grid-fill me-2"></i> Daftar Barang</a>
  <a href="peminjaman.php"><i class="bi bi-arrow-left-right me-2"></i> Peminjaman</a>
  <a href="pengembalian.php"><i class="bi bi-arrow-return-left me-2"></i> Pengembalian</a>
  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
  <a href="dashboard.php"><i class="bi bi-bar-chart-line-fill me-2"></i> Dashboard</a>
  <a href="serial.php"><i class="bi bi-upc-scan me-2"></i> Serial Barang</a>
  <?php endif; ?>
  <?php if (!isset($_SESSION['login'])): ?>
    <a href="login.php"><i class="bi bi-shield-lock-fill me-2"></i> Login</a>
  <?php endif; ?>
</div>

<!-- CONTENT -->
<div class="container content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="m-0">Daftar Barang Tersedia</h3>

<?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'admin'): ?>      
    <a href="tambah.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Barang
      </a>
    <?php endif; ?>
  </div>
  <div class="search-box">
    <i class="bi bi-search"></i>
    <input type="text" id="searchInput" class="form-control" placeholder="Search barang...">
  </div>
  <table class="table table-striped table-bordered" id="barangTable">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Stok</th>
        <th>Kondisi</th>
        <th>Lokasi</th>
        <th>Deskripsi</th>
<?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'admin'): ?>          
    <th>Aksi</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $query = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0");
      while($b = mysqli_fetch_assoc($query)):
      ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($b['nama_barang']); ?></td>
        <td><?= htmlspecialchars($b['stok']); ?></td>
        <td><?= htmlspecialchars($b['kondisi']); ?></td>
        <td><?= htmlspecialchars($b['lokasi']); ?></td>
        <td><?= htmlspecialchars($b['deskripsi']); ?></td>

<?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'admin'): ?>        
        <td>
          <a href="edit.php?id=<?= $b['id_barang']; ?>" class="btn btn-success btn-sm">Edit</a>
          <a href="hapus.php?id=<?= $b['id_barang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
        <?php endif; ?>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });
  document.getElementById("searchInput").addEventListener("keyup", function () {
  const keyword = this.value.toLowerCase();
  const rows = document.querySelectorAll("#barangTable tbody tr");

  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(keyword) ? "" : "none";
  });
});
</script>

</body>
</html>
