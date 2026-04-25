<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

if (!isset($_GET["id_stok"]) || !is_numeric($_GET["id_stok"])) {
    die("id_stok tidak ditemukan");
}

$id_stok = (int)$_GET['id_stok'];
$stok = query("SELECT * FROM stok_bahan WHERE id_stok = $id_stok")[0];

$pesan = '';
$tipe = '';

if (isset($_POST["submit"])) {
    $_POST['id_stok'] = $id_stok;
    $hasil = editStokBahan($_POST);

    if ($hasil > 0) {
        $pesan = 'Data berhasil diedit!';
        $tipe = 'success';
    } else {
        $pesan = 'Data gagal diedit!';
        $tipe = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Input Bahan Baku</title>
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

    select {
        margin-bottom: 8px;
        width: 100%;
        padding: 4px 30px 4px 8px;
        /* kanan lebih lebar buat arrow */
        border: 1px solid #B3B3B3;
        border-radius: 4px;
        background-color: #ffffff;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4 6L8 10L12 6' stroke='%231E1E1E' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
        background-repeat: no-repeat;
        background-position: right 10px center;
        cursor: pointer;
        font-size: 13px;

    }
</style>

<body>
    <div>
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3">
            <a href="listBahanTersedia.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Edit Bahan Baku</h5>
        </div>

        <!-- ALERT -->
        <?php if ($pesan): ?>
            <div class="mt-3 mb-3 alert alert- <?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="container">
            <form action="" method="POST">
                <label for="nama_bahan_stok">Nama Bahan Baku <br></label>
                <input type="text" name="nama_bahan_stok" id="nama_bahan_stok" value="<?= $stok['nama_bahan_stok'] ?>" maxlength="30" required><br><br>

                <!-- INPUT JUMLAH -->
                <label for="stok_tersedia">Stok<br></label>
                <input type="text" name="stok_tersedia" id="stok_tersedia" value="<?= $stok['stok_tersedia'] ?>" min="0" max="999999" step="0.01" required><br><br>

                <!-- INPUT SATUAN -->
                <label for="satuan">Satuan</label><br>
                <select name="satuan" id="satuan">
                    <option value="buah" <?= $stok['satuan'] == 'buah' ? 'selected' : '' ?>>buah</option>
                    <option value="bungkus" <?= $stok['satuan'] == 'bungkus' ? 'selected' : '' ?>>bungkus</option>
                    <option value="gelas" <?= $stok['satuan'] == 'gelas' ? 'selected' : '' ?>>gelas</option>
                    <option value="gram" <?= $stok['satuan'] == 'gram' ? 'selected' : '' ?>>gram</option>
                    <option value="kg" <?= $stok['satuan'] == 'kg' ? 'selected' : '' ?>>kg</option>
                    <option value="lembar" <?= $stok['satuan'] == 'lembar' ? 'selected' : '' ?>>lembar</option>
                    <option value="liter" <?= $stok['satuan'] == 'liter' ? 'selected' : '' ?>>liter</option>
                    <option value="ml" <?= $stok['satuan'] == 'ml' ? 'selected' : '' ?>>ml</option>
                    <option value="pcs" <?= $stok['satuan'] == 'pcs' ? 'selected' : '' ?>>pcs</option>
                    <option value="renteng" <?= $stok['satuan'] == 'renteng' ? 'selected' : '' ?>>renteng</option>
                    <option value="sachet" <?= $stok['satuan'] == 'sachet' ? 'selected' : '' ?>>sachet</option>
                    <option value="sendok makan" <?= $stok['satuan'] == 'sendok makan' ? 'selected' : '' ?>>sendok makan</option>
                    <option value="sendok teh" <?= $stok['satuan'] == 'sendok teh' ? 'selected' : '' ?>>sendok teh</option>
                </select>

                <!-- SUBMIT BUTTON -->
                <button type="submit" value="Kirim" name="submit" class="btn btn-dark mt-3">Kirim</button>
            </form>
        </div>
    </div>
</body>

</html>