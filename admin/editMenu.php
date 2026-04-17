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
                <input type="number" name="harga-menu" id="harga-menu" value="<?= ($m["harga_menu"]); ?>" min="0" oninput="validasiHarga(this)"><br><br>
                <small id="error-harga" style="color: red; display: none;">
                    Harga tidak valid. Harus dalam rentang 0 - 999999
                </small>

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
        // batasi maksimal 6 digit
        if (input.value.length > 6) {
            input.value = input.value.slice(0, 6);
        }

        // validasi range dan tampilkan pesan
        if (input.value < 0 || input.value > 999999) {
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    }
</script>