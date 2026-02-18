<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role    = $_SESSION['role'];

/* ================= PROSES KEMBALIKAN ================= */
if (isset($_POST['kembalikan'])) {

    $id = intval($_POST['id']);
    $tgl_kembali = date('Y-m-d');
    $jam_kembali = date('H:i:s');

    // ambil data peminjaman
    if ($role === 'admin') {
        $sql = "SELECT * FROM peminjaman WHERE id=$id AND status='dipinjam'";
    } else {
        $sql = "SELECT * FROM peminjaman 
                WHERE id=$id AND id_user=$id_user AND status='dipinjam'";
    }

    $result = mysqli_query($conn, $sql);
    $peminjaman = mysqli_fetch_assoc($result);

    if ($peminjaman) {

        // 1️⃣ update status peminjaman
        mysqli_query($conn, "
            UPDATE peminjaman SET
                tgl_kembali='$tgl_kembali',
                jam_kembali='$jam_kembali',
                status='dikembalikan'
            WHERE id=$id
        ");

        // 2️⃣ kembalikan stok barang
        mysqli_query($conn, "
            UPDATE barang SET
                stok = stok + {$peminjaman['jumlah']}
            WHERE id_barang = {$peminjaman['id_barang']}
        ");

        // 3️⃣ kembalikan SERIAL berdasarkan serial_number (FIX)
        if (!empty($peminjaman['serial_number'])) {
            $serials = explode(',', $peminjaman['serial_number']);
            $serials = array_map('trim', $serials);

            $list = "'" . implode("','", $serials) . "'";

            mysqli_query($conn, "
                UPDATE serial_barang SET
                    status='tersedia'
                WHERE serial_number IN ($list)
                AND id_barang = {$peminjaman['id_barang']}
            ");
        }
    }

    header("Location: pengembalian.php");
    exit;
}

/* ================= DATA TABEL ================= */
$query = "
SELECT 
    p.id,
    p.tgl_pinjam,
    p.jam_pinjam,
    p.tgl_kembali,
    p.jam_kembali,
    p.status,
    p.jumlah,
    p.satuan,
    p.serial_number AS serials,

    b.nama_barang,
    b.kondisi,
    b.lokasi

FROM peminjaman p
JOIN barang b ON p.id_barang = b.id_barang
";

if ($role !== 'admin') {
    $query .= " WHERE p.id_user = $id_user ";
}

$query .= "
ORDER BY 
    CASE WHEN p.status='dipinjam' THEN 1 ELSE 2 END,
    p.id DESC
";

$data = mysqli_query($conn, $query);
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengembalian Barang</title>
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
table th, table td { vertical-align:middle; }
.search-box { margin-bottom:15px; max-width:300px; position:relative; }
.search-box i { position:absolute; top:10px; left:10px; color:#666; }
.search-box input { padding-left:30px; }
.btn-kembali { padding:3px 8px; font-size:0.85rem; }
</style>
</head>
<body>

<div class="topbar">
  <button id="toggleSidebar"><i class="bi bi-list"></i></button>
  <div class="brand-title">
    <img src="assets/img/123.png" class="brand-logo" alt="Logo">
    <div class="brand-text">
      <span class="brand-main">Pengembalian Barang</span>
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
  <h3 class="mb-3">Daftar Peminjaman</h3>
  <?php if(isset($_SESSION['message'])) { echo "<div class='alert alert-success'>".$_SESSION['message']."</div>"; unset($_SESSION['message']); } ?>

  <div class="search-box">
    <i class="bi bi-search"></i>
    <input type="text" id="searchInput" class="form-control" placeholder="Search peminjaman...">
  </div>
    <table class="table table-bordered table-striped" id="peminjamanTable">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Barang</th>
          <th>Satuan</th>
          <th>Kondisi</th>
          <th>Lokasi Barang</th>
          <th>Tgl Pinjam</th>
          <th>Jam Pinjam</th>   
          <th>Tgl Kembali</th>
          <th>Jam Kembali</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
  $no = 1;
  while($row = mysqli_fetch_assoc($data)):
  ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_barang']); ?></td>
          <td><?= htmlspecialchars($row['satuan']); ?></td>
          <td><?= htmlspecialchars($row['kondisi']); ?></td>
          <td><?= htmlspecialchars($row['lokasi']); ?></td> 
          <td><?= $row['tgl_pinjam'] ? date('d-m-Y', strtotime($row['tgl_pinjam'])) : '-'; ?></td>
          <td><?= $row['jam_pinjam'] ? $row['jam_pinjam'] : '-'; ?></td>
          <td><?= $row['tgl_kembali'] ?: '-'; ?></td>
          <td><?= $row['jam_kembali'] ?: '-'; ?></td>
          <td><?= ucfirst($row['status']); ?></td>
<td>
<div class="d-flex gap-2 align-items-center flex-wrap">

<?php if ($row['status'] == 'menunggu'): ?> 
    <a href="status_peminjaman.php?id=<?= $row['id']; ?>" 
       class="btn btn-sm btn-info">
       Detail
    </a>
    <span class="badge bg-warning text-dark">
        Menunggu
    </span>
<?php elseif ($row['status'] == 'ditolak'): ?>
    <a href="status_peminjaman.php?id=<?= $row['id']; ?>" 
       class="btn btn-sm btn-info">
       Detail
    </a>
    <span class="badge bg-danger">
        Ditolak
    </span>
<?php elseif ($row['status'] == 'dipinjam'): ?>
    <a href="status_peminjaman.php?id=<?= $row['id']; ?>" 
       class="btn btn-sm btn-info">
       Detail
    </a>
    <form method="POST"
          onsubmit="return confirm('Apakah barang ingin dikembalikan?')">
      <input type="hidden" name="id" value="<?= $row['id']; ?>">
      <button type="submit" name="kembalikan"
              class="btn btn-success btn-sm">
        Kembalikan
      </button>
    </form>
<?php elseif ($row['status'] == 'dikembalikan'): ?>
    <span class="badge bg-secondary">
        Selesai
    </span>
<?php endif; ?>
</div>
</td>
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
    const rows = document.querySelectorAll("#peminjamanTable tbody tr");
    rows.forEach(row => {
        row.style.display = Array.from(row.cells).some(td => td.textContent.toLowerCase().includes(filter)) ? '' : 'none';
    });
});
</script>

</body>
</html>
