<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}
require '../functions.php';

if (isset($_POST['submit'])) {
    $nama   = mysqli_real_escape_string($conn, trim($_POST['nama_bahan']));
    $jumlah = floatval($_POST['jumlah']);
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);

    $cekStok = mysqli_query(
        $conn,
        "SELECT id_stok FROM stok_bahan WHERE nama_bahan_stok = '$nama' LIMIT 1"
    );
    $rowStok = mysqli_fetch_assoc($cekStok);
    if ($rowStok) {
        $idStok = $rowStok['id_stok'];
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO stok_bahan (nama_bahan_stok, stok_tersedia, satuan)
             VALUES ('$nama', 0, '$satuan')"
        );
        $idStok = mysqli_insert_id($conn);
    }

    mysqli_query(
        $conn,
        "INSERT INTO bahan_baku
            (nama_bahan, jumlah_default, satuan,
             fk_menu_bahan, fk_bahan_stok, fk_varian_bahan)
         VALUES
            ('$nama', $jumlah, '$satuan',
             NULL, $idStok, NULL)"
    );
    $idBahan = mysqli_insert_id($conn);

    $_SESSION['pesanan']['bahan_tambahan'][] = [
        'id_bahan'   => $idBahan,
        'nama_bahan' => $nama,
        'jumlah'     => $jumlah,
        'satuan'     => $satuan
    ];

    header('Location: inputBahanPesan.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Tambah Bahan</title>
    <style>
        body {
            font-family: 'Aleo', serif;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 290px;
            padding: 20px;
            border-radius: 8px;
            outline: black solid 1px;
        }

        input,
        select {
            width: 100%;
            padding: 4px;
            margin-top: 5px;
            border: 1px solid #B3B3B3;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        label {
            font-weight: 600;
            margin-top: 5px;
            display: block;
        }

        button {
            width: 100%;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div>
        <div style="display:flex; align-items:center; justify-content:center; position:relative;" class="mb-3">
            <a href="inputBahanPesan.php" style="position:absolute; left:0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin:0;">Tambah Bahan</h5>
        </div>
        <div class="container">
            <form method="POST">
                <label for="nama_bahan">Nama Bahan</label>
                <input type="text" name="nama_bahan" id="nama_bahan" maxlength="30" required>

                <label for="jumlah">Jumlah (per porsi)</label>
                <input type="number" name="jumlah" id="jumlah" min="0" step="0.01" required>

                <label for="satuan">Satuan</label>
                <select name="satuan" id="satuan">
                    <option value="buah">buah</option>
                    <option value="bungkus">bungkus</option>
                    <option value="gelas">gelas</option>
                    <option value="gram">gram</option>
                    <option value="kg">kg</option>
                    <option value="lembar">lembar</option>
                    <option value="liter">liter</option>
                    <option value="ml">ml</option>
                    <option value="pcs">pcs</option>
                    <option value="renteng">renteng</option>
                    <option value="sachet">sachet</option>
                    <option value="sendok makan">sendok makan</option>
                    <option value="sendok teh">sendok teh</option>
                </select>

                <button type="submit" name="submit" class="btn btn-dark mt-3">Tambah</button>
            </form>
        </div>
    </div>
</body>

</html>