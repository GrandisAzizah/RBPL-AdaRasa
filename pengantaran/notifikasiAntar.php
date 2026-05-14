<?php

session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}
date_default_timezone_set('Asia/Jakarta');
require '../functions.php';

$pesanan_terbaru = query("SELECT p.*, c.nama_pelanggan, mv.takaran,
    m.nama_menu, m.gambar_menu,
    MIN(dp.packing) AS packing
    FROM pesanan p 
    LEFT JOIN customer c ON p.fk_pesanan_customer = c.id_pelanggan
    LEFT JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
    LEFT JOIN menu m ON mv.fk_menu_varian = m.id_menu
    LEFT JOIN detail_pesanan_bahan dp ON dp.fk_detail_pesanan = p.id_pesanan
    WHERE p.status_pengantaran = 'Diterima'
    AND p.status_pemesanan = 'Diantar'
    AND p.metode_pengantaran = 'Kurir Catering'
    GROUP BY p.id_pesanan
    ORDER BY p.tanggal_pesan ASC
");

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
    <link rel="stylesheet" href="../pesanan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Page Notifikasi Tim Pengantaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<style>
    .card-notif.dibaca {
        background-color: #f0f0f0;
        opacity: 0.7;
    }
</style>

<script>
    // Tandai card yang sudah pernah diklik
    document.querySelectorAll('.card-notif').forEach(card => {
        const link = card.querySelector('a');
        const id = link.href.match(/id_pesanan=(\d+)/)?.[1];

        if (id && localStorage.getItem('notif_dibaca_' + id)) {
            card.classList.add('dibaca');
        }

        link.addEventListener('click', function() {
            if (id) localStorage.setItem('notif_dibaca_' + id, '1');
        });
    });
</script>

<body>
    <div class="container-main">
        <h3 class="mt-4 mb-4 header-text">Notifikasi</h3>
        <?php if (empty($pesanan_terbaru)): ?>
            <p class="text-muted" style="font-size: 16px; text-align: center; margin-top: 160px;">Belum ada pesanan</p>
        <?php else: ?>
            <?php foreach ($pesanan_per_tanggal as $tanggal => $pesanan_list): ?>
                <?php foreach ($pesanan_list as $p): ?>
                    <div class="card-notif mb-3" style="border: 1px solid #e0e0e0; border-radius: 5px; overflow: hidden; background: #fff;">
                        <a href="pengantaranDiterima.php" style="text-decoration: none; color: inherit;">
                            <div class="card-body p-3">
                                <div>
                                    <h6 class="fw-bold mb-1">Ada pesanan siap diantar!</h6>
                                    <p class="mb-1 fw-semibold"><?= $p['nama_menu'] ?> (<?= $p['takaran'] ?>)</p>
                                    <?php
                                    $waktu_pesan = strtotime($p['tanggal_pesan']);
                                    $tampil_waktu = (date('Y-m-d', $waktu_pesan) === date('Y-m-d'))
                                        ? date('H:i', $waktu_pesan)
                                        : date('d/m/Y', $waktu_pesan);
                                    ?>
                                    <p class="mb-1 text-muted small"><?= $p['jumlah'] . ' ' . $p['packing'] ?></p>
                                    <div class="d-flex justify-content-end">
                                        <small class="text-muted"><?= $tampil_waktu ?></small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="bottom-nav">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page"><svg width="53" height="45" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M39.7344 34.0144C38.5558 32.9637 37.524 31.7592 36.6667 30.4333C35.7298 28.6035 35.1688 26.6046 35.0167 24.5544V18.5167C35.0248 15.2968 33.8568 12.1849 31.7322 9.76551C29.6077 7.34612 26.6727 5.78582 23.4789 5.37777V3.8011C23.4789 3.36836 23.307 2.95333 23.001 2.64734C22.695 2.34134 22.28 2.16943 21.8472 2.16943C21.4145 2.16943 20.9995 2.34134 20.6935 2.64734C20.3875 2.95333 20.2156 3.36836 20.2156 3.8011V5.40221C17.0503 5.83968 14.1509 7.40941 12.0542 9.82067C9.95755 12.2319 8.80578 15.3213 8.81222 18.5167V24.5544C8.66012 26.6046 8.09909 28.6035 7.16222 30.4333C6.31996 31.7562 5.30468 32.9606 4.14333 34.0144C4.01296 34.129 3.90847 34.27 3.83682 34.428C3.76517 34.5861 3.728 34.7576 3.72778 34.9311V36.5933C3.72778 36.9175 3.85654 37.2284 4.08576 37.4576C4.31497 37.6868 4.62584 37.8155 4.95 37.8155H38.9278C39.2519 37.8155 39.5628 37.6868 39.792 37.4576C40.0212 37.2284 40.15 36.9175 40.15 36.5933V34.9311C40.1498 34.7576 40.1126 34.5861 40.041 34.428C39.9693 34.27 39.8648 34.129 39.7344 34.0144ZM6.27 35.3711C7.4069 34.2723 8.40811 33.0413 9.25222 31.7044C10.4326 29.4946 11.1206 27.0554 11.2689 24.5544V18.5167C11.2204 17.0843 11.4607 15.6567 11.9754 14.3191C12.4901 12.9815 13.2687 11.7612 14.2649 10.7307C15.261 9.7003 16.4543 8.88086 17.7738 8.32121C19.0932 7.76156 20.5118 7.47315 21.945 7.47315C23.3782 7.47315 24.7968 7.76156 26.1162 8.32121C27.4357 8.88086 28.629 9.7003 29.6251 10.7307C30.6213 11.7612 31.3999 12.9815 31.9146 14.3191C32.4293 15.6567 32.6696 17.0843 32.6211 18.5167V24.5544C32.7694 27.0554 33.4574 29.4946 34.6378 31.7044C35.4819 33.0413 36.4831 34.2723 37.62 35.3711H6.27Z" fill="black" />
                            <path d="M22 41.898C22.7699 41.8803 23.5088 41.5908 24.0858 41.0808C24.6629 40.5709 25.041 39.8732 25.1533 39.1113H18.7244C18.8399 39.8939 19.2357 40.608 19.8383 41.1206C20.4408 41.6332 21.209 41.9094 22 41.898Z" fill="black" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengantaranDiterima.php">
                        <svg width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.625 26.5H26.5V29.8125H6.625V26.5ZM3.3125 18.2188H19.875V21.5312H3.3125V18.2188Z" fill="black" />
                            <path d="M49.5533 27.5037L44.5845 15.9099C44.4568 15.6121 44.2445 15.3582 43.974 15.1798C43.7034 15.0014 43.3865 14.9063 43.0624 14.9062H38.0937V11.5937C38.0937 11.1545 37.9192 10.7332 37.6086 10.4226C37.298 10.112 36.8767 9.9375 36.4374 9.9375H9.93741V13.25H34.7812V34.0459C34.0269 34.4847 33.3668 35.0682 32.8387 35.7628C32.3106 36.4575 31.9249 37.2496 31.7039 38.0937H21.296C20.8929 36.5325 19.9342 35.1718 18.5996 34.2668C17.265 33.3618 15.6462 32.9746 14.0466 33.1778C12.4469 33.381 10.9763 34.1606 9.91038 35.3705C8.84444 36.5804 8.25635 38.1375 8.25635 39.75C8.25635 41.3625 8.84444 42.9196 9.91038 44.1295C10.9763 45.3394 12.4469 46.119 14.0466 46.3222C15.6462 46.5254 17.265 46.1382 18.5996 45.2332C19.9342 44.3282 20.8929 42.9675 21.296 41.4062H31.7039C32.0641 42.8277 32.8881 44.0884 34.0454 44.989C35.2027 45.8896 36.6273 46.3785 38.0937 46.3785C39.5601 46.3785 40.9846 45.8896 42.1419 44.989C43.2992 44.0884 44.1232 42.8277 44.4835 41.4062H48.0312C48.4704 41.4062 48.8917 41.2318 49.2023 40.9211C49.5129 40.6105 49.6874 40.1893 49.6874 39.75V28.1562C49.6874 27.9319 49.6417 27.7099 49.5533 27.5037ZM14.9062 43.0625C14.251 43.0625 13.6106 42.8682 13.0658 42.5042C12.5211 42.1403 12.0965 41.6229 11.8458 41.0176C11.5951 40.4124 11.5295 39.7463 11.6573 39.1038C11.7851 38.4612 12.1006 37.871 12.5639 37.4077C13.0271 36.9444 13.6174 36.629 14.2599 36.5011C14.9025 36.3733 15.5685 36.4389 16.1738 36.6896C16.7791 36.9404 17.2964 37.3649 17.6604 37.9097C18.0244 38.4544 18.2187 39.0948 18.2187 39.75C18.2187 40.6285 17.8697 41.4711 17.2485 42.0923C16.6272 42.7135 15.7847 43.0625 14.9062 43.0625ZM38.0937 18.2187H41.9693L45.5203 26.5H38.0937V18.2187ZM38.0937 43.0625C37.4385 43.0625 36.7981 42.8682 36.2533 42.5042C35.7086 42.1403 35.284 41.6229 35.0333 41.0176C34.7826 40.4124 34.717 39.7463 34.8448 39.1038C34.9726 38.4612 35.2881 37.871 35.7514 37.4077C36.2146 36.9444 36.8049 36.629 37.4474 36.5011C38.09 36.3733 38.756 36.4389 39.3613 36.6896C39.9666 36.9404 40.4839 37.3649 40.8479 37.9097C41.2119 38.4544 41.4062 39.0948 41.4062 39.75C41.4062 40.6285 41.0572 41.4711 40.436 42.0923C39.8147 42.7135 38.9722 43.0625 38.0937 43.0625ZM46.3749 38.0937H44.4835C44.1186 36.6751 43.2933 35.4175 42.137 34.5183C40.9806 33.6191 39.5585 33.1291 38.0937 33.125V29.8125H46.3749V38.0937Z" fill="black" />
                        </svg>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pengaturanAntar.php"><svg width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.3958 30.9165C34.5383 30.9165 41.9583 34.3836 41.9583 38.6457V44.1665H8.83333V38.6457C8.83333 34.3836 16.2533 30.9165 25.3958 30.9165ZM39.75 38.6457C39.75 35.5982 33.3237 33.1248 25.3958 33.1248C17.4679 33.1248 11.0417 35.5982 11.0417 38.6457V41.9582H39.75V38.6457ZM25.3958 11.0415C27.4457 11.0415 29.4117 11.8558 30.8612 13.3053C32.3107 14.7548 33.125 16.7208 33.125 18.7707C33.125 20.8206 32.3107 22.7865 30.8612 24.236C29.4117 25.6855 27.4457 26.4998 25.3958 26.4998C23.3459 26.4998 21.38 25.6855 19.9305 24.236C18.481 22.7865 17.6667 20.8206 17.6667 18.7707C17.6667 16.7208 18.481 14.7548 19.9305 13.3053C21.38 11.8558 23.3459 11.0415 25.3958 11.0415ZM25.3958 13.2498C23.9316 13.2498 22.5274 13.8315 21.492 14.8669C20.4567 15.9022 19.875 17.3065 19.875 18.7707C19.875 20.2349 20.4567 21.6391 21.492 22.6745C22.5274 23.7098 23.9316 24.2915 25.3958 24.2915C26.86 24.2915 28.2643 23.7098 29.2996 22.6745C30.335 21.6391 30.9167 20.2349 30.9167 18.7707C30.9167 17.3065 30.335 15.9022 29.2996 14.8669C28.2643 13.8315 26.86 13.2498 25.3958 13.2498Z" fill="black" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</body>

</html>