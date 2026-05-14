<?php
require '../functions.php';
session_start();

$pesan = '';
$tipe = '';

if (isset($_POST["reset"])) {
    $username_lama = $_SESSION['username'];
    $username_baru = $_POST["username_baru"];

    // Cek apakah username baru sama dengan username lama
    if ($username_lama == $username_baru) {
        $pesan = 'Username baru tidak boleh sama dengan username lama!';
        $tipe = 'danger';
    } else {
        $hasil = ganti_usn($username_lama, $username_baru);

        if ($hasil === true) {
            $_SESSION['username'] = $username_baru; // Update session
            $pesan = 'Username berhasil diganti!';
            $tipe = 'success';
        } elseif ($hasil === 'exists') {
            $pesan = 'Username sudah dipakai oleh pengguna lain!';
            $tipe = 'danger';
        } else {
            $pesan = 'Terjadi kesalahan, silakan coba lagi!';
            $tipe = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Ganti Username</title>
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
        max-width: 250px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
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
    }

    h5 {
        margin: 0;
    }

    label {
        font-weight: 600;
        margin-top: 5px;
    }

    .alert {
        width: 250px;
        margin: 0 auto 15px auto;
        padding: 15px 20px;
        border-radius: 8px;
        outline: 1px solid #000;
        background-color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<body>
    <div>
        <div class="menu-item">
            <a href="pengaturanAdmin.php">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_78_992)">
                        <path d="M19.825 25L25.425 30.6L24 32L16 24L24 16L25.425 17.4L19.825 23H32V25H19.825Z" fill="#1D1B20" />
                    </g>
                    <defs>
                        <clipPath id="clip0_78_992">
                            <rect x="4" y="4" width="40" height="40" rx="20" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
            </a>
            <h5 class="text-center">Ganti Username</h5>
        </div>

        <?php if ($pesan): ?>
            <div class="alert alert-<?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="container">
            <form action="" method="POST">
                <label for="username_lama">Username Lama</label>
                <input type="text" id="username_lama" name="username_lama" value="<?= $_SESSION['username'] ?>" readonly>

                <label for="username_baru">Username Baru</label>
                <input type="text" id="username_baru" name="username_baru" required>

                <button type="submit" class="btn btn-dark mt-3" name="reset">Save</button>
            </form>
        </div>
    </div>
</body>

</html>