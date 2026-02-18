<?php
session_start();
require_once 'database.php';
require_once 'email_admin.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak');
}

$id   = (int)$_POST['id'];
$aksi = $_POST['aksi'];


// =====================
// JIKA TOLAK
// =====================
if ($aksi === 'tolak') {

    mysqli_query($conn, "
        UPDATE peminjaman
        SET status = 'ditolak'
        WHERE id = $id
    ");

    header("Location: status_peminjaman.php?id=$id");
    exit;
}


// =====================
// JIKA SETUJU
// =====================
if ($aksi === 'setuju') {

    $p = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT p.*, b.satuan
        FROM peminjaman p
        JOIN barang b ON p.id_barang = b.id_barang
        WHERE p.id=$id
    "));

    $jumlah = (int)$p['jumlah'];

    /* ===== JIKA UNIT (PAKAI SERIAL) ===== */
    if ($p['satuan'] === 'unit') {

        $qSerial = mysqli_query($conn,"
            SELECT id_serial, serial_number
            FROM serial_barang
            WHERE id_barang={$p['id_barang']}
            AND status='tersedia'
            LIMIT $jumlah
        ");

        if (mysqli_num_rows($qSerial) < $jumlah) {
            die('Serial barang tidak mencukupi');
        }

        $serials = [];
        $ids = [];

        while ($s = mysqli_fetch_assoc($qSerial)) {
            $serials[] = $s['serial_number'];
            $ids[] = $s['id_serial'];
        }

        mysqli_query($conn,"
            UPDATE serial_barang
            SET status='dipinjam'
            WHERE id_serial IN (".implode(',', $ids).")
        ");

        $serial_text = implode(', ', $serials);

    } else {
        $serial_text = NULL;
    }

    mysqli_query($conn,"
        UPDATE peminjaman SET
            status='dipinjam',
            satuan='{$p['satuan']}',
            serial_number=".($serial_text ? "'$serial_text'" : "NULL").",
            tgl_pinjam=CURDATE(),
            jam_pinjam=CURTIME()
        WHERE id=$id
    ");

    mysqli_query($conn,"
        UPDATE barang SET
            stok = stok - $jumlah
        WHERE id_barang={$p['id_barang']}
    ");

    header("Location: status_peminjaman.php?id=$id");
    exit;
}
