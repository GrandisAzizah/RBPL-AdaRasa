<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$periode = $_GET['periode'] ?? '1hari';
$format = $_GET['format'] ?? 'html';

// Tentukan filter SQL dan judul berdasarkan periode
if ($periode == '1hari') {
    $filter = "DATE(p.tanggal_pesan) = CURDATE()";
    $judul_periode = "Harian - " . date('d F Y');
    $nama_file = "Detail_Laporan_Penjualan_Harian_" . date('Y-m-d');
} elseif ($periode == '1minggu') {
    $filter = "p.tanggal_pesan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $start_week = date('d F Y', strtotime('-6 days'));
    $end_week = date('d F Y');
    $judul_periode = "Mingguan ($start_week - $end_week)";
    $nama_file = "Detail_Laporan_Penjualan_Mingguan_" . date('Y-m-d');
} elseif ($periode == '1bulan') {
    $filter = "MONTH(p.tanggal_pesan) = MONTH(CURDATE()) AND YEAR(p.tanggal_pesan) = YEAR(CURDATE())";
    $judul_periode = "Bulanan - " . date('F Y');
    $nama_file = "Detail_Laporan_Penjualan_Bulanan_" . date('Y-m');
} else {
    $filter = "YEAR(p.tanggal_pesan) = YEAR(CURDATE())";
    $judul_periode = "Tahunan - " . date('Y');
    $nama_file = "Detail_Laporan_Penjualan_Tahunan_" . date('Y');
}

// Cek apakah ada pesanan dalam periode tersebut
$cek_pesanan = query("SELECT COUNT(*) as total FROM pesanan p WHERE $filter")[0]['total'];
$ada_pesanan = $cek_pesanan > 0;

if ($ada_pesanan) {
    // Ambil data detail per menu dan varian
    $data = query("
        SELECT 
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

    // Hitung menu paling laris dan jarang dipesan
    $laris = query("
        SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total DESC
        LIMIT 1
    ")[0] ?? null;

    $jarang = query("
        SELECT m.nama_menu, mv.takaran, SUM(p.jumlah) as total
        FROM pesanan p
        JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m ON mv.fk_menu_varian = m.id_menu
        WHERE $filter
        GROUP BY m.id_menu, mv.id_varian
        ORDER BY total ASC
        LIMIT 1
    ")[0] ?? null;

    // Total pesanan dibuat dan diterima
    $total_dibuat = query("SELECT COUNT(*) as total FROM pesanan p WHERE $filter")[0]['total'];
    $total_diterima = query("SELECT COUNT(*) as total FROM pesanan p WHERE status_pemesanan = 'Diterima' AND $filter")[0]['total'];
}

// Jika request download PDF
if ($format == 'pdf') {
    require_once('../tcpdf/tcpdf.php');

    $html = '
    <h2 style="text-align:center">Detail Laporan Penjualan</h2>
    <h4 style="text-align:center">Periode: ' . $judul_periode . '</h4>
    <br><br>';

    if (!$ada_pesanan) {
        $html .= '<p style="text-align:center; color:red;">Belum ada pesanan pada periode ' . $judul_periode . '</p>';
    } else {
        $html .= '
        <table border="1" cellpadding="6" style="width:100%; border-collapse:collapse;">
            <tr>
                <td colspan="2"><strong>Menu paling laris:</strong></td>
                <td colspan="2">' . ($laris ? $laris['nama_menu'] . ' (' . $laris['takaran'] . ')' : '-') . '</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Menu jarang dipesan:</strong></td>
                <td colspan="2">' . ($jarang ? $jarang['nama_menu'] . ' (' . ($jarang['takaran'] ?? '-') . ')' : '-') . '</td>
            </tr>
        </table>
        <br>
        <h4>Detail Menu</h4>
        <table border="1" cellpadding="6" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr><th>Nama Menu</th><th>Takaran</th><th>Frekuensi Dipesan</th><th>Jumlah Porsi</th></tr>
            </thead>
            <tbody>';
        foreach ($data as $row) {
            $html .= '<tr>
                <td>' . $row['nama_menu'] . '</td>
                <td>' . ($row['takaran'] ?? '-') . '</td>
                <td>' . $row['frekuensi_dipesan'] . '</td>
                <td>' . $row['jumlah_porsi_dipesan'] . '</td>
            </tr>';
        }
        $html .= '
            </tbody>
        </table>
        <br>
        <table border="1" cellpadding="6" style="width:100%; border-collapse:collapse;">
            <tr><td><strong>Jumlah pesanan dibuat:</strong></td><td>' . $total_dibuat . '</td></tr>
            <tr><td><strong>Jumlah pesanan diterima:</strong></td><td>' . $total_diterima . '</td></tr>
        </table>';
    }
    exit;
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
            <a href="laporan.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">LAPORAN <?= strtoupper($judul_periode) ?></h5>
        </div>

        <?php if (!$ada_pesanan): ?>
            <div class="alert alert-warning text-center mt-4">
                Belum ada pesanan pada periode <?= $judul_periode ?>
            </div>
        <?php else: ?>
            <table class="table table-bordered mt-4">
                <tr>
                    <td colspan="2"><strong>Menu paling laris:</strong></td>
                    <td colspan="2"><?= $laris ? $laris['nama_menu'] . ' (' . $laris['takaran'] . ')' : '-' ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Menu jarang dipesan:</strong></td>
                    <td colspan="2"><?= $jarang ? $jarang['nama_menu'] . ' (' . ($jarang['takaran'] ?? '-') . ')' : '-' ?></td>
                </tr>
            </table>

            <button onclick="window.print()" class="btn-download" style="background-color: #6750A4; color: white; border: none; padding: 8px 20px; border-radius: 20px;">
                Cetak/Simpan PDF
            </button>

            <h6 class="mt-4">Detail Menu</h6>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Nama Menu</th>
                        <th>Takaran</th>
                        <th>Frekuensi Dipesan</th>
                        <th>Jumlah Porsi Dipesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?= $row['nama_menu'] ?></td>
                            <td><?= $row['takaran'] ?? '-' ?></td>
                            <td><?= $row['frekuensi_dipesan'] ?></td>
                            <td><?= $row['jumlah_porsi_dipesan'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <table class="table table-bordered mt-3">
                <tr>
                    <td><strong>Jumlah pesanan dibuat:</strong></td>
                    <td><?= $total_dibuat ?></td>
                </tr>
                <tr>
                    <td><strong>Jumlah pesanan diterima:</strong></td>
                    <td><?= $total_diterima ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>