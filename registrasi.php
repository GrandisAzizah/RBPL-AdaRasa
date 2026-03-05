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
        padding: 10px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        max-width: 250px;
        margin: 0 auto;
        padding: 15px;
        border-radius: 8px;
        outline: black solid 1px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    button {
        width: 100%;
        padding: 4px !important;
        margin-bottom: 5px;
    }

    input {
        width: 100%;
        padding: 4px;
        margin-top: 5px;
        border: 1px solid #B3B3B3;
        border-radius: 4px;
    }

    span {
        display: block;
        text-align: center;
        margin-top: 10px;
    }

    a {
        text-decoration: underline;
        color: #000000;
    }
</style>

<body>
    <div class="container">
        <form action="" method="POST" autocomplete="off">
            <label for="username">Username</label><br>
            <input type="text" id="username" name="username" id="username" required><br><br>

            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" id="password" required><br><br>

            <label for="password2">Konfirmasi Password</label><br>
            <input type="password" id="password2" name="password2" id="password2" required><br><br>

            <label for="no_hp">No HP</label><br>
            <input type="text" id="no_hp" name="no_hp" id="no_hp" placeholder="No HP" maxlength="12" required><br><br>

            <button type="submit" name="register" value="register" class="btn btn-dark">Register</button>
            <span>Login <a href="login.php">di sini</a></span>
        </form>
    </div>
</body>

</html>