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

$status_valid = ['Diterima', 'Dalam Proses', 'Sedang Diantar', 'Selesai'];

if ($id_pesanan > 0 && in_array($status_baru, $status_valid)) {
    $query = "UPDATE pesanan SET status_pengantaran = '$status_baru' WHERE id_pesanan = $id_pesanan";
    $hasil = mysqli_query($GLOBALS['conn'], $query);

    if ($hasil) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($GLOBALS['conn'])]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters. Status: ' . $status_baru]);
}
