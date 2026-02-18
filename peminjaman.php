<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

function generateKodePinjam() {
    return 'PMJ' . date('YmdHis') . rand(100,999);
}

if (isset($_POST['pinjam'])) {

    $id_barang     = (int) $_POST['id_barang'];
    $jumlah        = (int) $_POST['jumlah'];
    $nama_peminjam = mysqli_real_escape_string($conn, $_SESSION['nama']);
    $unit_kerja    = mysqli_real_escape_string($conn, $_SESSION['unit_kerja']);
    $nama_dinas    = mysqli_real_escape_string($conn, $_SESSION['nama_dinas']);
    $tgl = date('Y-m-d');
    $jam = date('H:i:s');
    $kode_pinjam = generateKodePinjam();

    // Cek stok barang
    $cek = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT stok FROM barang WHERE id_barang = $id_barang")
    );

    if (!$cek || $cek['stok'] < $jumlah || $jumlah <= 0) {
        die("Stok tidak mencukupi");
    }
    $dataBarang = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT satuan FROM barang WHERE id_barang = $id_barang")
);

$satuan = $dataBarang['satuan'];

    // SIMPAN PEMINJAMAN (USER HANYA MENGAJUKAN)
mysqli_query($conn, "
INSERT INTO peminjaman (
    kode_pinjam,
    id_user,
    id_barang,
    nama_peminjam,
    unit_kerja,
    nama_dinas,
    satuan,
    jumlah,
    status
) VALUES (
    '$kode_pinjam',
    $id_user,
    $id_barang,
    '$nama_peminjam',
    '$unit_kerja',
    '$nama_dinas',
    '$satuan',
    $jumlah,
    'menunggu'
)
");

$_SESSION['message'] = "Permohonan peminjaman berhasil dikirim";
header("Location: status_peminjaman.php");
exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Peminjaman Barang</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" href="assets/img/123.png">
<style>
body { margin:0; background:#f8f9fa; font-family:Arial,sans-serif; }
.topbar { height:90px; background:#003a8f; color:#fff; display:flex; align-items:center; gap:15px; padding:0 15px; position:fixed; top:0; left:0; right:0; z-index:1000; }
#toggleSidebar { background:none; border:none; color:white; font-size:1.8rem; cursor:pointer; }
.brand-title { display:flex; align-items:center; gap:12px; }
.brand-logo { height:50px; width:auto; object-fit:contain; padding:0; }
.brand-text { display:flex; flex-direction:column; line-height:1.1; }
.brand-main { font-weight:bold; font-size:1.2rem; }
.brand-sub { font-size:0.85rem; opacity:0.85; }
.auth-links { margin-left:auto; }
.sidebar { width:200px; background:#1a237e; position:fixed; top:90px; left:-220px; bottom:0; padding-top:10px; transition:left 0.3s ease; z-index:999; }
.sidebar.show { left:0; }
.sidebar a { color:white; padding:12px 20px; display:block; text-decoration:none; }
.sidebar a:hover, .sidebar .active { background:#1565c0; }
.content { padding:110px 20px 20px 20px; transition:none; }
.search-box { margin-bottom:15px; max-width:300px; position:relative; }
.search-box i { position:absolute; top:10px; left:10px; color:#666; }
.search-box input { padding-left:30px; }
.btn-pinjam { padding:3px 8px; font-size:0.85rem; }
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

<div class="container content">
  <h3 class="mb-3">Daftar Barang</h3>
  <?php if(isset($_SESSION['message'])) { echo "<div class='alert alert-success'>".$_SESSION['message']."</div>"; unset($_SESSION['message']); } ?>

  <div class="search-box">
    <i class="bi bi-search"></i>
    <input type="text" id="searchInput" class="form-control" placeholder="Search barang...">
  </div>

  <table class="table table-bordered table-striped" id="barangTable">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Stok</th>
        <th>Satuan</th>
        <th>Kondisi</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
$q = mysqli_query($conn, "
  SELECT * FROM barang
  ORDER BY 
    CASE WHEN stok = 0 THEN 1 ELSE 0 END,
    stok DESC
");
      while($b = mysqli_fetch_assoc($q)):
      ?>
<tr>
<form method="POST" action="proses_pinjam.php">

  <td><?= $no++; ?></td>
  <td><?= $b['nama_barang']; ?></td>
  <td><?= $b['stok']; ?></td>
  <td><?= $b['satuan']; ?></td>
  <td><?= $b['kondisi']; ?></td>

  <td>
      <input type="hidden" name="id_barang" value="<?= $b['id_barang']; ?>">

      <input type="number"
        name="jumlah"
        min="1"
        max="<?= $b['stok']; ?>"
        class="form-control form-control-sm mb-1"
        placeholder="Jumlah pinjam"
        required
        <?= $b['stok'] == 0 ? 'disabled' : '' ?>>

      <button type="submit"
        name="pinjam"
        class="btn btn-primary w-100"
        <?= $b['stok'] == 0 ? 'disabled' : '' ?>>
        Pinjam
      </button>
  </td>

</form>
</tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
const toggleBtn = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
toggleBtn.addEventListener('click', ()=>{ sidebar.classList.toggle('show'); });

const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#barangTable tbody tr");
    rows.forEach(row => {
        row.style.display = Array.from(row.cells).some(td => td.textContent.toLowerCase().includes(filter)) ? '' : 'none';
    });
});
</script>

</body>
</html>
