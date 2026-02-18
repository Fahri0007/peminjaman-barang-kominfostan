<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM barang WHERE id_barang=$id");

$_SESSION['message'] = "Barang berhasil dihapus!";
header("Location: index.php");
exit;
