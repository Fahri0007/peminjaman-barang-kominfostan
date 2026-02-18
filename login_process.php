<?php
session_start();
require_once 'database.php';

$aksi = $_POST['aksi'] ?? 'login';

// ================= REGISTER =================
if ($aksi === 'register') {
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $unit_kerja = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    $nama_dinas = mysqli_real_escape_string($conn, $_POST['nama_dinas']);

    // CEK KE TABEL users
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['message'] = "Username sudah digunakan!";
        header("Location: register.php");
        exit;
    }

    // INSERT KE users
    mysqli_query($conn, "
        INSERT INTO users (nama, username, password, role, unit_kerja, nama_dinas)
        VALUES ('$nama', '$username', '$password', 'user', '$unit_kerja', '$nama_dinas')
    ");

    $_SESSION['message'] = "Registrasi berhasil, silakan login!";
    header("Location: login.php");
    exit;
}

if ($aksi === 'login') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE BINARY username=? 
        LIMIT 1
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['login']      = true;
        $_SESSION['id_user']    = $user['id_user'];
        $_SESSION['nama']       = $user['nama'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['unit_kerja'] = $user['unit_kerja'];
        $_SESSION['nama_dinas'] = $user['nama_dinas'];

        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = "Username atau Password salah!";
        header("Location: login.php");
        exit;
    }
}

