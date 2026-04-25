<?php

session_start();
// // user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

if (!isset($_GET["id_pelanggan"]) || !is_numeric(($_GET["id_pelanggan"]))) { //is_numeric mencegah injection
    die("id_pelanggan tidak ditemukan"); //die untuk menghentikan eksekusi
}
$id_pelanggan = (int)$_GET['id_pelanggan'];
$pelanggan = query("SELECT * FROM customer WHERE id_pelanggan = $id_pelanggan");

if (count($pelanggan) == 0) {
    die("Data pelanggan tidak ditemukan");
}

$p = $pelanggan[0];

// alert jika input berhasil atau gagal
$pesan = '';
$tipe = '';
if (isset($_POST["submit"])) {
    $_POST['id_pelanggan'] = $id_pelanggan;
    $_POST['gambarLama'] = $p['profil_foto'];

    $hasil = editPelanggan($_POST);
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
    <title>Input Data Pelanggan</title>
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
            <a href="showPelanggan.php?id_pelanggan=<?= $id_pelanggan ?>" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Edit Data Pelanggan</h5>
        </div>

        <?php if ($pesan): ?>
            <div class="mt-3 mb-3 alert alert- <?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="container">
            <form action="" method="POST" enctype="multipart/form-data">

                <!-- INPUT NAMA MENU -->
                <label for="nama_pelanggan">Nama<br></label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="<?= $p['nama_pelanggan'] ?>" maxlength="30" required><br><br>

                <!-- INPUT NO HP -->
                <label for="no_hp">No HP</label>
                <input type="text" name="no_hp" id="no_hp" value="<?= $p['no_hp'] ?>" maxlength="15" required><br><br>

                <label for="alamat">Alamat</label>
                <input type="text" name="alamat" id="alamat" value="<?= $p['alamat'] ?>" required><br><br>

                <!-- INPUT GAMBAR -->
                <label for="profil_foto">Profil:<br></label>
                <input type="file" name="profil_foto" id="profil_foto" value="<?= $p['profil_foto'] ?>" required><br><br>

                <!-- SUBMIT BUTTON -->
                <button type="submit" value="Kirim" name="submit" class="btn btn-dark">Kirim</button>
            </form>
        </div>
    </div>
</body>

</html>