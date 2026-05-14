<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: ../login.php");
    exit;
}

require '../functions.php';
require '../alamat.php';

$id_pelanggan = isset($_GET["id_pelanggan"]) ? (int)$_GET["id_pelanggan"] : 0;

if ($id_pelanggan > 0 && hapusPelanggan($id_pelanggan) > 0) {
    header("location: pelanggan.php");
} else {
    echo "Data gagal dihapus";
}
