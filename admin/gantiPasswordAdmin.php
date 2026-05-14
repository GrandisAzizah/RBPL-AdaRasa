<?php
require '../functions.php';
session_start();

$pesan = '';
$tipe = '';

if (isset($_POST["reset"])) {
    $username = $_SESSION['username'];
    $password_lama = $_POST["password_lama"];
    $password_baru = $_POST["password_baru"];

    // Cek apakah password baru sama dengan password lama
    if ($password_lama == $password_baru) {
        $pesan = 'Password baru tidak boleh sama dengan password lama!';
        $tipe = 'danger';
    } else {
        $hasil = ganti_pw($username, $password_lama, $password_baru);

        if ($hasil === true) {
            $pesan = 'Password berhasil diganti!';
            $tipe = 'success';
        } elseif ($hasil === 'wrong') {
            $pesan = 'Password lama salah!';
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
    <title>Ganti Password</title>
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
        width: 290px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #ffffff;
        border-color: black;
        margin-bottom: 10px;
        color: black;
    }

    .btn-close {
        position: absolute;
        top: 10px;
        right: 10px;
        box-shadow: none !important;
    }
</style>

<script>
    function togglePassword(inputId, openIconId, closedIconId) {
        const input = document.getElementById(inputId);
        const iconOpen = document.getElementById(openIconId);
        const iconClosed = document.getElementById(closedIconId);
        if (input.type === 'password') {
            input.type = 'text';
            iconOpen.style.display = 'none';
            iconClosed.style.display = 'block';
        } else {
            input.type = 'password';
            iconOpen.style.display = 'block';
            iconClosed.style.display = 'none';
        }
    }
</script>

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
            <h5 class="text-center">Ganti Password</h5>
        </div>

        <?php if ($pesan): ?>
            <div class="alert alert-<?= $tipe ?> alert-dismissible fade show" role="alert">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="container">
            <form action="" method="POST">
                <label for="password_lama">Password Lama</label>
                <div style="position: relative;">
                    <input type="password" id="password_lama" name="password_lama" required style="padding-right: 36px;">
                    <span onclick="togglePassword('password_lama', 'iconOpen1', 'iconClosed1')" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <svg id="iconOpen1" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="#B3B3B3" />
                        </svg>
                        <svg id="iconClosed1" width="20" height="20" viewBox="0 0 24 24" fill="none" style="display:none;">
                            <path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 23 12C21.27 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.01 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.84 9.02L14.99 12.17L15.01 12.01C15.01 10.35 13.67 9.01 12.01 9.01L11.84 9.02Z" fill="#B3B3B3" />
                        </svg>
                    </span>
                </div>

                <label for="password_baru">Password Baru</label>
                <div style="position: relative;">
                    <input type="password" id="password_baru" name="password_baru" required style="padding-right: 36px;">
                    <span onclick="togglePassword('password_baru', 'iconOpen2', 'iconClosed2')" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <svg id="iconOpen2" width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="#B3B3B3" />
                        </svg>
                        <svg id="iconClosed2" width="20" height="20" viewBox="0 0 24 24" fill="none" style="display:none;">
                            <path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 23 12C21.27 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.01 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.84 9.02L14.99 12.17L15.01 12.01C15.01 10.35 13.67 9.01 12.01 9.01L11.84 9.02Z" fill="#B3B3B3" />
                        </svg>
                    </span>
                </div>
                <button type="submit" class="btn btn-dark mt-3" name="reset">Save</button>
            </form>
        </div>
    </div>
</body>

</html>