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
} elseif ($periode == '1minggu') {
    $filter = "p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $start_week = date('d', strtotime('-6 days'));
    $end_week_full = tanggal_indonesia(date('Y-m-d'));

    // Ambil hanya tanggal dan bulan dari hasil tanggal_indonesia
    $end_week_parts = explode(', ', $end_week_full);
    $end_week = $end_week_parts[1]; // "14 Mei 2026"

    $judul_periode = "Laporan Mingguan $start_week - $end_week";
} elseif ($periode == '1bulan') {
    $filter = "MONTH(p.tanggal_pesan) = MONTH(CURDATE()) AND YEAR(p.tanggal_pesan) = YEAR(CURDATE())";
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
    $judul_periode = "Laporan Bulanan " . $bulan_indonesia[date('F')] . " " . date('Y');
} else {
    $filter = "YEAR(p.tanggal_pesan) = YEAR(CURDATE())";
    $judul_periode = "Laporan Tahunan " . date('Y');
}

$cek_pesanan = query("SELECT COUNT(*) as total FROM pesanan p WHERE $filter")[0]['total'];
$ada_pesanan = $cek_pesanan > 0;

if ($ada_pesanan) {
    if ($periode == '1hari') {
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
            ORDER BY jumlah_porsi_dipesan DESC
        ");
    } else {
        $data = query("SELECT 
                DATE(p.tanggal_pesan) as tanggal_pesan,
                m.nama_menu,
                mv.takaran,
                COUNT(DISTINCT p.id_pesanan) as frekuensi_dipesan,
                SUM(p.jumlah) as jumlah_porsi_dipesan
            FROM pesanan p
            JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
            JOIN menu m ON mv.fk_menu_varian = m.id_menu
            WHERE $filter
            GROUP BY DATE(p.tanggal_pesan), m.id_menu, mv.id_varian
            ORDER BY tanggal_pesan DESC, jumlah_porsi_dipesan DESC
        ");
    }

    $laris = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total DESC
        LIMIT 1")[0] ?? null;

    $jarang = query("SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total, COUNT(DISTINCT p.id_pesanan) as frekuensi
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total ASC
        LIMIT 1")[0] ?? null;

    $total_dibuat = query("SELECT COUNT(*) as total FROM pesanan p WHERE $filter")[0]['total'];
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

        table {
            margin-top: 15px;
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
                Belum ada pesanan pada periode <?= $judul_periode ?>
            </div>
        <?php else: ?>
            <div class="mb-3">
                <table class="table table-bordered mt-4">
                    <tr>
                        <td colspan="2"><strong>Menu paling laris:</strong></td>
                        <td colspan="2">
                            <?= $laris ? $laris['nama_menu'] . ' (' . $laris['takaran'] . ') - ' . $laris['frekuensi'] . ' kali dipesan (' . $laris['total'] . ' porsi)' : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Menu jarang dipesan:</strong></td>
                        <td colspan="2">
                            <?= $jarang ? $jarang['nama_menu'] . ' (' . ($jarang['takaran'] ?? '-') . ') - ' . $jarang['frekuensi'] . ' kali dipesan (' . $jarang['total'] . ' porsi)' : '-' ?>
                        </td>
                    </tr>
                </table>
            </div>

            <button onclick="window.print()" class="btn-download mb-3">Cetak/Simpan PDF</button>

            <h6 class="mt-4">Detail Menu</h6>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <?php if ($periode != '1hari'): ?>
                            <th>Tanggal Pesan</th>
                        <?php endif; ?>
                        <th>Nama Menu</th>
                        <th>Takaran</th>
                        <th>Jumlah Porsi</th>
                        <th>Frekuensi Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php if ($periode != '1hari'): ?>
                                <td><?= tanggal_indonesia($row['tanggal_pesan']) ?></td>
                            <?php endif; ?>
                            <td><?= $row['nama_menu'] ?></td>
                            <td><?= $row['takaran'] ?? '-' ?></td>
                            <td><?= $row['jumlah_porsi_dipesan'] ?></td>
                            <td><?= $row['frekuensi_dipesan'] ?> kali</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <table class="table table-bordered mt-3">
                <tr>
                    <td><strong>Jumlah pesanan dibuat:</strong></td>
                    <td><?= $total_dibuat ?> kali</td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>