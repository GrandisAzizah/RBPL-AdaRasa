<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

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
    <link rel="stylesheet" href="pesanan.css">
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
            <a href="pesanan.php" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title-input" style="margin: 0;">INPUT PEMESANAN</h5>
        </div>

        <div class="container">
            <form action="" method="POST">
                <!-- INPUT NAMA MENU -->
                <label for="nama_pelanggan">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" maxlength="30" required>


                <!-- INPUT PESANAN -->
                <label for="nama_pesanan">Pesanan</label><br>
                <select name="nama_pesanan" id="nama_pesanan">
                    <option value="kg">kg</option>
                </select><br>

                <!-- INPUT JUMLAH -->
                <label for="jumlah">Jumlah:<br></label>
                <input type="number" name="jumlah" id="jumlah" min="0" max="100" required>

                <label>Tanggal Pemesanan</label>
                <div class="date-group">
                    <input type="number" name="tanggal_pesan_day" placeholder="DD" min="1" max="31" required>
                    <input type="number" name="tanggal_pesan_month" placeholder="MM" min="1" max="12" required>
                    <input type="number" name="tanggal_pesan_year" placeholder="YYYY" min="2000" max="2100" required>
                </div>

                <label for="metode_pengantaran">Metode Pengantaran</label>
                <select name="metode_pengantaran" id="metode_pengantaran">
                    <option value="Kurir Catering">Kurir Catering</option>
                    <option value="Ojek Online">Ojek Online</option>
                </select><br>

                <label>Tanggal Pengiriman</label>
                <div class="date-group">
                    <input type="number" name="tanggal_antar_day" placeholder="DD" min="1" max="31" required>
                    <input type="number" name="tanggal_antar_month" placeholder="MM" min="1" max="12" required>
                    <input type="number" name="tanggal_antar_year" placeholder="YYYY" min="2000" max="2100" required>
                </div>
                <label for="catatan_khusus_pemesanan" class="form-label">Catatan Khusus Pemesanan</label>
                <textarea type="text" name="catatan_khusus_pemesanan" id="catatan_khusus_pemesanan" maxlength="255" required></textarea>
            </form>
        </div>

        <!-- SUBMIT BUTTON -->
        <a href="inputBahanPesan.php">
            <button type="submit" value="Kirim" name="submit" class="btn btn-outline-dark input-next">Next</button>
        </a>
    </div>
</body>

</html>