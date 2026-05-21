<!-- REGISTRASI ATAU SIGN UP -->
<?php
require 'functions.php';
// kondisi apakah tombol register sudah ditekan
if (isset($_POST["register"])) {
    if (registrasi($_POST) > 0) {
        echo "<script>
        alert('User baru berhasil ditambahkan')
        </script>";
    } else {
        echo mysqli_error($conn);
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
    <title>Registrasi Ada Rasa</title>
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

    span {
        display: block;
        text-align: center;
        margin-top: 10px;
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

    function togglePassword2() {
        const input = document.getElementById('password2');
        const iconOpen2 = document.getElementById('iconOpen2');
        const iconClosed2 = document.getElementById('iconClosed2');
        if (input.type === 'password') {
            input.type = 'text';
            iconOpen2.style.display = 'none';
            iconClosed2.style.display = 'block';
        } else {
            input.type = 'password';
            iconOpen2.style.display = 'block';
            iconClosed2.style.display = 'none';
        }
    }
</script>

<body>
    <div class="container">
        <form action="" method="POST" autocomplete="off">
            <label for="username">Username</label><br>
            <input type="text" id="username" name="username" id="username" required><br><br>

            <label for="password">Password</label><br>
            <div style="position: relative;" class="mb-3">
                <input type="password" id="password" name="password" required style="padding-right: 36px;">
                <span onclick="togglePassword()" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer;" id="toggleIcon">
                    <svg id="iconOpen" class="mb-3" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="#B3B3B3" />
                    </svg>
                    <svg id="iconClosed" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                        <path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 23 12C21.27 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.01 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.84 9.02L14.99 12.17L15.01 12.01C15.01 10.35 13.67 9.01 12.01 9.01L11.84 9.02Z" fill="#B3B3B3" />
                    </svg>
                </span>
            </div>

            <label for="password">Konfirmasi Password</label><br>
            <div style="position: relative;" class="mb-3">
                <input type="password" id="password2" name="password2" required style="padding-right: 36px; width: 100%;">
                <span onclick="togglePassword2()" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                    <svg id="iconOpen2" class="mb-3" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="#B3B3B3" />
                    </svg>
                    <svg id="iconClosed2" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none; margin-bottom: 5px;">
                        <path d="M12 7C14.76 7 17 9.24 17 12C17 12.65 16.87 13.26 16.64 13.83L19.56 16.75C21.07 15.49 22.26 13.86 23 12C21.27 7.61 17 4.5 12 4.5C10.6 4.5 9.26 4.75 8.01 5.2L10.17 7.36C10.74 7.13 11.35 7 12 7ZM2 4.27L4.28 6.55L4.74 7.01C3.08 8.3 1.78 10.02 1 12C2.73 16.39 7 19.5 12 19.5C13.55 19.5 15.03 19.2 16.38 18.66L16.8 19.08L19.73 22L21 20.73L3.27 3L2 4.27ZM7.53 9.8L9.08 11.35C9.03 11.56 9 11.78 9 12C9 13.66 10.34 15 12 15C12.22 15 12.44 14.97 12.65 14.92L14.2 16.47C13.53 16.8 12.79 17 12 17C9.24 17 7 14.76 7 12C7 11.21 7.2 10.47 7.53 9.8ZM11.84 9.02L14.99 12.17L15.01 12.01C15.01 10.35 13.67 9.01 12.01 9.01L11.84 9.02Z" fill="#B3B3B3" />
                    </svg>
                </span>
            </div>

            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" id="email" placeholder="Email" required><br><br>

            <button type="submit" name="register" value="register" class="btn btn-dark">Register</button>
            <span>Login <a href="login.php"><u>di sini</u></a></span>
        </form>
    </div>
</body>

</html>