<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'User';

/* ================== ID BARANG ================== */
$id_barang = isset($_GET['id_barang']) ? intval($_GET['id_barang']) : 0;

/* ================== BARANG UNIT ================== */
$barang = mysqli_query($conn, "
    SELECT id_barang, nama_barang
    FROM barang
    WHERE is_serial = 1
    ORDER BY nama_barang ASC
");

/* ================== TAMBAH ================== */
if (isset($_POST['tambah_serial']) && $role === 'admin') {

    $id_barang     = intval($_POST['id_barang']);
    $serial_number = trim($_POST['serial_number']);

    $cek = mysqli_query($conn, "
        SELECT id_serial FROM serial_barang
        WHERE serial_number = '$serial_number'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) > 0) {
        header("Location: serial.php?id_barang=$id_barang&error=duplikat");
        exit;
    }

    mysqli_query($conn, "
        INSERT INTO serial_barang (id_barang, serial_number, status)
        VALUES ($id_barang, '$serial_number', 'tersedia')
    ");

    header("Location: serial.php?id_barang=$id_barang&success=1");
    exit;
}

/* ================== HAPUS ================== */
if (isset($_GET['hapus']) && $role === 'admin') {
    $id_hapus = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM serial_barang WHERE id_serial = $id_hapus");
    header("Location: serial.php?id_barang=$id_barang");
    exit;
}

/* ================== EDIT ================== */
if (isset($_POST['edit_serial']) && $role === 'admin') {

    $id_serial     = intval($_POST['id_serial']);
    $serial_number = trim($_POST['serial_number']);

    mysqli_query($conn, "
        UPDATE serial_barang
        SET serial_number = '$serial_number'
        WHERE id_serial = $id_serial
    ");

    header("Location: serial.php?id_barang=$id_barang");
    exit;
}

/* ================== DATA SERIAL ================== */
$serial = false;
if ($id_barang > 0) {
    $serial = mysqli_query($conn, "
        SELECT * FROM serial_barang
        WHERE id_barang = $id_barang
        ORDER BY serial_number ASC
    ");
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Serial Barang</title>
<link rel="icon" href="assets/img/123.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { margin:0; background:#f4f6f9; font-family:Arial; }

.topbar{
  height:90px;
  background:#003a8f;
  color:white;
  display:flex;
  align-items:center;
  gap:15px;
  padding:0 20px;
  position:fixed;
  top:0; left:0; right:0;
  z-index:1000;
}

#toggleSidebar{
  background:none;
  border:none;
  color:white;
  font-size:1.8rem;
}

.brand-logo{ height:50px; }

.brand-main{ font-weight:bold; font-size:1.2rem; }

.auth-links{ margin-left:auto; }

.sidebar{
  width:200px;
  background:#1a237e;
  position:fixed;
  top:90px;
  left:-220px;
  bottom:0;
  padding-top:10px;
  transition:.3s;
}

.sidebar.show{ left:0; }

.sidebar a{
  color:white;
  padding:12px 20px;
  display:block;
  text-decoration:none;
}

.sidebar a:hover,
.sidebar .active{
  background:#1565c0;
}

.content{
  padding:120px 25px 30px;
}

.card-box{
  background:white;
  border-radius:14px;
  padding:20px;
}
</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
  <button id="toggleSidebar"><i class="bi bi-list"></i></button>

  <img src="assets/img/123.png" class="brand-logo">

  <div>
    <div class="brand-main">Serial Barang</div>
    <small>DISKOMINFOSTAN</small>
  </div>

  <div class="auth-links">
    <?= htmlspecialchars($nama); ?>
    <a href="logout.php" class="btn btn-sm btn-light ms-3">Logout</a>
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <a href="index.php"><i class="bi bi-grid-fill me-2"></i> Daftar Barang</a>
  <a href="peminjaman.php"><i class="bi bi-arrow-left-right me-2"></i> Peminjaman</a>
  <a href="pengembalian.php"><i class="bi bi-arrow-return-left me-2"></i> Pengembalian</a>

  <?php if($role === 'admin'): ?>
    <a href="dashboard.php"><i class="bi bi-bar-chart-line-fill me-2"></i> Dashboard</a>
    <a href="serial.php" class="active"><i class="bi bi-upc-scan me-2"></i> Serial Barang</a>
  <?php endif; ?>
</div>

<!-- CONTENT -->
<div class="container-fluid content">

<div class="card-box shadow">

<div class="d-flex justify-content-center align-items-center mb-4">
    <h5 class="mb-0">Kelola Nomor Seri Barang</h5>
</div>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger">Nomor seri sudah ada</div>
<?php elseif (isset($_GET['success'])): ?>
<div class="alert alert-success">Serial berhasil ditambahkan</div>
<?php endif; ?>

<form method="get" class="mb-3">
    <label class="fw-bold">Pilih Barang Unit</label>
    <select name="id_barang" class="form-select" onchange="this.form.submit()">
        <option value="">-- Pilih Barang --</option>
        <?php while ($b = mysqli_fetch_assoc($barang)) : ?>
            <option value="<?= $b['id_barang']; ?>"
                <?= $id_barang == $b['id_barang'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['nama_barang']); ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if ($id_barang > 0): ?>

<?php if($role === 'admin'): ?>
<form method="post" class="row g-2 mb-3">
    <input type="hidden" name="id_barang" value="<?= $id_barang ?>">
    <div class="col-md-8">
        <input type="text" name="serial_number" class="form-control"
               placeholder="Contoh: XXX-XX-001" required>
    </div>
    <div class="col-md-4 d-grid">
        <button class="btn btn-success" name="tambah_serial">
            <i class="bi bi-plus-circle"></i> Tambah Serial
        </button>
    </div>
</form>
<?php endif; ?>

<table class="table table-bordered table-striped">
<thead class="table-primary">
<tr>
    <th width="50">No</th>
    <th>Nomor Seri</th>
    <th>Status</th>
    <?php if($role === 'admin'): ?>
    <th width="120">Aksi</th>
    <?php endif; ?>
</tr>
</thead>
<tbody>

<?php
$no = 1;
if ($serial && mysqli_num_rows($serial) > 0):
while ($s = mysqli_fetch_assoc($serial)):
?>
<tr>
<td><?= $no++ ?></td>

<td>
<?php if($role === 'admin'): ?>
<form method="post" class="d-flex">
    <input type="hidden" name="id_serial" value="<?= $s['id_serial'] ?>">
    <input type="text" name="serial_number"
        value="<?= htmlspecialchars($s['serial_number']) ?>"
        class="form-control form-control-sm me-2" required>
    <button class="btn btn-warning btn-sm" name="edit_serial">
        <i class="bi bi-pencil"></i>
    </button>
</form>
<?php else: ?>
<?= htmlspecialchars($s['serial_number']) ?>
<?php endif; ?>
</td>

<td>
<span class="badge bg-<?= $s['status']=='tersedia'?'success':'warning' ?>">
<?= $s['status'] ?>
</span>
</td>

<?php if($role === 'admin'): ?>
<td class="text-center">
<a href="serial.php?id_barang=<?= $id_barang ?>&hapus=<?= $s['id_serial'] ?>"
   onclick="return confirm('Yakin hapus serial ini?')"
   class="btn btn-danger btn-sm">
   <i class="bi bi-trash"></i>
</a>
</td>
<?php endif; ?>

</tr>
<?php endwhile; else: ?>
<tr>
<td colspan="4" class="text-center text-muted">
Belum ada serial
</td>
</tr>
<?php endif; ?>

</tbody>
</table>

<?php endif; ?>

</div>
</div>

<script>
document.getElementById('toggleSidebar')
.addEventListener('click', function(){
    document.getElementById('sidebar')
    .classList.toggle('show');
});
</script>

</body>
</html>
