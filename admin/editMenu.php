<?php

session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

// ambil data di url
if (!isset($_GET["id_menu"]) || !is_numeric(($_GET["id_menu"]))) { //is_numeric mencegah injection
    die("id_menu tidak ditemukan"); //die untuk menghentikan eksekusi
}
$id_menu = (int)$_GET["id_menu"];
$m = query("SELECT * FROM menu WHERE id_menu = $id_menu")[0];
$varian = query("SELECT * FROM menu_varian WHERE fk_menu_varian = $id_menu");

$pesan = '';
$tipe = '';
if (isset($_POST["submit"])) {
    $hasil = editMenu($_POST);
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
    <title>Edit Menu</title>
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
    <div>
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3">
            <a href="menu.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Edit Menu</h5>
        </div>
        <?php if ($pesan): ?>
            <div class="mt-3 mb-3 alert alert- <?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="container">
            <form action="" method="POST" enctype="multipart/form-data">
                <!-- id atau ID -->
                <input type="hidden" name="id_menu" value="<?= ($m["id_menu"]); ?>">
                <input type="hidden" name="gambarLama" value="<?= ($m["gambar_menu"]); ?>">

                <!-- INPUT NAMA MENU -->
                <label for="nama-menu">Nama Menu <br></label>
                <input type="text" name="nama-menu" id="nama-menu" value="<?= ($m["nama_menu"]); ?>" maxlength="30"><br><br>

                <!-- INPUT HARGA -->
                <label for="harga-menu">Harga Menu:<br></label>
                <input type="text" name="harga-menu" id="harga-menu" value="<?= ($m["harga_menu"]); ?>" min="0" oninput="validasiHarga(this)" inputmode="numeric" pattern="[0-9]*"><br><br>
                <small id="error-harga" style="color: red; display: none;">
                    Harga tidak valid. Harus dalam rentang 0 - 999999
                </small>

                <label for="kategori">Kategori Menu:<br></label>
                <select name="kategori" id="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Nasi Box" <?= ($m["kategori_menu"] == "Nasi Box") ? "selected" : "" ?>>Nasi Box</option>
                    <option value="Kue Kering" <?= ($m["kategori_menu"] == "Kue Kering") ? "selected" : "" ?>>Kue Kering</option>
                    <option value="Cake" <?= ($m["kategori_menu"] == "Cake") ? "selected" : "" ?>>Cake</option>
                    <option value="Kue Brownies" <?= ($m["kategori_menu"] == "Kue Brownies") ? "selected" : "" ?>>Kue Brownies</option>
                </select>

                <label>Varian Takaran</label>
                <div id="varian-container">
                    <?php if (count($varian) > 0) : ?>
                        <?php foreach ($varian as $v) : ?>
                            <div class="varian-field mb-2">
                                <input type="text" name="takaran[]" value="<?= $v['takaran'] ?>" placeholder="Takaran" class="mb-1" style="width: 100%;">
                                <input type="text" name="harga_tambahan[]" value="<?= $v['harga_varian'] ?>"
                                    placeholder="Harga tambahan" class="mb-1" style="width: 100%;"
                                    oninput="validasiHargaTambahan(this)">
                                <button type="button" onclick="this.parentElement.remove()" class="btn btn-sm btn-danger mt-1" style="width: auto; padding: 2px 8px !important;">Hapus</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="varian-field mb-2">
                            <input type="text" name="takaran[]" placeholder="Takaran (contoh: 500 gram)" class="mb-1" style="width: 100%;">
                            <input type="text" name="harga_tambahan[]" placeholder="Harga tambahan" class="mb-1" style="width: 100%;">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" onclick="tambahVarian()" class="btn btn-sm btn-outline-secondary mt-1" style="width: auto; padding: 4px 12px !important;">+ Tambah Varian</button><br><br>

                <!-- INPUT GAMBAR -->
                <label for="gambar-menu">Gambar:<br></label>
                <img src="<?= ($m["gambar_menu"]); ?>" alt="" style="width: 100%; margin-bottom: 10px; border-radius: 4px;">
                <input type="file" name="gambar-menu" id="gambar-menu"><br><br>

                <!-- SUBMIT BUTTON -->
                <button type="submit" value="Kirim" name="submit" class="btn btn-dark mt-3">Kirim</button>
            </form>
        </div>
    </div>
</body>

</html>

<script>
    function validasiHarga(input) {
        const error = document.getElementById('error-harga');
        let value = input.value.replace(/[^\d]/g, '');

        if (value.length > 6) {
            value = value.slice(0, 6);
        }

        value = value.replace(/^0+/, '');
        if (value === '') value = '0';

        input.value = value;

        const numericValue = parseInt(value, 10);
        if (isNaN(numericValue) || numericValue < 0 || numericValue > 999999) {
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        let hargaInput = document.getElementById('harga-menu');
        let hargaValue = hargaInput.value.replace(/[^\d]/g, '');
        hargaInput.value = hargaValue;
        console.log('Nilai yang dikirim:', hargaValue);
    });

    function tambahVarian() {
        const container = document.getElementById('varian-container');
        const div = document.createElement('div');
        div.className = 'varian-field mb-2';
        div.innerHTML = `
            <input type="text" name="takaran[]" placeholder="Takaran (contoh: 800 gram)" style="width: 100%;" class="mb-1">
            <input type="number" name="harga_tambahan[]" placeholder="Harga tambahan" style="width: 100%;" class="mb-1">
            <button type="button" onclick="this.parentElement.remove()" class="btn btn-sm btn-danger mt-1" style="width: auto; padding: 2px 8px !important;">Hapus</button>
        `;
        container.appendChild(div);
    }

    function validasiHarga(input) {
        const error = document.getElementById('error-harga');
        let value = input.value.replace(/[^\d]/g, '');

        if (value.length > 6) {
            value = value.slice(0, 6);
        }

        value = value.replace(/^0+/, '');
        if (value === '') value = '0';

        input.value = value;

        const numericValue = parseInt(value, 10);
        if (isNaN(numericValue) || numericValue < 0 || numericValue > 999999) {
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    }

    // Validasi untuk input harga_tambahan
    function validasiHargaTambahan(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value === '') value = '0';
        input.value = value;
    }
</script>