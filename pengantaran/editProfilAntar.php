<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

// Ambil data user yang login dari session
$username = $_SESSION['username'];
$user = query("SELECT * FROM user WHERE username = '$username'")[0];

$pesan = '';
$tipe = '';

$role = $user['role'];
$is_tim_pengantaran = ($role == 'tim pengantaran');

if (isset($_POST["submit"])) {
    $result = editProfil($_POST, $username);

    if ($result['success']) {
        $pesan = $result['message'];
        $tipe = 'success';
        if (isset($result['username_baru'])) {
            $_SESSION['username'] = $result['username_baru'];
        }
        $user = query("SELECT * FROM user WHERE username = '{$_SESSION['username']}'")[0];
    } else {
        $pesan = $result['message'];
        $tipe = 'danger';
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
    <title>Edit Profil</title>
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
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #ffffff;
        border-color: black;
    }

    .btn-close {
        position: absolute;
        top: 10px;
        right: 10px;
        box-shadow: none !important;
    }
</style>

<body>
    <div>
        <div class="menu-item">
            <a href="pengaturanAntar.php">
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
            <h5 class="text-center">Ganti Profil</h5>
        </div>

        <?php if ($pesan): ?>
            <div class="mt-3 mb-3 alert alert-<?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="container">
            <form action="" method="POST" enctype="multipart/form-data">

                <!-- Tampilkan foto profil saat ini -->
                <?php if (!empty($user['foto_profil'])): ?>
                    <div class="text-center mb-3">
                        <img src="<?= $user['foto_profil'] ?>" alt="Foto Profil" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                    </div>
                <?php endif; ?>

                <!-- Hidden untuk gambar lama -->
                <input type="hidden" name="gambarLama" value="<?= $user['foto_profil_user'] ?? '' ?>">

                <label for="nama_depan">Nama Depan</label>
                <input type="text" id="nama_depan" name="nama_depan" value="<?= $user['nama_depan'] ?? '' ?>">

                <label for="nama_belakang">Nama Belakang</label>
                <input type="text" id="nama_belakang" name="nama_belakang" value="<?= $user['nama_belakang'] ?? '' ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= $user['email'] ?? '' ?>">

                <label for="foto_profil_user">Foto Profil</label>
                <input type="file" id="foto_profil_user" name="foto_profil_user" accept="image/*">

                <button type="submit" class="btn btn-dark mt-3" name="submit">Save</button>
            </form>
        </div>
    </div>
</body>

</html>