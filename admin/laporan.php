<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

// Daftar SEMUA kategori yang ingin ditampilkan (hardcode)
$semua_kategori = [
    'Nasi Box' => 'nasibox-laporan.png',
    'Kue Kering' => 'kuekering-laporan.png',
    'Cake' => 'cake-laporan.png',
    'Kue Brownies' => 'kuebrownies-laporan.png'
];

// Ambil data penjualan per kategori dari database
$penjualan_kategori = query("
    SELECT 
        m.kategori,
        SUM(p.jumlah) as total_terjual
    FROM pesanan p
    JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
    JOIN menu m ON mv.fk_menu_varian = m.id_menu
    GROUP BY m.kategori
");

// Buat array hasil dengan semua kategori (default 0)
$kategori_total = [];
foreach (array_keys($semua_kategori) as $kat) {
    $kategori_total[$kat] = 0;
}
foreach ($penjualan_kategori as $row) {
    $kategori_total[$row['kategori']] = $row['total_terjual'];
}

$labels = array_keys($kategori_total);
$data_penjualan = array_values($kategori_total);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Laporan</title>
    <link rel="stylesheet" href="../pesanan.css">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<style>
    body {
        font-family: 'Aleo', serif;
        background-color: #ffffff;
        margin: 0;
        padding: 20px;
    }

    h5 {
        font-weight: 590;
        margin-top: 20px;
        font-size: 18px;
    }

    p {
        font-weight: 400;
        margin-left: 10px;
        margin-top: 10px;
        font-size: 16px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 5px;
    }

    .menu-item .icon {
        flex-shrink: 0;
        width: 24px;
    }

    .menu-item .label {
        flex: 1;
        margin-left: 8px;
    }

    .menu-item .arrow-icon {
        flex-shrink: 0;
    }

    a {
        text-decoration: none;
        color: black;
    }

    img {
        width: 70px;
        height: 70px;
    }

    .card .nama-kategori {
        margin-top: 10px;
    }

    .report-card .card {
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 10px;
        gap: 15px;
        height: 150px;
        outline: 1px solid #000000 !important;
    }

    .report-card .card img {
        width: 80px;
        height: 70px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .report-card .card-info {
        text-align: left;
        flex: 1;
    }

    .report-card .card-info .nama-kategori {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .report-card .card-info .jumlah {
        font-size: 20px;
        font-weight: bold;
        color: #000000;
    }

    button.btn-outline-dark {
        justify-content: center !important;
        display: flex !important;
        border-radius: 20px !important;
    }
</style>

<body>
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3 mt-3">
            <a href="berandaAdmin.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Detail Laporan</h5>
        </div>

        <div class="chart-container mb-4">
            <canvas id="kategoriChart" width="400" height="400"></canvas>
        </div>

        <div class="report-card mt-3">
            <div class="row">
                <?php foreach ($kategori_total as $kategori => $total): ?>
                    <div class="col-sm-6 mb-3">
                        <div class="card">
                            <img src="laporan-img/<?= $semua_kategori[$kategori] ?>" alt="<?= $kategori ?>">
                            <div class="card-info">
                                <div class="nama-kategori"><?= $kategori ?></div>
                                <div class="jumlah"><?= $total ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-3 mt-3 mb-3">
        <button onclick="window.print()" class="btn btn-sm" style="background-color: #6750A4; color: white; border-radius: 20px; padding: 6px 20px;">
            Cetak/Simpan PDF
        </button>
        <a href="analisisLaporan.php">
            <button class="btn btn-outline-dark btn-sm" style="border-radius: 20px; padding: 6px 20px;">Detail Analisis</button>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('kategoriChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: <?= json_encode($data_penjualan) ?>,
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: ''
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>