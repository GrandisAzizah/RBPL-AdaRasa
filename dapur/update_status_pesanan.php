<?php
session_start();
require '../functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION["login"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_pesanan = isset($_POST['id_pesanan']) ? (int)$_POST['id_pesanan'] : 0;
$status_baru = isset($_POST['status']) ? $_POST['status'] : '';

$status_valid = ['Diterima', 'Diproses', 'Selesai', 'Diantar'];

if ($id_pesanan > 0 && in_array($status_baru, $status_valid)) {

    // Update status_pemesanan
    $query = "UPDATE pesanan SET status_pemesanan = '$status_baru' WHERE id_pesanan = $id_pesanan";
    $hasil = mysqli_query($GLOBALS['conn'], $query);

    if ($hasil) {
        // Cek metode pengantaran ketika status Selesai
        if ($status_baru == 'Selesai') {
            $cek = query("SELECT metode_pengantaran FROM pesanan WHERE id_pesanan = $id_pesanan");
            $metode = $cek[0]['metode_pengantaran'] ?? '';

            if ($metode == 'Kurir Catering') {
                mysqli_query($GLOBALS['conn'], "UPDATE pesanan SET status_pengantaran = 'Diterima' WHERE id_pesanan = $id_pesanan");
            } else {
                mysqli_query($GLOBALS['conn'], "UPDATE pesanan SET status_pengantaran = 'Selesai' WHERE id_pesanan = $id_pesanan");
            }
        }
        // Status lain tidak mengubah status_pengantaran

        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid']);
}
