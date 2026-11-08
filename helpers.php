<?php
function hapusBahanForTest($id_bahan)
{
    global $conn;
    $ids = (int)$id_bahan;
    mysqli_query($conn, "DELETE FROM bahan_baku WHERE id_bahan IN ($ids)");
    return mysqli_affected_rows($conn);
}

function hapusPelangganForTest($id_pelanggan)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM customer WHERE id_pelanggan = $id_pelanggan");
    return mysqli_affected_rows($conn);
}

function hapusPesananForTest($id_pesanan)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = $id_pesanan");
    return mysqli_affected_rows($conn);
}

function getCoordinates_test($address)
{
    return [
        'lat' => '-7.7828',
        'lng' => '110.3670'
    ];
}

function cleanUpTestData()
{
    global $conn;
    mysqli_query($conn, "DELETE FROM customer WHERE nama_pelanggan LIKE 'Test%'");
    mysqli_query($conn, "DELETE FROM pesanan WHERE catatan_khusus_pemesanan = 'test'");
    mysqli_query($conn, "DELETE FROM bahan_baku WHERE nama_bahan LIKE 'Test%'");
    mysqli_query($conn, "DELETE FROM menu WHERE nama_menu LIKE 'Test%'");
}

function hapusStokBahanForTest($id_stok)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM stok_bahan WHERE id_stok = $id_stok");
    return mysqli_affected_rows($conn);
}
