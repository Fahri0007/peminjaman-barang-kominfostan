<?php
session_start();
require_once 'database.php';

// pastikan login admin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Statistik
$total_barang = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM barang"))[0];
$total_peminjaman = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman"))[0];
$dipinjam = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'"))[0];
$dikembalikan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='dikembalikan'"))[0];
$menunggu = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='menunggu'"))[0];
$ditolak = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='ditolak'"))[0];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" href="assets/img/123.png">

<style>
body { margin:0; background:#f4f6f9; font-family:Arial,sans-serif; }

.topbar {
  height:90px;
  background:#003a8f;
  color:#fff;
  display:flex;
  align-items:center;
  gap:15px;
  padding:0 20px;
  position:fixed;
  top:0; left:0; right:0;
  z-index:1000;
}

#toggleSidebar {
  background:none;
  border:none;
  color:white;
  font-size:1.8rem;
  cursor:pointer;
}

.brand-title { display:flex; align-items:center; gap:12px; }
.brand-logo { height:50px; }
.brand-text { display:flex; flex-direction:column; }
.brand-main { font-weight:bold; font-size:1.2rem; }
.brand-sub { font-size:0.85rem; opacity:.85; }

.auth-links { margin-left:auto; }

.sidebar {
  width:200px;
  background:#1a237e;
  position:fixed;
  top:90px;
  left:-220px;
  bottom:0;
  padding-top:10px;
  transition:.3s;
  z-index:1100;
}
.sidebar.show { left:0; }

