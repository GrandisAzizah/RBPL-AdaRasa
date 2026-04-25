<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$bahan_tersedia = query("SELECT * FROM stok_bahan ORDER BY nama_bahan_stok ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Bahan Baku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pesananAdmin.css">
</head>

<body>
    <div class="container-main">
        <div class="header-nav mt-3">
            <a href="berandaAdmin.php" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title" style="margin: 0;">Stok Bahan Baku</h5>
            <a href="inputStokBahan.php" class="header-nav-right">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.91669 18.9998H30.0834M19 7.9165V30.0832" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>

        <!-- tombol navigasi pelanggan dan pesanan -->
        <div class="btn-nav card row g-0">
            <div class="sort col-auto">
                <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.33333 9.16667V3.1875L1.1875 5.33333L0 4.16667L4.16667 0L8.33333 4.16667L7.14583 5.33333L5 3.1875V9.16667H3.33333ZM9.16667 16.6667L5 12.5L6.1875 11.3333L8.33333 13.4792V7.5H10V13.4792L12.1458 11.3333L13.3333 12.5L9.16667 16.6667Z" fill="black" />
                </svg>
                <span>Urutkan</span>
            </div>
        </div>
        <div class="btn-group-bahan" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio1">
                <a href="listBelanjaBahan.php">List Belanja Bahan</a>
            </label>
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="btnradio2">
                <svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.275 9.01875L0 4.74375L1.06875 3.675L4.275 6.88125L11.1563 0L12.225 1.06875L4.275 9.01875Z" fill="#4A4459" />
                </svg>
                <a href="listBahanTersedia.php">List Bahan Tersedia</a>
            </label>
        </div>

        <?php if (empty($bahan_tersedia)): ?>
            <p class="text-center mt-5" style="color: #979696; margin-top: 20px; height: 70vh; display: flex; align-items: center; justify-content: center;">Belum ada data stok bahan baku yang ditambahkan</p>
        <?php else: ?>
            <?php foreach ($bahan_tersedia as $b): ?>
                <div class="card-bahan">
                    <div class="row g-0 align-items-center">
                        <!-- Isi -->
                        <div class="col">
                            <div class="card-body-bahan">
                                <h5 class="card-title-bahan"><?= $b['nama_bahan_stok'] ?></h5>
                                <p class="card-text-bahan">Stok: <?= $b['stok_tersedia'] . ' ' . $b['satuan'] ?></p>
                            </div>
                        </div>
                        <div class="col-auto me-3">
                            <a href="editStokTersedia.php?id_stok=<?= $b['id_stok'] ?>" class="edit-btn btn btn-dark btn-sm">Edit</a>
                            <a href="#" class="delete-btn btn btn-danger btn-sm" onclick="setHapusUrl('hapusStokBahan.php?id_stok=<?= $b['id_stok'] ?>')">Hapus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="modalHapus" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <p>Yakin ingin menghapus bahan ini?</p>
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