<?php
// ini dibuat biar nanti di setiap menu gak perlu buat koneksi lagi dan tinggal panggil saja function nya
// koneksi ke database ("namahost", "username", "password", "nama_database")
$conn = mysqli_connect("localhost", "root", "", "adarasa");

// function query
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function registrasi($data)
{
    global $conn;

    // stripslashes untuk backslash dll biar ga masuk ke database
    // htmlspecialchar untuk mastiin user ga input yang aneh2
    $username = htmlspecialchars(stripslashes($data["username"]));
    // mysqli_real_escape_string memungkinkan user input password dengan tanda kutip
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);
    $no_hp = htmlspecialchars(stripslashes($data["no_hp"]));

    // cek apakah username udah ada
    $result = mysqli_query($conn, "SELECT username FROM 
    user WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
        alert('Username sudah terdaftar.')
        </script>";
        return false; // agar insert tidak terjadi
    }

    if ($password != $password2) {
        echo "<script>
        alert('Konfirmasi password tidak sesuai!')
        </script>";
        return false;
    }

    // enkripsi password
    // PASSWORD DEFAULT dipilih oleh php dan akan terus update jika ada cara yang baru
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tambahkan userbaru ke database
    mysqli_query($conn, "INSERT INTO user (username, password, no_hp, role) VALUES ('$username', '$password', '$no_hp', 'user')");

    return mysqli_affected_rows($conn);
}

function ganti_pw($data)
{
    global $conn;

    $no_hp = htmlspecialchars($data['no_hp']);
    $passwordNew = password_hash($data['passwordNew'], PASSWORD_DEFAULT);

    $query = "UPDATE user SET password = ? WHERE no_hp = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $passwordNew, $no_hp);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // jika gagal -1, jika berhasil 1
    return mysqli_stmt_affected_rows($stmt);
}

function ganti_usn($data)
{
    global $conn;

    $no_hp = htmlspecialchars($data['no_hp']);
    $usernameNew = htmlspecialchars($data['usernameNew']);

    $query = "UPDATE user SET username = ? WHERE no_hp = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $usernameNew, $no_hp);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // jika gagal -1, jika berhasil 1
    return mysqli_stmt_affected_rows($stmt);
}

function tambah($data)
{
    global $conn;
    // ambil data dari setiap elemen dalam form
    // disimpan ke dalam variabel biar nanti di query gampang
    // diubah jadi $data["..."] karena elemen form di 'post' dan ditangkap oleh parameter $data
    $nama_menu = htmlspecialchars($data["nama-menu"]);
    $harga = htmlspecialchars($data["harga-menu"]);
    // upload gambar
    $gambar_menu = upload();
    if (!$gambar_menu) {
        return false; // insert tidak dijalankan
    }

    $query = "INSERT INTO menu VALUES
            (NULL, '$nama_menu', '$harga', '$gambar_menu')
    ";

    mysqli_query($conn, $query);

    // jika gagal -1, jika berhasil 1
    return mysqli_affected_rows($conn);
}

function upload()
{
    $namaFile = $_FILES['gambar-menu']['name'];
    $ukuranFile = $_FILES['gambar-menu']['size'];
    $error = $_FILES['gambar-menu']['error'];
    $tmpName = $_FILES['gambar-menu']['tmp_name'];

    // cek apakah ada gambar yang diupload
    if ($error == 4) { // 4: tidak ada file yang diunggah
        return ['status' => 'error', 'pesan' => 'Silakan unggah gambar terlebih dahulu.'];
    }

    // cek apakah yang diunggah gambar atau bukan agar user hanya unggah gambar
    // yang bisa diunggah cuma bentuk jpg dll
    $ekstensiGambarValid = ['jpg', 'png', 'jpeg', 'webp'];
    // explode = memecah string menjadi array
    // explode->contoh nama gambar saat upload adalah gambar.jpg nanti diubah jadi ['gambar', 'jpg']
    $ekstensiGambar = explode('.', $namaFile);
    // buat ambil format gambar saja seperti jpg, jpeg dll
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    // cek apakah format yang diunggah termasuk yang diperbolehkan di ekstensiGambarValid
    // in_array = apakah ada sebuah string dalam array
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>alert('Silakan unggah gambar dengan format png, jpg, webp atau jpeg')</script>";
        return false;
    }
    // cek ukuran gambar
    if ($ukuranFile > 5000000) {
        echo "<script>alert('Ukuran gambar terlalu besar. Ukuran maksimal adalah 5 MB')</script>";
        return false;
    }

    // generate nama file baru untuk file yang diunggah agar tidak ada duplikasi
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.' . $ekstensiGambar;

    // masukkan ke direktori
    move_uploaded_file($tmpName, '../img/' . $namaFileBaru);

    return '../img/' . $namaFileBaru;
}