.sidebar a {
  color:white;
  padding:14px 20px;
  display:block;
  text-decoration:none;
}
.sidebar a:hover,
.sidebar .active { background:#1565c0; }

.content { padding:120px 25px 30px; }

.stat-card {
  background: #f8f9fa;
  border-radius: 18px;
  padding: 18px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  transition: 0.3s;
  height: 95px;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.stat-text h6 {
  font-size: 0.75rem;
  color: #6c757d;
  margin: 0;
  letter-spacing: 0.5px;
}

.stat-text h2 {
  font-weight: bold;
  margin: 4px 0 0 0;
  font-size: 1.3rem;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.3rem;
}

/* Warna Icon */
.icon-blue { background: #5e72e4; }
.icon-green { background: #2dce89; }
.icon-orange { background: #fb6340; }
.icon-red { background: #f5365c; }
.icon-yellow { background: #fbcf33; }
.icon-dark { background: #172b4d; }
.card-box {
  background:white;
  border-radius:14px;
  padding:20px;
}

/* Highlight baris */
tr.status-menunggu { background-color:#f8f9fa; }
tr.status-ditolak { background-color:#fff5f5; }
tr.status-dipinjam { background-color:#eef5ff; }
tr.status-dikembalikan { background-color:#f0fff4; }

#dashboardTable tbody tr:hover {
  transform: scale(1.002);
  transition: 0.2s;
  box-shadow:0 3px 8px rgba(0,0,0,0.05);
}
</style>
</head>

<body>

<div class="topbar">
  <button id="toggleSidebar"><i class="bi bi-list"></i></button>

  <div class="brand-title">
    <img src="assets/img/123.png" class="brand-logo">
    <div class="brand-text">
      <span class="brand-main">Dashboard Admin</span>
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
<div class="container-fluid content">
<div class="row g-3 mb-4">
  <!-- TOTAL BARANG -->
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-text">
        <h6>TOTAL BARANG</h6>
        <h2><?= $total_barang ?></h2>
      </div>
      <div class="stat-icon icon-blue">
        <i class="bi bi-box-seam"></i>
      </div>
    </div>
  </div>

  <!-- TOTAL PEMINJAMAN -->
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-text">
        <h6>TOTAL PEMINJAMAN</h6>
        <h2><?= $total_peminjaman ?></h2>
      </div>
      <div class="stat-icon icon-dark">
        <i class="bi bi-arrow-left-right"></i>
      </div>
    </div>
  </div>

  <!-- SEDANG DIPINJAM -->
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-text">
        <h6>DIPINJAM</h6>
        <h2><?= $dipinjam ?></h2>
      </div>
      <div class="stat-icon icon-orange">
        <i class="bi bi-clock-history"></i>
      </div>
    </div>
  </div>

  <!-- DIKEMBALIKAN -->
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-text">
        <h6>DIKEMBALIKAN</h6>
        <h2><?= $dikembalikan ?></h2>
      </div>
      <div class="stat-icon icon-green">
        <i class="bi bi-check-circle"></i>
      </div>
    </div>
  </div>

  <!-- MENUNGGU -->
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-text">
        <h6>MENUNGGU</h6>
        <h2><?= $menunggu ?></h2>
      </div>
      <div class="stat-icon icon-yellow">
        <i class="bi bi-hourglass-split"></i>
      </div>
    </div>
  </div>

  <!-- DITOLAK -->
  <div class="col-lg-2 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-text">
        <h6>DITOLAK</h6>
        <h2><?= $ditolak ?></h2>
      </div>
      <div class="stat-icon icon-red">
        <i class="bi bi-x-circle"></i>
      </div>
    </div>
  </div>
<div class="card-box shadow-sm">
<h5 class="mb-3">Peminjaman Terbaru</h5>

<div class="row mb-3">
  <div class="col-md-4">
    <div class="input-group shadow-sm">
      <span class="input-group-text bg-white">
        <i class="bi bi-search"></i>
      </span>
      <input type="text" id="searchDashboard" 
             class="form-control"
             placeholder="Cari data peminjaman...">
    </div>
  </div>
</div>
<table class="table table-bordered table-striped" id="dashboardTable">
<thead class="table-dark">
<tr>
<th>No</th>
<th>No Transaksi</th>
<th>Nama</th>
<th>Unit Kerja</th>
<th>Nama Dinas</th>
<th>Barang</th>
<th>Jumlah</th>
<th>Serial</th>
<th>Tgl Pinjam</th>
<th>Tgl Kembali</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>

<?php
$q = mysqli_query($conn, "
SELECT p.*, b.nama_barang 
FROM peminjaman p
JOIN barang b ON p.id_barang = b.id_barang
ORDER BY p.id DESC
LIMIT 10
");

$no=1;
while($d=mysqli_fetch_assoc($q)):
?>

<tr class="status-<?= $d['status']; ?>">
<td><?= $no++; ?></td>
<td><strong>TRX-<?= $d['id']; ?></strong></td>
<td><?= htmlspecialchars($d['nama_peminjam']); ?></td>
<td><?= htmlspecialchars($d['unit_kerja']); ?></td>
<td><?= htmlspecialchars($d['nama_dinas']); ?></td>
<td><?= htmlspecialchars($d['nama_barang']); ?></td>
<td><?= $d['jumlah']; ?></td>
<td><?= $d['serial_number'] ?: '-'; ?></td>
<td><?= $d['tgl_pinjam'] ? date('d-m-Y',strtotime($d['tgl_pinjam'])):'-'; ?></td>
<td><?= $d['tgl_kembali'] ? date('d-m-Y',strtotime($d['tgl_kembali'])):'-'; ?></td>
<td>
<?php
$statusColor=[
'menunggu'=>'bg-secondary',
'ditolak'=>'bg-danger',
'dipinjam'=>'bg-primary',
'dikembalikan'=>'bg-success'
];
?>
<span class="badge <?= $statusColor[$d['status']] ?? 'bg-dark' ?>">
<?= ucfirst($d['status']); ?>
</span>
</td>
<td>
<a href="status_peminjaman.php?id=<?= $d['id']; ?>" 
class="btn btn-sm btn-outline-primary">Detail</a>
</td>
</tr>

<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

<script>
document.getElementById('toggleSidebar')
.addEventListener('click',()=>document.getElementById('sidebar').classList.toggle('show'));

document.getElementById("searchDashboard").addEventListener("keyup", function () {
  const keyword = this.value.toLowerCase();
  const rows = document.querySelectorAll("#dashboardTable tbody tr");

  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(keyword) ? "" : "none";
  });
});
</script>
</body>
</html>
