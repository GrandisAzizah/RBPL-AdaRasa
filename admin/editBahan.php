<?php

session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

// ambil data di url
if (!isset($_GET["id_bahan"]) || !is_numeric(($_GET["id_bahan"]))) { //is_numeric mencegah injection
    die("id_bahan tidak ditemukan"); //die untuk menghentikan eksekusi
}

$id_bahan = (int)$_GET["id_bahan"];
$bahan = query("SELECT * FROM bahan_baku WHERE id_bahan = $id_bahan")[0];
$varian = query("SELECT * FROM menu_varian WHERE fk_menu_varian = $id_menu");

// ambil id menu dari data bahan (fk_bahan_menu)
$id_menu = $bahan['fk_menu_bahan'];

// ambil nama menu untuk ditampilkan
if ($id_menu > 0) {
    $menu = query("SELECT nama_menu FROM menu WHERE id_menu = $id_menu");
    $nama_menu = $menu[0]['nama_menu'] ?? 'Menu tidak ditemukan';
} else {
    $nama_menu = 'Menu tidak ditemukan';
}

$pesan = '';
$tipe = '';

if (isset($_POST["submit"])) {
    $hasil = editBahan($_POST);
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
    <title>Edit Bahan Baku</title>
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
        /* hapus 'v' */
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4 6L8 10L12 6' stroke='%231E1E1E' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
        background-repeat: no-repeat;
        background-position: right 10px center;
        /* arrow tidak mepet */
        cursor: pointer;
        font-size: 13px;

    }
</style>

<body>
    <div>
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3">
            <a href="showBahan.php?id_menu=<?= $bahan['fk_menu_bahan'] ?>" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Edit Bahan Baku</h5>
        </div>
        <?php if ($pesan): ?>
            <div class="mt-3 mb-3 alert alert- <?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="container">
            <form action="" method="POST" enctype="multipart/form-data">

                <!-- Nama Menu (read) -->
                <label>Nama Menu</label><br>
                <input type="hidden" name="id_bahan" value="<?= $bahan['id_bahan'] ?>">
                <input type="text" value="<?= $nama_menu ?>" disabled><br><br>

                <!-- INPUT NAMA BAHAN -->
                <label for="nama_bahan">Nama Bahan Baku <br></label>
                <input type="text" name="nama_bahan" id="nama_bahan" value="<?= $bahan['nama_bahan'] ?>" maxlength="40" required><br><br>

                <!-- INPUT HARGA -->
                <label for="jumlah_default">Jumlah:<br></label>
                <input type="number" name="jumlah_default" id="jumlah_default" value="<?= $bahan['jumlah_default'] ?>" min="0" max="999999" required><br><br>

                <!-- INPUT SATUAN -->
                <label for="satuan">Satuan</label><br>
                <select name="satuan" id="satuan">
                    <option value="buah" <?= $bahan['satuan'] == 'buah' ? 'selected' : '' ?>>buah</option>
                    <option value="bungkus" <?= $bahan['satuan'] == 'bungkus' ? 'selected' : '' ?>>bungkus</option>
                    <option value="gelas" <?= $bahan['satuan'] == 'gelas' ? 'selected' : '' ?>>gelas</option>
                    <option value="gram" <?= $bahan['satuan'] == 'gram' ? 'selected' : '' ?>>gram</option>
                    <option value="kg" <?= $bahan['satuan'] == 'kg' ? 'selected' : '' ?>>kg</option>
                    <option value="lembar" <?= $bahan['satuan'] == 'lembar' ? 'selected' : '' ?>>lembar</option>
                    <option value="liter" <?= $bahan['satuan'] == 'liter' ? 'selected' : '' ?>>liter</option>
                    <option value="ml" <?= $bahan['satuan'] == 'ml' ? 'selected' : '' ?>>ml</option>
                    <option value="pcs" <?= $bahan['satuan'] == 'pcs' ? 'selected' : '' ?>>pcs</option>
                    <option value="renteng" <?= $bahan['satuan'] == 'renteng' ? 'selected' : '' ?>>renteng</option>
                    <option value="sachet" <?= $bahan['satuan'] == 'sachet' ? 'selected' : '' ?>>sachet</option>
                    <option value="sendok makan" <?= $bahan['satuan'] == 'sendok makan' ? 'selected' : '' ?>>sendok makan</option>
                    <option value="sendok teh" <?= $bahan['satuan'] == 'sendok teh' ? 'selected' : '' ?>>sendok teh</option>
                </select>

                <label>Jumlah per Varian</label>
                <div id="varian-container">
                    <?php if (empty($varian)): ?>
                        <!-- Kalau tidak ada varian, satu input saja -->
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                            <span style="flex:1; font-size:13px;">Semua varian</span>
                            <input type="number" name="jumlah_default[]" placeholder="Jumlah" min="0" step="0.01" required style="flex:1; margin:0;">
                            <input type="hidden" name="fk_varian_bahan[]" value="">
                        </div>
                    <?php else: ?>
                        <?php foreach ($varian as $v): ?>
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                                <span style="flex:1; font-size:13px;"><?= $v['takaran'] ?></span>
                                <input type="number" name="jumlah_default[]" placeholder="Jumlah" min="0" step="0.01" style="flex:1; margin:0;">
                                <input type="hidden" name="fk_varian_bahan[]" value="<?= $v['id_varian'] ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" value="Kirim" name="submit" class="btn btn-dark mt-3">Kirim</button>
            </form>
        </div>
    </div>
</body>

</html>

<script>
    document.getElementById('harga-menu').addEventListener('input', function() {
        if (this.value > 999999) {
            this.value = this.value.slice(0, -1); // hapus karakter terakhir
        }
    });
</script>