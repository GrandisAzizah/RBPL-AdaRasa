<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$pesanan = query("SELECT * FROM pesanan ORDER BY tanggal_pesan ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pesanan.css">
</head>

<body>
    <div class="container-main">
        <div class="header-nav mt-3">
            <a href="berandaAdmin.php" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title" style="margin: 0;">Detail</h5>
            <a href="inputPesanan.php" class="header-nav-right">
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
            <div class="btn-group col-auto" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="btnradio1">
                    <svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.275 9.01875L0 4.74375L1.06875 3.675L4.275 6.88125L11.1563 0L12.225 1.06875L4.275 9.01875Z" fill="#4A4459" />
                    </svg>
                    <a href="">Pesanan</a>
                </label>
                <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                <label class="btn btn-outline-primary" for="btnradio2"><a href="pelanggan.php">Pelanggan</a></label>
            </div>
        </div>

        <!-- card untuk pesanan -->
        <!-- card untuk pelanggan -->
        <?php if (count($pesanan) == 0) : ?>
            <p class="text-center mt-5" style="color: #979696; margin-top: 20px; height: 70vh; display: flex; align-items: center; justify-content: center;">Belum ada data pesanan yang ditambahkan</p>
        <?php else : ?>
            <?php foreach ($pesanan as $row) : ?>
                <div class="card-order">
                    <div class="row g-0">
                        <!-- Isi -->
                        <div class="col-auto">
                            <div class="card-body">
                                <h5 class="card-title"><?= ['nama_menu'] ?></h5>
                                <p class="card-text"><?= ['takaran'] ?></p>
                            </div>
                        </div>

                        <!-- Gambar -->
                        <div class="order-img col">
                            <img src="../rbpl nasi kuning.png" class="" alt="...">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
</body>

</html>