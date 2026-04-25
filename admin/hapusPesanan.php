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
$id_pesanan = $_GET["id_pesanan"];

if (hapusPesanan($id_pesanan) > 0) {
    header("location: showBahan.php?id_menu=" . $id_menu);
} else {
    echo "Data gagal dihapus";
    // menampilkan kenapa gagal
    echo mysqli_error($conn);
}
