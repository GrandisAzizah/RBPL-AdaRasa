<?php
session_start();

require 'functions.php';

$pesan = '';
$tipe = '';
// cek apakah ada cookie
if (isset($_COOKIE['k']) && isset($_COOKIE['x'])) {
    $a = $_COOKIE['k'];
    $x = $_COOKIE['x'];

    // ambil username berdasarkan id
    $result = mysqli_query($conn, "SELECT username FROM user WHERE a = $a");
    $row = mysqli_fetch_assoc($result);

    // cek cookie dan username
    if ($x === hash('sha256', $row['username'])) {
        $_SESSION['login'] = true;
    }
}

// cek apakah sudah login ga usah balik ke login
if (isset($_SESSION["login"])) {
    if ($_SESSION['role'] == 'admin') {
        header("location:admin/berandaAdmin.php");
    } else if ($_SESSION['role'] == 'tim dapur') {
        header("location:dapur/pesananDapurDiterima.php");
    } else if ($_SESSION['role'] == 'tim pengantaran') {
        header("location:pengantaran\pengantaranDiterima.php");
    }
}

// cek apakah tombol login sudah diklik
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM user 
    WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result); // dalam row akan sudah ada datanya
        if ($username === $row["username"]) {
            // cek string sama atau tidak dengan hash nya
            if (password_verify($password, $row["password"])) {
                // set session
                $_SESSION["login"] = true;
                $_SESSION['role'] = $row['role'];
                $_SESSION['username'] = $row['username']; // pastiin session username tersimpan saat login

                if ($row['role'] == 'admin') {
                    header("Location: /RBPL-AdaRasa/admin/berandaAdmin.php");
                } else if ($row['role'] == 'tim dapur') {
                    header("Location: /RBPL-AdaRasa/dapur/pesananDapurDiterima.php");
                } else if ($row['role'] == 'tim pengantaran') {
                    header("Location: /RBPL-AdaRasa/pengantaran/pengantaranDiterima.php");
                }
                exit;
            }
        }
    }
    // jika salah maka dia error
    $error = true;
    $pesan = 'Username atau password salah!';
    $tipe = 'danger';
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
    <title>Login Ada Rasa</title>
</head>

<style>
    body {
        font-family: 'Aleo', serif;
        background-color: #ffffff;
        margin: 0;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
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

    .alert {
        width: 250px;
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

    a {
        text-decoration: none !important;
        color: black !important;
    }

    .regist {
        font-size: 14px;
        margin-top: 10px !important;
    }
</style>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const iconOpen = document.getElementById('iconOpen');
        const iconClosed = document.getElementById('iconClosed');
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
    <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
        <?php if ($pesan): ?>
            <div class="alert alert-<?= $tipe ?> alert-dismissible fade show text-center" role="alert"
                style="width: 250px; padding: 12px 20px; margin: 0;">
                <?= $pesan ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                    style="position: absolute; top: 12px; right: 12px;"></button>
            </div>
        <?php endif; ?>

        <div class="container">
            <form action="" method="POST">
                <label for="username">Username</label><br>
                <input type="text" id="username" name="username" required><br><br>

                <label for="passworq">Password</label><br>
                <div style="position: relative;" class="mb-3">
                    <input type="password" id="password" name="password" required style="padding-right: 36px;">
                    <span onclick="togglePassword()" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer;" id="toggleIcon">
                        <svg id="iconOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="#B3B3B3" />
                        </svg>
                        <svg id="iconClosed" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                            <path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 23 12C21.27 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.01 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.84 9.02L14.99 12.17L15.01 12.01C15.01 10.35 13.67 9.01 12.01 9.01L11.84 9.02Z" fill="#B3B3B3" />
                        </svg>
                    </span>
                </div>
                <button type="submit" value="login" class="btn btn-dark mb-3" name="login">Login</button>
                <span class="regist">Belum punya akun? <a href="registrasi.php"><u>Daftar di sini</u></a></span>
            </form>
        </div>
</body>

</html>