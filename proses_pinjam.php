<?php
session_start();
require_once 'database.php';
require_once 'email_admin.php';

if (!isset($_SESSION['id_user'])) die('Login dulu');
if (!isset($_POST['pinjam'])) die('Akses tidak valid');

$id_user   = $_SESSION['id_user'];
$id_barang = (int)$_POST['id_barang'];
$jumlah    = (int)$_POST['jumlah'];

$nama  = $_SESSION['nama'];
$unit  = $_SESSION['unit_kerja'];
$dinas = $_SESSION['nama_dinas'];

$kode_pinjam = 'PMJ' . date('YmdHis');

/* ambil data barang */
$barang = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM barang WHERE id_barang=$id_barang
"));
if (!$barang) die('Barang tidak ditemukan');

$satuan = $barang['satuan'];

/* CEK KHUSUS JIKA UNIT (HANYA VALIDASI) */
if ($satuan === 'unit') {

    $qSerial = mysqli_query($conn,"
        SELECT id_serial
        FROM serial_barang
        WHERE id_barang=$id_barang
        AND status='tersedia'
        LIMIT $jumlah
    ");

    if (mysqli_num_rows($qSerial) < $jumlah) {
        die('Unit tersedia tidak mencukupi');
    }
}

/* ================= INSERT PEMINJAMAN (WAJIB) ================= */
mysqli_query($conn,"
    INSERT INTO peminjaman (
        kode_pinjam,
        id_user,
        id_barang,
        nama_peminjam,
        unit_kerja,
        nama_dinas,
        jumlah,
        satuan,
        status
    ) VALUES (
        '$kode_pinjam',
        $id_user,
        $id_barang,
        '$nama',
        '$unit',
        '$dinas',
        $jumlah,
        '$satuan',
        'menunggu'
    )
");
$id_peminjaman = mysqli_insert_id($conn);
if ($id_peminjaman == 0) {
    die('Gagal menyimpan peminjaman');
}
/* ================= KIRIM EMAIL ================= */
kirimEmailAdmin($id_peminjaman);
header("Location: status_peminjaman.php?id=$id_peminjaman");
exit;
