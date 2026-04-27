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

// if ($checked == 1) {
//     $query = "UPDATE stok_bahan SET stok_tersedia = stok_tersedia + $jumlah_beli 
//               WHERE nama_bahan_stok = '$nama_bahan'";
//     mysqli_query($conn, $query);
//     $updateStatus = "UPDATE detail_pesanan_bahan dpb 
//                      JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
//                      SET dpb.status_beli = 1 
//                      WHERE bb.nama_bahan = '$nama_bahan' AND dpb.status_beli = 0";
//     mysqli_query($conn, $updateStatus);
// } else {
//     $query = "UPDATE stok_bahan SET stok_tersedia = stok_tersedia - $jumlah_beli 
//               WHERE nama_bahan_stok = '$nama_bahan' AND stok_tersedia >= $jumlah_beli";
//     mysqli_query($conn, $query);

//     $updateStatus = "UPDATE detail_pesanan_bahan dpb 
//                      JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
//                      SET dpb.status_beli = 0 
//                      WHERE bb.nama_bahan = '$nama_bahan' AND dpb.status_beli = 1";
//     mysqli_query($conn, $updateStatus);
// }

$stmt = $conn->prepare(
    $checked == 1
        ? "UPDATE stok_bahan SET stok_tersedia = stok_tersedia + ? WHERE nama_bahan_stok = ?"
        : "UPDATE stok_bahan SET stok_tersedia = stok_tersedia - ? WHERE nama_bahan_stok = ? AND stok_tersedia >= ?"
);
if ($checked == 1) {
    $stmt->bind_param('ds', $jumlah_beli, $nama_bahan);
} else {
    $stmt->bind_param('dsi', $jumlah_beli, $nama_bahan, $jumlah_beli);
}
$stmt->execute();
$stmt->close();

$statusQry = $conn->prepare(
    "UPDATE detail_pesanan_bahan dpb 
     JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
     SET dpb.status_beli = ? 
     WHERE bb.nama_bahan = ? AND dpb.status_beli <> ?"
);
$newStatus = $checked == 1 ? 1 : 0;
$oldStatus = $checked == 1 ? 0 : 1;
$statusQry->bind_param('isi', $newStatus, $nama_bahan, $oldStatus);
$statusQry->execute();
$statusQry->close();

echo json_encode(['success' => true]);
