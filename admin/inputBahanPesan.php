<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$jumlah = $_SESSION['pesanan']['jumlah'] ?? 1;
$id_menu = $_SESSION['pesanan']['fk_menu'] ?? 0;
$fk_varian = $_SESSION['pesanan']['fk_pesanan_varian'] ?? 0;

$bahan = query("SELECT * FROM bahan_baku 
    WHERE fk_menu_bahan = $id_menu 
    AND (fk_varian_bahan = $fk_varian OR fk_varian_bahan IS NULL)");

$pesan = '';
$tipe = '';

if (isset($_POST["submit"])) {
    $packing = $_POST['packing'];
    $bahan_dipilih = $_POST['bahan'] ?? [];

    $dataPesanan = [
        'fk_customer' => $_SESSION['pesanan']['fk_customer'],
        'fk_menu' => $_SESSION['pesanan']['fk_menu'],
        'fk_pesanan_varian' => $_SESSION['pesanan']['fk_pesanan_varian'],
        'jumlah' => $_SESSION['pesanan']['jumlah'],
        'catatan' => $_SESSION['pesanan']['catatan'],
        'tanggal_antar' => $_SESSION['pesanan']['tanggal_antar'],
        'metode' => $_SESSION['pesanan']['metode']
    ];

    // Simpan ke tabel pesanan
    $id_pesanan = tambahPesanan($dataPesanan);

    if ($id_pesanan > 0) {
        // Simpan detail bahan dan packing
        $bahan_tambahan_dipilih = $_POST['bahan_tambahan'] ?? [];
        $bahan_tambahan_session = $_SESSION['pesanan']['bahan_tambahan'] ?? [];

        simpanDetailPesanan(
            $id_pesanan,
            $bahan_dipilih,
            $packing,
            $_SESSION['pesanan']['jumlah'],
            $bahan_tambahan_dipilih,
            $bahan_tambahan_session
        );

        $pesan = 'Pesanan berhasil disimpan!';
        $tipe = 'success';

        unset($_SESSION['pesanan']);
        echo "<meta http-equiv='refresh' content='2;url=pesanan.php'>";
    } else {
        $pesan = 'Pesanan gagal disimpan!';
        $tipe = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan & Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pesananAdmin.css">
</head>

<style>
    body {
        font-family: 'Aleo', serif;
        background-color: #ffffff;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
    }

    .container {
        width: 290px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .alert {
        width: 290px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-close {
        position: absolute;
        top: 10px;
        right: 10px;
        box-shadow: none !important;
    }

    button {
        width: 100%;
        padding: 4px !important;
    }

    input {
        width: 100%;
        padding: 4px;
        margin-top: 5px;
        border: 1px solid #B3B3B3;
        border-radius: 4px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 290px;
    }

    .menu-item .icon {
        flex-shrink: 0;
        width: 24px;
    }

    h5 {
        font-weight: 600;
        margin-top: 20px;
        text-align: center;
    }

    label {
        font-weight: 600;
        margin-top: 5px;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        margin-top: 20px;
    }

    input:focus {
        outline: 1px solid #B3B3B3 !important;
        border-color: #B3B3B3 !important;
        box-shadow: none !important;
    }
</style>

<body>
    <div class="container-main">
        <div class="header-nav-input mt-3">
            <a href="inputPesanan.php" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title-input" style="margin: 0;">BAHAN YANG DIPERLUKAN</h5>
        </div>

        <?php if ($pesan): ?>
            <div class="mt-3 mb-3 alert alert- <?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="container">
            <form action="" method="POST">
                <!-- INPUT JUMLAH -->
                <label for="jumlah">Jumlah:</label>
                <input type="number" name="jumlah" id="jumlah" value="<?= $jumlah ?>" readonly>

                <label>Bahan yang Diperlukan</label>
                <div class="bahan-list">
                    <?php foreach ($bahan as $i => $b):
                        $jumlah_bahan = $b['jumlah_default'] * $jumlah;
                    ?>
                        <div class="bahan-item">
                            <input type="checkbox" name="bahan[]" value="<?= $b['id_bahan'] ?>" id="bahan<?= $i ?>" checked>
                            <label for="bahan<?= $i ?>" class="bahan-label">
                                <span class="bahan-nama"><?= $b['nama_bahan'] ?></span>
                                <span class="bahan-jumlah" id="jumlah-bahan-<?= $i ?>">
                                    <?= $jumlah_bahan ?> <?= $b['satuan'] ?>
                                </span>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <!-- Setelah loop bahan default -->
                    <?php if (!empty($_SESSION['pesanan']['bahan_tambahan'])): ?>
                        <?php foreach ($_SESSION['pesanan']['bahan_tambahan'] as $i => $bt): ?>
                            <div class="bahan-item">
                                <input type="checkbox" name="bahan_tambahan[]" value="<?= $i ?>" id="tambahan<?= $i ?>" checked>
                                <label for="tambahan<?= $i ?>" class="bahan-label">
                                    <span class="bahan-nama"><?= $bt['nama_bahan'] ?></span>
                                    <span class="bahan-jumlah"><?= $bt['jumlah'] * $jumlah ?> <?= $bt['satuan'] ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="tambah-bahan-baru">
                    <p><a href="tambahBahanBaru.php">Tambah</a></p>
                </div>

                <div class="packing-takaran-group">
                    <div class="packing-takaran-item">
                        <label for="packing">Packing</label>
                        <select name="packing" id="packing">
                            <option value="Box">Box</option>
                            <option value="Mika">Mika</option>
                            <option value="Kertas Minyak">Kertas Minyak</option>
                            <option value="Plastik">Plastik</option>
                            <option value="Toples Kotak">Toples Kotak</option>
                            <option value="Toples Lingkaran">Toples Lingkaran</option>
                            <option value="Toples Hati">Toples Hati</option>
                            <option value="Toples Tabung">Toples Tabung</option>
                        </select>
                    </div>
                </div>
                <button type="submit" value="Kirim" name="submit" class="btn btn-outline-dark input-next">Save</button>
            </form>
        </div>

    </div>
</body>

</html>