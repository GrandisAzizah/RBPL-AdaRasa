<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: ../login.php");
    exit;
}
require '../functions.php';

$id_pesanan = (int)$_GET['id_pesanan'];

if (hapusPesanan($id_pesanan) > 0) {
    header("location: pesanan.php");
} else {
    echo "Data gagal dihapus: " . mysqli_error($conn);
}
