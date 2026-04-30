<?php

session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}
require '../functions.php';

$pesanan_terbaru = query("SELECT p.*, c.nama_pelanggan, mv.takaran,
m.nama_menu, m.gambar_menu FROM pesanan p 
LEFT JOIN customer c ON p.fk_pesanan_customer = c.id_pelanggan
LEFT JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
LEFT JOIN menu m ON mv.fk_menu_varian = m.id_menu
WHERE p.status_pemesanan = 'Diproses'
ORDER BY p.tanggal_pesan ASC");

$id_pesanan = isset($_GET['id_pesanan']) ? (int)$_GET['id_pesanan'] : 0;
$status_baru = isset($_GET['status']) ? $_GET['status'] : '';

$status_valid = ['Diterima', 'Diproses', 'Selesai', 'Diantar'];
if ($id_pesanan > 0 && in_array($status_baru, $status_valid)) {
    // Update status pesanan
    $sql = "UPDATE pesanan SET status_pemesanan = '$status_baru' WHERE id_pesanan = $id_pesanan";
    query($sql);
}
// Kelompokkan pesanan berdasarkan tanggal
$pesanan_per_tanggal = [];
foreach ($pesanan_terbaru as $p) {
    $tanggal = date('d F Y', strtotime($p['tanggal_pesan']));
    if (!isset($pesanan_per_tanggal[$tanggal])) {
        $pesanan_per_tanggal[$tanggal] = [];
    }
    $pesanan_per_tanggal[$tanggal][] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Page Pesanan Tim Dapur</title>
    <link rel="stylesheet" href="../pesanan.css">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Aleo', serif;
    }

    .card {
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    }

    .nav-underline .nav-link {
        color: #000 !important;
        font-size: 18px;
    }

    .nav-underline .nav-link.active {
        color: #000 !important;
        border-bottom: 2px solid #000 !important;
    }

    .nav-scroll {
        overflow-x: auto;
        /* Biar bisa scroll horizontal */
        overflow-y: hidden;
        white-space: nowrap;
        /* Biar ga wrap/turun */
        -webkit-overflow-scrolling: touch;
        /* Smooth scroll di mobile */
    }

    .nav-scroll::-webkit-scrollbar {
        height: 4px;
        /* Tinggi scrollbar */
    }

    .nav-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        /* Warna scrollbar */
        border-radius: 10px;
    }

    .nav-scroll .nav {
        flex-wrap: nowrap;
        /* Jangan wrap */
    }

    .nav-scroll .nav-link {
        white-space: nowrap;
        /* Teks ga wrap */
    }

    .nav-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .nav-scroll::-webkit-scrollbar {
        display: none;
        /* Sembunyikan scrollbar */
    }

    .nav-scroll .nav {
        flex-wrap: nowrap;
        display: flex;
    }

    .nav-scroll .nav-link {
        white-space: nowrap;
        padding: 0.5rem 1.5rem;
    }

    /* Container untuk navbar yang bisa scroll */
    .nav-container {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
    }

    /* Sembunyikan scrollbar tapi tetap bisa scroll */
    .nav-container::-webkit-scrollbar {
        display: none;
    }

    .nav-underline .nav-link.active {
        color: #000 !important;
        border-bottom-color: #000 !important;
    }

    .nav-container {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Nav jangan wrap, tetap horizontal */
    .nav-container .nav {
        flex-wrap: nowrap !important;
        display: flex;
    }

    /* Nav item jangan shrink */
    .nav-container .nav-item {
        flex: 0 0 auto;
    }

    /* Nav link styling */
    .nav-container .nav-link {
        white-space: nowrap;
        padding: 0.5rem 1.5rem;
        color: #000;
    }

    .nav-container .nav-link.active {
        color: #000;
        border-bottom: 2px solid #000;
    }

    button {
        background-color: black !important;
        outline: none !important;
    }

    .dropdown-menu {
        outline: black solid 1px !important;
    }

    .dropdown-item:active {
        background-color: #d3d3d3 !important;
        /* abu-abu terang */
        color: #000 !important;
    }

    .container-main {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px 20px 100px 20px;
    }

    h3 {
        font-size: 24px;
        font-weight: 600;
        line-height: normal;
    }

    h4 {
        font-size: 16px;
        font-weight: bolder;
        line-height: normal;
    }

    p {
        font-size: 14px;
        font-weight: 400;
        line-height: normal;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }
</style>

<body>

    <div class="container-main">
        <h3 class="mt-4 mb-4">Pesanan</h3>

        <!-- <nav class="mb-4 navbar">
            <ul class="nav nav-underline nav-fill w-100">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="pesananDapurDiterima.php">Diterima</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pesananDapurDiproses.php">Diproses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pesananDapurSelesai.php">Selesai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pesananDapurDiantar.php">Diantar</a>
                </li>
            </ul>
        </nav> -->

        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

        <nav class="mb-4 navbar">
            <ul class="nav nav-underline nav-fill w-100">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'pesananDapurDiterima.php' ? 'active' : '' ?>" href="pesananDapurDiterima.php">Diterima</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'pesananDapurDiproses.php' ? 'active' : '' ?>" href="pesananDapurDiproses.php">Diproses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'pesananDapurSelesai.php' ? 'active' : '' ?>" href="pesananDapurSelesai.php">Selesai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'pesananDapurDiantar.php' ? 'active' : '' ?>" href="pesananDapurDiantar.php">Diantar</a>
                </li>
            </ul>
        </nav>

        <?php if (empty($pesanan_terbaru)): ?>
            <p class="text-muted" style="font-size: 16px; text-align: center; margin-top: 160px;">Belum ada pesanan</p>
        <?php else: ?>
            <?php foreach ($pesanan_per_tanggal as $tanggal => $pesanan_list): ?>
                <h4>Tanggal <?= $tanggal ?></h4>
                <?php foreach ($pesanan_list as $p): ?>
                    <div class="card mb-3" style="max-width: 540px; outline: 1px solid black; position: relative;">
                        <div class="row g-0">
                            <!-- Gambar -->
                            <div class="col-auto">
                                <img src="<?= $p['gambar_menu'] ?>" class="card-img-top" alt="..." style="width: 100px; height: 100px; padding: 10px;">
                            </div>

                            <!-- Nama Pemesan dan detail lainnya -->
                            <div class="col mb-5">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $p['nama_menu'] ?></h5>
                                    <p class="mb-1">Porsi: <?= $p['jumlah'] ?></p>
                                    <p class="mb-1"><?= date('H:i', strtotime($p['tanggal_pesan'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown untuk mengubah status pesanan -->
                        <div class="dropdown mb-1" style="position: absolute; bottom: 8px; right: 18px;">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Ubah Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="ubahStatusPesanan.php?id_pesanan=<?= $p['id_pesanan'] ?>&status=Diterima">Diterima</a></li>
                                <li><a class="dropdown-item" href="ubahStatusPesanan.php?id_pesanan=<?= $p['id_pesanan'] ?>&status=Diproses">Diproses</a></li>
                                <li><a class="dropdown-item disabled" aria-disabled="true" href="#">Selesai</a></li>
                                <li><a class="dropdown-item disabled" aria-disabled="true" href="#">Diantar</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="bottom-nav">
        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="notifikasiDapur.php"><svg width="53" height="45" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M39.7344 34.0144C38.5558 32.9637 37.524 31.7592 36.6667 30.4333C35.7298 28.6035 35.1688 26.6046 35.0167 24.5544V18.5167C35.0248 15.2968 33.8568 12.1849 31.7322 9.76551C29.6077 7.34612 26.6727 5.78582 23.4789 5.37777V3.8011C23.4789 3.36836 23.307 2.95333 23.001 2.64734C22.695 2.34134 22.28 2.16943 21.8472 2.16943C21.4145 2.16943 20.9995 2.34134 20.6935 2.64734C20.3875 2.95333 20.2156 3.36836 20.2156 3.8011V5.40221C17.0503 5.83968 14.1509 7.40941 12.0542 9.82067C9.95755 12.2319 8.80578 15.3213 8.81222 18.5167V24.5544C8.66012 26.6046 8.09909 28.6035 7.16222 30.4333C6.31996 31.7562 5.30468 32.9606 4.14333 34.0144C4.01296 34.129 3.90847 34.27 3.83682 34.428C3.76517 34.5861 3.728 34.7576 3.72778 34.9311V36.5933C3.72778 36.9175 3.85654 37.2284 4.08576 37.4576C4.31497 37.6868 4.62584 37.8155 4.95 37.8155H38.9278C39.2519 37.8155 39.5628 37.6868 39.792 37.4576C40.0212 37.2284 40.15 36.9175 40.15 36.5933V34.9311C40.1498 34.7576 40.1126 34.5861 40.041 34.428C39.9693 34.27 39.8648 34.129 39.7344 34.0144ZM6.27 35.3711C7.4069 34.2723 8.40811 33.0413 9.25222 31.7044C10.4326 29.4946 11.1206 27.0554 11.2689 24.5544V18.5167C11.2204 17.0843 11.4607 15.6567 11.9754 14.3191C12.4901 12.9815 13.2687 11.7612 14.2649 10.7307C15.261 9.7003 16.4543 8.88086 17.7738 8.32121C19.0932 7.76156 20.5118 7.47315 21.945 7.47315C23.3782 7.47315 24.7968 7.76156 26.1162 8.32121C27.4357 8.88086 28.629 9.7003 29.6251 10.7307C30.6213 11.7612 31.3999 12.9815 31.9146 14.3191C32.4293 15.6567 32.6696 17.0843 32.6211 18.5167V24.5544C32.7694 27.0554 33.4574 29.4946 34.6378 31.7044C35.4819 33.0413 36.4831 34.2723 37.62 35.3711H6.27Z" fill="black" />
                        <path d="M22 41.898C22.7699 41.8803 23.5088 41.5908 24.0858 41.0808C24.6629 40.5709 25.041 39.8732 25.1533 39.1113H18.7244C18.8399 39.8939 19.2357 40.608 19.8383 41.1206C20.4408 41.6332 21.209 41.9094 22 41.898Z" fill="black" />
                    </svg>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><svg width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M37.5417 8.8335H15.4583C13.0191 8.8335 11.0417 10.8109 11.0417 13.2502V41.9585C11.0417 44.3978 13.0191 46.3752 15.4583 46.3752H37.5417C39.9809 46.3752 41.9583 44.3978 41.9583 41.9585V13.2502C41.9583 10.8109 39.9809 8.8335 37.5417 8.8335Z" stroke="black" stroke-width="2" />
                        <path d="M19.875 19.875H33.125M19.875 28.7083H33.125M19.875 37.5417H28.7083" stroke="black" stroke-width="2" stroke-linecap="round" />
                    </svg>

                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pengaturanDapur.php"><svg width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M25.3958 30.9165C34.5383 30.9165 41.9583 34.3836 41.9583 38.6457V44.1665H8.83333V38.6457C8.83333 34.3836 16.2533 30.9165 25.3958 30.9165ZM39.75 38.6457C39.75 35.5982 33.3237 33.1248 25.3958 33.1248C17.4679 33.1248 11.0417 35.5982 11.0417 38.6457V41.9582H39.75V38.6457ZM25.3958 11.0415C27.4457 11.0415 29.4117 11.8558 30.8612 13.3053C32.3107 14.7548 33.125 16.7208 33.125 18.7707C33.125 20.8206 32.3107 22.7865 30.8612 24.236C29.4117 25.6855 27.4457 26.4998 25.3958 26.4998C23.3459 26.4998 21.38 25.6855 19.9305 24.236C18.481 22.7865 17.6667 20.8206 17.6667 18.7707C17.6667 16.7208 18.481 14.7548 19.9305 13.3053C21.38 11.8558 23.3459 11.0415 25.3958 11.0415ZM25.3958 13.2498C23.9316 13.2498 22.5274 13.8315 21.492 14.8669C20.4567 15.9022 19.875 17.3065 19.875 18.7707C19.875 20.2349 20.4567 21.6391 21.492 22.6745C22.5274 23.7098 23.9316 24.2915 25.3958 24.2915C26.86 24.2915 28.2643 23.7098 29.2996 22.6745C30.335 21.6391 30.9167 20.2349 30.9167 18.7707C30.9167 17.3065 30.335 15.9022 29.2996 14.8669C28.2643 13.8315 26.86 13.2498 25.3958 13.2498Z" fill="black" />
                    </svg>
                </a>
            </li>
        </ul>
    </div>
</body>

</html>