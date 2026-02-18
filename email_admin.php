<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once 'database.php';
function kirimEmailAdmin($id_peminjaman): bool {
    global $conn;

    $q = mysqli_query($conn, "
        SELECT p.*, b.nama_barang 
        FROM peminjaman p
        LEFT JOIN barang b ON p.id_barang = b.id_barang
        WHERE p.id = $id_peminjaman
        LIMIT 1
    ");

    if (!$q || mysqli_num_rows($q) == 0) {
        return false;
    }

    $p = mysqli_fetch_assoc($q);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fahrirkt7@gmail.com';
        $mail->Password   = 'shoywzbbslfssmsc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('fahrirkt7@gmail.com', 'Sistem Peminjaman');
        $mail->addAddress('fahrirkt7@gmail.com');

        $link_dashbord = "http://localhost/barang/dashboard.php";
        $mail->isHTML(true);
        $mail->Subject = 'Peminjaman Baru - ' . $p['nama_barang'];
        $mail->Body = "
            <h2>Notifikasi Peminjaman</h2>
            <table border='1' cellpadding='5'>
            <tr><td>Nama</td><td>{$p['nama_peminjam']}</td></tr>
            <tr><td>Nama Dinas</td><td>{$p['nama_dinas']}</td></tr>
            <tr><td>Unit Kerja</td><td>{$p['unit_kerja']}</td></tr>
            <tr><td>Barang</td><td>{$p['nama_barang']}</td></tr>
            <tr><td>Jumlah</td><td>{$p['jumlah']} {$p['satuan']}</td></tr>
            <tr><td>Status</td><td>{$p['status']}</td></tr>
            </table>

            <br><br>

            <a href='$link_dashbord' 
            style='padding:15px 15px;background:#008B8B;color:white;text-decoration:none;border-radius:5px;margin-top:-10px;'>
            Dashboard ->
            </a>
        ";
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->send();
        return true;

    } catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
    exit;
}

}
       