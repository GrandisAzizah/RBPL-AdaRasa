<?php

session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: ../login.php");
    exit;
}

require '../functions.php';
// ambil id yang dikirim ketika tombol dipencet
// id dikirim lewat url
$id_pelanggan = $_GET["id_pelanggan"];

if (hapusPelanggan($id_pelanggan) > 0) {
    header("location: pelanggan.php");
} else {
    echo "Data gagal dihapus";
    // menampilkan kenapa gagal
    echo mysqli_error($conn);
}
