<?php
session_start();
if (!isset($_SESSION["login"])) {
    http_response_code(401);
    exit;
}

require '../functions.php';

$input = json_decode(file_get_contents('php://input'), true);

$nama_bahan = $input['nama'] ?? '';
$jumlah_beli = (float)($input['jumlah'] ?? 0);
$checked = (int)($input['checked'] ?? 0);

if (empty($nama_bahan) || $jumlah_beli <= 0) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

if ($checked == 1) {
    $query = "UPDATE stok_bahan SET stok_tersedia = stok_tersedia + $jumlah_beli 
              WHERE nama_bahan_stok = '$nama_bahan'";
    mysqli_query($conn, $query);
    $updateStatus = "UPDATE detail_pesanan_bahan dpb 
                     JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
                     SET dpb.status_beli = 1 
                     WHERE bb.nama_bahan = '$nama_bahan' AND dpb.status_beli = 0";
    mysqli_query($conn, $updateStatus);
} else {
    $query = "UPDATE stok_bahan SET stok_tersedia = stok_tersedia - $jumlah_beli 
              WHERE nama_bahan_stok = '$nama_bahan' AND stok_tersedia >= $jumlah_beli";
    mysqli_query($conn, $query);

    $updateStatus = "UPDATE detail_pesanan_bahan dpb 
                     JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
                     SET dpb.status_beli = 0 
                     WHERE bb.nama_bahan = '$nama_bahan' AND dpb.status_beli = 1";
    mysqli_query($conn, $updateStatus);
}

echo json_encode(['success' => true]);
