<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$periode = $_GET['periode'] ?? '1hari';

if ($periode == '1hari') {
    $filter = "DATE(p.tanggal_pesan) = CURDATE()";
    $judul_periode = "Laporan Harian - " . tanggal_indonesia(date('Y-m-d'));
    $data = query("SELECT 
            m.nama_menu,
            mv.takaran,
            COUNT(DISTINCT p.id_pesanan) as frekuensi_dipesan,
            SUM(p.jumlah) as jumlah_porsi_dipesan
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY jumlah_porsi_dipesan DESC");

    $laris = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total DESC LIMIT 1")[0] ?? null;

    $jarang = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total ASC LIMIT 1")[0] ?? null;

    $total_dibuat = query("SELECT COUNT(*) as total FROM pesanan p WHERE $filter")[0]['total'];
    $ada_pesanan = $total_dibuat > 0;
} elseif ($periode == '1minggu') {
    $judul_periode = "Laporan Mingguan (4 Minggu Terakhir)";
    $weeks = query("SELECT DISTINCT
        week_num,
        tgl_awal,
        tgl_akhir
    FROM (
        SELECT 
            WEEK(tanggal_pesan, 1) as week_num,
            MIN(DATE(tanggal_pesan)) OVER (PARTITION BY WEEK(tanggal_pesan, 1)) as tgl_awal,
            MAX(DATE(tanggal_pesan)) OVER (PARTITION BY WEEK(tanggal_pesan, 1)) as tgl_akhir
        FROM pesanan
        WHERE tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ) as sub
");

    $data_mingguan = [];
    $week_count = count($weeks);
    foreach ($weeks as $index => $week) {
        $data = query("SELECT 
                m.nama_menu,
                mv.takaran,
                COUNT(DISTINCT p.id_pesanan) as frekuensi_dipesan,
                SUM(p.jumlah) as jumlah_porsi_dipesan
            FROM pesanan p
            JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
            JOIN menu m ON mv.fk_menu_varian = m.id_menu
            WHERE WEEK(p.tanggal_pesan, 1) = {$week['week_num']}
            AND p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY m.id_menu, mv.id_varian
            ORDER BY jumlah_porsi_dipesan DESC
        ");

        $data_mingguan[] = [
            'minggu_ke' => "Minggu ke-" . ($week_count - $index),
            'tgl_awal' => $week['tgl_awal'],
            'tgl_akhir' => $week['tgl_akhir'],
            'data' => $data
        ];
    }

    $laris = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total DESC LIMIT 1")[0] ?? null;

    $jarang = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total ASC LIMIT 1")[0] ?? null;

    $total_dibuat = query("SELECT COUNT(*) as total FROM pesanan p WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")[0]['total'];
    $ada_pesanan = $total_dibuat > 0;
} elseif ($periode == '1bulan') {
    $judul_periode = "Laporan Bulanan (12 Bulan Terakhir)";

    $bulan_indonesia = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    $data_bulanan = query("SELECT 
            DATE_FORMAT(p.tanggal_pesan, '%Y-%m') as bulan,
            m.nama_menu,
            mv.takaran,
            COUNT(DISTINCT p.id_pesanan) as frekuensi_dipesan,
            SUM(p.jumlah) as jumlah_porsi_dipesan
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(p.tanggal_pesan, '%Y-%m'), m.id_menu, mv.id_varian
        ORDER BY bulan DESC, jumlah_porsi_dipesan DESC
    ");

    $laris = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total DESC LIMIT 1")[0] ?? null;

    $jarang = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total ASC LIMIT 1")[0] ?? null;

    $total_dibuat = query("SELECT COUNT(*) as total FROM pesanan p WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)")[0]['total'];
    $ada_pesanan = $total_dibuat > 0;
} else {
    $judul_periode = "Laporan Tahunan (5 tahun terakhir)";

    $data_tahunan = query("SELECT 
            YEAR(p.tanggal_pesan) as tahun,
            m.nama_menu,
            mv.takaran,
            COUNT(DISTINCT p.id_pesanan) as frekuensi_dipesan,
            SUM(p.jumlah) as jumlah_porsi_dipesan
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
        GROUP BY YEAR(p.tanggal_pesan), m.id_menu, mv.id_varian
        ORDER BY tahun DESC, jumlah_porsi_dipesan DESC
    ");

    $laris = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total DESC LIMIT 1")[0] ?? null;

    $jarang = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total ASC LIMIT 1")[0] ?? null;

    $total_dibuat = query("SELECT COUNT(*) as total FROM pesanan p WHERE p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)")[0]['total'];
    $ada_pesanan = $total_dibuat > 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Analisis Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Aleo', serif;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .btn-download {
            background-color: #6750A4;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
        }

        .btn-download:hover {
            background-color: #523d8c;
            color: white;
        }

        .periode-group {
            margin-top: 30px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #523d8c;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        table {
            margin-top: 15px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3 mt-3">
            <a href="analisisLaporan.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;"><?= strtoupper($judul_periode) ?></h5>
        </div>

        <?php if (!$ada_pesanan): ?>
            <div class="alert alert-warning text-center mt-4">
                Belum ada pesanan pada periode yang dipilih
            </div>
        <?php else: ?>
            <div class="mb-4">
                <div class="mb-3">
                    <div class="periode-group">
                        <h6 class="mb-2">Rangkuman</h6>
                        <p><strong>Menu paling laris:</strong>
                            <?php if (isset($laris) && $laris): ?>
                                <?= $laris['nama_menu'] . ' (' . $laris['takaran'] . ') - ' . $laris['frekuensi'] . ' kali dipesan (' . $laris['total'] . ' porsi)' ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                        <p><strong>Menu jarang dipesan:</strong>
                            <?php if (isset($jarang) && $jarang): ?>
                                <?= $jarang['nama_menu'] . ' (' . ($jarang['takaran'] ?? '-') . ') - ' . $jarang['frekuensi'] . ' kali dipesan (' . $jarang['total'] . ' porsi)' ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <button onclick="window.print()" class="btn-download mb-3">Cetak/Simpan PDF</button>

            <?php if ($periode == '1hari'): ?>
                <!-- HARIAN -->
                <h6 class="mt-4">Detail Menu</h6>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Menu</th>
                            <th>Takaran</th>
                            <th>Jumlah Porsi</th>
                            <th>Frekuensi Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= $row['nama_menu'] ?></td>
                                <td><?= $row['takaran'] ?? '-' ?></td>
                                <td><?= $row['jumlah_porsi_dipesan'] ?></td>
                                <td><?= $row['frekuensi_dipesan'] ?> kali</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php
            elseif ($periode == '1minggu'):
                foreach ($data_mingguan as $week_data):
                ?>
                    <div class="periode-group">
                        <h6 class="mb-2"><?= $week_data['minggu_ke'] ?> (<?= tanggal_indonesia($week_data['tgl_awal']) ?> - <?= tanggal_indonesia($week_data['tgl_akhir']) ?>)</h6>
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Menu</th>
                                <th>Takaran</th>
                                <th>Jumlah Porsi</th>
                                <th>Frekuensi Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($week_data['data'] as $row): ?>
                                <tr>
                                    <td><?= $row['nama_menu'] ?></td>
                                    <td><?= $row['takaran'] ?? '-' ?></td>
                                    <td><?= $row['jumlah_porsi_dipesan'] ?></td>
                                    <td><?= $row['frekuensi_dipesan'] ?> kali</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php
                endforeach;

            elseif ($periode == '1bulan'): ?>
                <?php
                $current_bulan = '';
                foreach ($data_bulanan as $row):
                    $tahun = substr($row['bulan'], 0, 4);
                    $bulan_num = substr($row['bulan'], 5, 2);
                    $bulan_name = '';
                    foreach ($bulan_indonesia as $eng => $ind) {
                        if (date('F', mktime(0, 0, 0, $bulan_num, 1)) == $eng) {
                            $bulan_name = $ind;
                            break;
                        }
                    }

                    if ($current_bulan != $row['bulan']):
                        if ($current_bulan != ''): ?>
                            </tbody>
                            </table>
                        <?php endif; ?>
                        <div class="periode-group">
                            <h6 class="mb-2 mt-2 fw-semibold"><?= $bulan_name ?> <?= $tahun ?></h6>
                        </div>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Menu</th>
                                    <th>Takaran</th>
                                    <th>Jumlah Porsi</th>
                                    <th>Frekuensi Pesan</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $current_bulan = $row['bulan'];
                        endif; ?>
                            <tr>
                                <td><?= $row['nama_menu'] ?></td>
                                <td><?= $row['takaran'] ?? '-' ?></td>
                                <td><?= $row['jumlah_porsi_dipesan'] ?></td>
                                <td><?= $row['frekuensi_dipesan'] ?> kali</td>
                            </tr>
                        <?php endforeach; ?>
                            </tbody>
                        </table>

                    <?php else: ?>
                        <?php
                        $current_tahun = '';
                        foreach ($data_tahunan as $row):
                            if ($current_tahun != $row['tahun']):
                                if ($current_tahun != ''): ?>
                                    </tbody>
                                    </table>
                                <?php endif; ?>
                                <div class="periode-group">
                                    <h6 class="mb-2 mt-2">Tahun <?= $row['tahun'] ?></h6>
                                </div>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Menu</th>
                                            <th>Takaran</th>
                                            <th>Jumlah Porsi</th>
                                            <th>Frekuensi Pesan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $current_tahun = $row['tahun'];
                                endif; ?>
                                    <tr>
                                        <td><?= $row['nama_menu'] ?></td>
                                        <td><?= $row['takaran'] ?? '-' ?></td>
                                        <td><?= $row['jumlah_porsi_dipesan'] ?></td>
                                        <td><?= $row['frekuensi_dipesan'] ?> kali</td>
                                    </tr>
                                <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                            <div class="periode-group">
                                <p class="mt-2 mb-2"><strong>Total pesanan dibuat:</strong> <?= $total_dibuat ?> pesanan</p>
                            </div>
                        <?php endif; ?>
    </div>
</body>

</html>