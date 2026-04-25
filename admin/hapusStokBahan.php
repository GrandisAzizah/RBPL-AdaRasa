<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: ../login.php");
    exit;
}

require '../functions.php';

$id_stok = isset($_GET["id_stok"]) ? (int)$_GET["id_stok"] : 0;

if ($id_stok > 0 && hapusStokBahan($id_stok) > 0) {
    header("location: listBahanTersedia.php");
} else {
    echo "Data gagal dihapus";
}
