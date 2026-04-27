<?php

session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';
// Ambil id_pesanan dari URL
$id_pesanan = isset($_GET['id_pesanan']) ? (int)$_GET['id_pesanan'] : 0;

if ($id_pesanan == 0) {
    echo "ID Pesanan tidak valid";
    exit;
}

$pesanan = query("SELECT 
    p.id_pesanan, p.jumlah, p.harga_total, p.tanggal_pesan, p.tanggal_antar,
    p.catatan_khusus_pemesanan, p.metode_pengantaran,
    mv.takaran, m.nama_menu, m.gambar_menu,
    c.nama_pelanggan, c.alamat,
    MIN(dp.packing) as packing,
    GROUP_CONCAT(bb.nama_bahan, ' ', dp.jumlah_dipakai, ' ', bb.satuan SEPARATOR ', ') as bahan_baku
FROM pesanan p
JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
JOIN menu m ON mv.fk_menu_varian = m.id_menu
JOIN customer c ON p.fk_pesanan_customer = c.id_pelanggan
LEFT JOIN detail_pesanan_bahan dp ON dp.fk_detail_pesanan = p.id_pesanan
LEFT JOIN bahan_baku bb ON dp.fk_bahan_detail = bb.id_bahan
WHERE p.id_pesanan = $id_pesanan
GROUP BY p.id_pesanan");

// Cek apakah data ditemukan
if (count($pesanan) == 0) {
    echo "Pesanan tidak ditemukan";
    exit;
}

$row = $pesanan[0]; // Ambil data pertama

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Pesanan</title>
    <link rel="stylesheet" href="menu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container-main">
        <div class="header-nav mt-3">
            <a href="pesanan.php" class="header-nav-left">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title" style="margin: 0;">Detail Pesanan <?= $row['nama_pelanggan'] ?></h5>
        </div>


        <?php if (count($pesanan) == 0) : ?>
            <p class="text-center mt-5" style="color: #979696; margin-top: 20px; height: 70vh; display: flex; align-items: center; justify-content: center;">Belum ada pesanan yang ditambahkan</p>
        <?php else : ?>
            <?php foreach ($pesanan as $row) : ?>
                <div class=" container-menu mt-4">
                    <!-- Isi -->
                    <div class="col">
                        <div class="card-body">
                            <div style="display: flex; justify-content:center;">
                                <img src="<?= $row['gambar_menu'] ?>" alt="" style="width: 200px; height: 200px;">
                            </div>
                            <p class="card-text mt-4 mb-3"><strong>Menu dipesan:</strong> <br><?= $row['nama_menu'] ?></p>
                            <p class="card-text mb-3"><strong>Jumlah pesan:</strong> <br><?= $row['jumlah'] . ' ' . $row['packing'] ?></p>
                            <p class="card-text mb-3"><strong>Harga Total:</strong> <br>Rp <?= number_format($row['harga_total'], 0, ',', '.') ?></p>
                            <p class="card-text mb-3"><strong>Catatan</strong><br> <?= $row['catatan_khusus_pemesanan'] ?></p>
                            <p class="card-text mb-3"><strong>Takaran:</strong><br> <?= $row['takaran'] ?></p>
                            <p class="card-text mb-3"><strong>Packing: </strong><br> <?= $row['packing'] ?></strong></p>
                            <p class="card-text mb-3"><strong>Nama Pelanggan:</strong><br> <?= $row['nama_pelanggan'] ?></p>
                            <p class="card-text mb-3"><strong>Alamat:</strong><br> <?= $row['alamat'] ?></strong></p>
                            <p class="card-text mb-2"><strong>Dipesan pada <?= $row['tanggal_pesan'] ?></strong></p>
                            <p class="card-text mb-3"><strong>Pesanan untuk <?= $row['tanggal_antar'] ?></strong></p>
                            <p class="card-text mb-3"><strong>Bahan Baku:</strong><br> <?= $row['bahan_baku'] ?></p>
                        </div>
                    </div>
                    <!-- Tombol Edit dan Hapus -->
                    <div class="col-auto menu-btn d-flex align-items-center gap-2 p-2 align-self-end">
                        <a href="editPesanan.php?id_pesanan=<?= $row['id_pesanan'] ?>" class="edit-btn btn btn-dark btn-sm">Edit</a>
                        <a href="#" class="delete-btn btn btn-danger btn-sm" onclick="setHapusUrl('hapusPesanan.php?id_pesanan=<?= $row['id_pesanan'] ?>&id_pesanan=<?= $id_pesanan ?>')">Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="modalHapus" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <p>Yakin ingin menghapus data pesanan ini?</p>
                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <button class="btn btn-dark" data-bs-dismiss="modal" style="width: auto !important; height: auto !important; padding: 6px 20px !important;">Tidak</button>
                            <a id="btnYaHapus" href="#" class="btn btn-danger" style="width: auto !important; height: auto !important; padding: 6px 20px !important;">Ya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function setHapusUrl(url) {
                document.getElementById('btnYaHapus').href = url;
                new bootstrap.Modal(document.getElementById('modalHapus')).show();
            }
        </script>
</body>

</html>