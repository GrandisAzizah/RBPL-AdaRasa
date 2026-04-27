<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

$_SESSION['pesanan'] = [
    'fk_customer' => $_POST['nama_pelanggan'],
    'fk_menu' => $_POST['nama_pesanan'],
    'fk_pesanan_varian' => !empty($_POST['takaran']) ? (int)$_POST['takaran'] : null,
    'jumlah' => (int)$_POST['jumlah'],
    'metode' => $_POST['metode_pengantaran'],
    'tanggal_antar' => $_POST['tanggal_antar'],
    'catatan' => $_POST['catatan_khusus_pemesanan'],
];

header('location: inputBahanPesan.php');
exit;
