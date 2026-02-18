<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role    = $_SESSION['role'];
$q = null; 
if ($role === 'admin') { 
    if (!empty($_GET['id'])) {
        $id = (int)$_GET['id'];

        $q = mysqli_query($conn,"
    SELECT 
        p.id,
        p.kode_pinjam,
        p.nama_peminjam,
        p.unit_kerja,
        p.nama_dinas,
        p.jumlah,
        p.satuan,
        p.serial_number,
        p.status,
        p.tgl_pinjam,
        p.jam_pinjam,
        b.nama_barang
    FROM peminjaman p
    JOIN barang b ON p.id_barang = b.id_barang
    WHERE p.id = $id
    LIMIT 1
");
    } else {
        $q = mysqli_query($conn,"
            SELECT p.*, b.nama_barang, b.satuan
            FROM peminjaman p
            LEFT JOIN barang b ON p.id_barang = b.id_barang
            ORDER BY p.id DESC
            LIMIT 1
        ");
    }

/* =========================
   USER
   ========================= */


   } else {
    $q = mysqli_query($conn,"
       SELECT p.*, b.nama_barang, b.satuan
       FROM peminjaman p
       LEFT JOIN barang b ON p.id_barang = b.id_barang
        WHERE p.id_user = $id_user
        ORDER BY p.id DESC
    ");
}


/* =========================
   VALIDASI QUERY
   ========================= */
if (!$q) {
    die('Query gagal: ' . mysqli_error($conn));
}

$last = mysqli_fetch_assoc($q);

if (!$last) {
    die('Data peminjaman tidak ditemukan');
}
$serials = [];
if (!empty($last['serial_number'])) {
    $serials = array_map('trim', explode(',', $last['serial_number']));
}
    
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="5">
<link rel="icon" href="assets/img/123.png">
<title>Status Peminjaman</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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

.newtons-cradle {
 --uib-size: 30px;
 --uib-speed: 1.0s;
 --uib-color: #6c757d;
 position: relative;
 display: flex;
 margin-left: 20px;
 align-items: center;
 justify-content: center;
 width: var(--uib-size);
 height: var(--uib-size);
}

.newtons-cradle__dot {
 position: relative;
 display: flex;
 align-items: center;
 height: 100%;
 width: 25%;
 transform-origin: center top;
}

.newtons-cradle__dot::after {
 content: '';
 display: block;
 width: 100%;
 height: 25%;
 border-radius: 50%;
 background-color: var(--uib-color);
}

.newtons-cradle__dot:first-child {
 animation: swing var(--uib-speed) linear infinite;
}

.newtons-cradle__dot:last-child {
 animation: swing2 var(--uib-speed) linear infinite;
}

@keyframes swing {
 0% { transform: rotate(0deg); animation-timing-function: ease-out; }
 25% { transform: rotate(70deg); animation-timing-function: ease-in; }
 50% { transform: rotate(0deg); }
}

@keyframes swing2 {
 0% { transform: rotate(0deg); }
 50% { transform: rotate(0deg); animation-timing-function: ease-out; }
 75% { transform: rotate(-70deg); animation-timing-function: ease-in; }
}
</style>
</head>
<body class="bg-light">
<div class="topbar">
  <button id="toggleSidebar"><i class="bi bi-list"></i></button>

  <div class="brand-title">
    <img src="assets/img/123.png" class="brand-logo">
    <div class="brand-text">
      <span class="brand-main">Peminjaman Barang</span>
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
<div class="sidebar" id="sidebar">
  <a href="index.php"><i class="bi bi-grid-fill me-2"></i> Daftar Barang</a>
  <a href="peminjaman.php"><i class="bi bi-arrow-left-right me-2"></i> Peminjaman</a>
  <a href="pengembalian.php"><i class="bi bi-arrow-return-left me-2"></i> Pengembalian</a>
  <?php if($_SESSION['role'] === 'admin'): ?>
  <a href="dashboard.php"><i class="bi bi-bar-chart-line-fill me-2"></i> Dashboard</a>
  <a href="serial.php"><i class="bi bi-upc-scan me-2"></i> Serial Barang</a>
  <?php endif; ?>
</div>
<div class="content">
<div class="mb-3">
    <button onclick="history.back()" 
        style="
            background: transparent;
            border: none;
            font-size: 45px;
            color: #003a8f;
            cursor: pointer;
            transition: 0.3s;
        "
        onmouseover="this.style.transform='translateX(-5px)'"
        onmouseout="this.style.transform='translateX(0)'">
        <i class="bi bi-arrow-left-short"></i>
    </button>
</div>
<?php if (!empty($last)): ?>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>Barang</th>
            <th>Serial</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Kode Pinjam</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td><?= htmlspecialchars($last['nama_barang']) ?></td>
            <td>
    <?php if (!empty($serials)): ?>
        <?php foreach ($serials as $sn): ?>
            <div><?= htmlspecialchars($sn) ?></div>
        <?php endforeach; ?>
    <?php else: ?>
        <span class="text-muted">-</span>
    <?php endif; ?>
</td>
            <td><?= (int)$last['jumlah'] ?></td>
            <td><?= htmlspecialchars($last['satuan']) ?></td>
            <td>
                <?php if ($last['status'] === 'dipinjam'): ?>
                    <span class="badge bg-success">
                        <?= htmlspecialchars($last['kode_pinjam']) ?>
                    </span>
                <?php elseif ($last['status'] === 'menunggu'): ?>
                    <!-- ANIMASI TIDAK DIUBAH -->
                    <div class="newtons-cradle">
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                        <div class="newtons-cradle__dot"></div>
                    </div>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>

            <td>
                <?php if ($last['status'] === 'menunggu'): ?>
                    <span class="badge bg-secondary">Menunggu</span>
                <?php elseif ($last['status'] === 'dipinjam'): ?>
                    <span class="badge bg-success">Disetujui</span>
                <?php elseif ($last['status'] === 'ditolak'): ?>
                    <span class="badge bg-danger">Ditolak</span>
                <?php else: ?>
                    <span class="badge bg-dark">Tidak diketahui</span>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>
<?php if (
    isset($_SESSION['role']) &&
    strtolower($_SESSION['role']) === 'admin' &&
    isset($last['status']) &&
    $last['status'] === 'menunggu'
): ?>

<form method="post" action="aksi_peminjaman.php" class="mt-3 text-center">
    <input type="hidden" name="id" value="<?= $last['id'] ?>">

    <button type="submit" name="aksi" value="setuju" class="btn btn-success me-2">
        <i class="bi bi-check-circle"></i> Setujui
    </button>

    <button type="submit" name="aksi" value="tolak" class="btn btn-danger">
        <i class="bi bi-x-circle"></i> Tolak
    </button>
</form>
<?php endif; ?>
<?php else: ?>
<div class="alert alert-warning text-center">
    Belum ada data peminjaman
</div>
<?php endif; ?>
</div>
<script>
const toggleBtn = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('show');
});
</script>
</body>
</html>
