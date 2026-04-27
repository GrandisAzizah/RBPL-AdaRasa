<?php
session_start();
// user belum login
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
ORDER BY p.tanggal_pesan DESC");

$bahan_perlu_beli = query("SELECT 
    bb.nama_bahan,
    bb.satuan,
    SUM(dpb.jumlah_dipakai) as total_butuh,
    COALESCE(sb.stok_tersedia, 0) as stok_ada,
    (SUM(dpb.jumlah_dipakai) - COALESCE(sb.stok_tersedia, 0)) as perlu_beli
FROM detail_pesanan_bahan dpb
JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
LEFT JOIN stok_bahan sb ON sb.nama_bahan_stok = bb.nama_bahan
GROUP BY bb.nama_bahan, bb.satuan, sb.stok_tersedia
HAVING total_butuh > COALESCE(sb.stok_tersedia, 0)");

// alert
$tanggal_hari_ini = date('Y-m-d');

$query = "SELECT COUNT(*) as total FROM pesanan WHERE DATE(tanggal_pesan) = '$tanggal_hari_ini'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_pesanan = $row['total'];

$pesan = "Terdapat $total_pesanan pesanan hari ini.";
$tipe = 'info';
?>

<!DOCTYPE html>
<html lang="en">

<style>
    .alert {
        width: 330px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    a {
        text-decoration: none !important;
        color: inherit;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./admin-page.css">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <p>Halo, ADMIN!</p>

    <div class="container-main">
        <?php if ($pesan): ?>
            <a href="pesanan.php" style="color: inherit;">
                <div class="mt-3 mb-3 text-center alert alert- <?= $tipe ?> alert-dismissible fade show" role="alert">
                    <div class="col">
                        <strong>Informasi</strong><br>
                        <?= $pesan ?>
                    </div>
                </div>
            </a>
        <?php endif; ?>

        <div class="container-button">
            <div class="menu-item">
                <button type="button" class="menu-button" data-bs-toggle="modal" data-bs-target="">
                    <a href="menu.php">
                        <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.125 7.29167C13.125 6.51812 13.4323 5.77625 13.9793 5.22927C14.5263 4.68229 15.2681 4.375 16.0417 4.375H18.9583C19.7319 4.375 20.4737 4.68229 21.0207 5.22927C21.5677 5.77625 21.875 6.51812 21.875 7.29167V13.125H27.7083C28.4819 13.125 29.2237 13.4323 29.7707 13.9793C30.3177 14.5263 30.625 15.2681 30.625 16.0417V18.9583C30.625 19.7319 30.3177 20.4737 29.7707 21.0207C29.2237 21.5677 28.4819 21.875 27.7083 21.875H21.875V27.7083C21.875 28.4819 21.5677 29.2237 21.0207 29.7707C20.4737 30.3177 19.7319 30.625 18.9583 30.625H16.0417C15.2681 30.625 14.5263 30.3177 13.9793 29.7707C13.4323 29.2237 13.125 28.4819 13.125 27.7083V21.875H7.29167C6.51812 21.875 5.77625 21.5677 5.22927 21.0207C4.68229 20.4737 4.375 19.7319 4.375 18.9583V16.0417C4.375 15.2681 4.68229 14.5263 5.22927 13.9793C5.77625 13.4323 6.51812 13.125 7.29167 13.125H13.125V7.29167ZM18.9583 7.29167H16.0417V14.5833C16.0417 14.9701 15.888 15.341 15.6145 15.6145C15.341 15.888 14.9701 16.0417 14.5833 16.0417H7.29167V18.9583H14.5833C14.9701 18.9583 15.341 19.112 15.6145 19.3855C15.888 19.659 16.0417 20.0299 16.0417 20.4167V27.7083H18.9583V20.4167C18.9583 20.0299 19.112 19.659 19.3855 19.3855C19.659 19.112 20.0299 18.9583 20.4167 18.9583H27.7083V16.0417H20.4167C20.0299 16.0417 19.659 15.888 19.3855 15.6145C19.112 15.341 18.9583 14.9701 18.9583 14.5833V7.29167Z" fill="black" />
                        </svg>
                    </a>
                </button>
                <span class="menu-label">Menu</span>
            </div>

            <div class="menu-item">
                <button type="button" class="menu-button" data-bs-toggle="modal" data-bs-target="">
                    <a href="pesanan.php">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.0045 31.5C10.9305 31.5 7.395 31.5 5.1975 29.3025C3 27.108 3 23.5725 3 16.5V11.916C3 9.192 3 7.83 3.57 6.807C3.9767 6.07811 4.57811 5.4767 5.307 5.07C6.33 4.5 7.692 4.5 10.419 4.5C12.165 4.5 13.038 4.5 13.8 4.7865C15.5445 5.4405 16.2645 7.026 17.052 8.5995L18.0045 10.5M12.003 10.5H25.131C28.2915 10.5 29.871 10.5 31.008 11.259C31.4994 11.587 31.9214 12.0085 32.25 12.4995C32.841 13.3845 32.97 14.5395 33 16.5M27 29.6595C29.6505 29.6595 31.8 27.462 31.8 24.75C31.8 22.0395 29.6505 19.8405 27 19.8405M27 29.6595C24.3495 29.6595 22.2 27.462 22.2 24.75C22.2 22.0395 24.3495 19.8405 27 19.8405M27 29.6595V31.5M27 19.8405V18M22.6365 22.0725L21.0015 21.0675M33 28.434L31.365 27.429M31.3635 22.074L32.9985 21.069M21 28.434L22.635 27.429" stroke="black" stroke-width="2.25" stroke-linecap="round" />
                        </svg>
                    </a>
                </button>
                <span class="menu-label" sty>Pesanan</span>
            </div>

            <div class="menu-item">
                <button type="button" class="menu-button" data-bs-toggle="modal" data-bs-target="">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="34.0596" height="34.0596" fill="url(#pattern0_151_6152)" />
                        <defs>
                            <pattern id="pattern0_151_6152" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use xlink:href="#image0_151_6152" transform="scale(0.01)" />
                            </pattern>
                            <image id="image0_151_6152" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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" />
                        </defs>
                    </svg>
                </button>
                <span class="menu-label">Laporan</span>
            </div>

            <div class="menu-item">
                <button type="button" class="menu-button" data-bs-toggle="modal" data-bs-target="">
                    <a href="./pengaturanAdmin.php">
                        <svg width="35" height="35" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.8438 8.15625C20.8438 9.83872 20.1754 11.4523 18.9857 12.642C17.796 13.8316 16.1825 14.5 14.5 14.5C12.8175 14.5 11.204 13.8316 10.0143 12.642C8.82461 11.4523 8.15625 9.83872 8.15625 8.15625C8.15625 6.47378 8.82461 4.86023 10.0143 3.67054C11.204 2.48086 12.8175 1.8125 14.5 1.8125C16.1825 1.8125 17.796 2.48086 18.9857 3.67054C20.1754 4.86023 20.8438 6.47378 20.8438 8.15625ZM19.0312 8.15625C19.0312 6.95449 18.5539 5.80195 17.7041 4.95217C16.8543 4.1024 15.7018 3.625 14.5 3.625C13.2982 3.625 12.1457 4.1024 11.2959 4.95217C10.4461 5.80195 9.96875 6.95449 9.96875 8.15625C9.96875 9.35801 10.4461 10.5106 11.2959 11.3603C12.1457 12.2101 13.2982 12.6875 14.5 12.6875C15.7018 12.6875 16.8543 12.2101 17.7041 11.3603C18.5539 10.5106 19.0312 9.35801 19.0312 8.15625ZM6.79688 16.3125C5.95564 16.3125 5.14886 16.6467 4.55402 17.2415C3.95918 17.8364 3.625 18.6431 3.625 19.4844V19.9375C3.625 22.1062 5.00522 23.9404 6.96453 25.1874C8.93472 26.4417 11.6018 27.1875 14.5 27.1875C17.3982 27.1875 20.0644 26.4417 22.0355 25.1874C23.9948 23.9404 25.375 22.1062 25.375 19.9375V19.4844C25.375 18.6431 25.0408 17.8364 24.446 17.2415C23.8511 16.6467 23.0444 16.3125 22.2031 16.3125H6.79688ZM5.4375 19.4844C5.4375 19.1238 5.58072 18.7781 5.83565 18.5232C6.09058 18.2682 6.43635 18.125 6.79688 18.125H22.2031C22.5637 18.125 22.9094 18.2682 23.1643 18.5232C23.4193 18.7781 23.5625 19.1238 23.5625 19.4844V19.9375C23.5625 21.2724 22.7106 22.61 21.0622 23.6586C19.4246 24.7008 17.1073 25.375 14.5 25.375C11.8927 25.375 9.57544 24.7008 7.93784 23.6586C6.28847 22.6109 5.4375 21.2715 5.4375 19.9375V19.4844Z" fill="black" />
                        </svg>
                    </a>
                </button>
                <span class="menu-label">Pengaturan</span>
            </div>
        </div>

        <a href="pesanan.php" style="text-decoration: none;">
            <h5>Pesanan Masuk</h5>
        </a>

        <?php if (empty($pesanan_terbaru)): ?>
            <p class="text-muted" style="font-size: 16px;">Belum ada pesanan</p>
        <?php else: ?>
            <?php foreach ($pesanan_terbaru as $p): ?>
                <a href="showDetailPesananAdmin.php?id_pesanan=<?= $p['id_pesanan'] ?>" style="text-decoration: none; color: inherit;">
                    <div class="container-order">
                        <div class="order-data card mb-3">
                            <div class="row g-0">
                                <!-- Gambar -->
                                <div class="col-auto">
                                    <img src="<?= $p['gambar_menu'] ?>" class="order-img" alt="...">
                                </div>

                                <!-- Detail -->
                                <div class="col">
                                    <div class="card-body">
                                        <p class="card-title">
                                            <?= $p['nama_pelanggan'] ?> - <?= $p['nama_menu'] ?>
                                        </p>
                                        <p class="card-detail"><?= $p['takaran'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
            <button type="button" class="load-more-button" data-bs-toggle="modal" data-bs-target="">
                <span> More</span>
                <svg width="10" height="10" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0L5 5L10 0H0Z" fill="black" />
                </svg>
            </button>
        <?php endif; ?>

        <a href="listBelanjaBahan.php">
            <h5>Bahan Baku</h5>
        </a>
        <?php if (empty($bahan_perlu_beli)): ?>
            <p class="text-muted" style="font-size: 14px;">
                Belum ada bahan baku yang perlu dibeli
            </p>
        <?php else: ?>
            <div class="container-bahan-baku">
                <div class="card">
                    <?php foreach ($bahan_perlu_beli as $i => $b): ?>
                        <div class="d-flex justify-content-between align-items-center px-3 py-1">
                            <p class="mb-0" style="font-size: 13px;">
                                <?= $b['nama_bahan'] ?> - Perlu beli: <?= $b['perlu_beli'] ?> <?= $b['satuan'] ?>
                            </p>
                            <input type="checkbox" class="form-check-input flex-shrink-0 ms-2">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>