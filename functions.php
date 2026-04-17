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

// unggah gambar menu
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

    return '/RBPL-AdaRasa/img/' . $namaFileBaru;
}

function hapusMenu($id_menu)
{
    global $conn;

    // ambil path gambar
    $result = mysqli_query($conn, "SELECT gambar_menu FROM menu WHERE id_menu = $id_menu");
    $row = mysqli_fetch_assoc($result);

    // hapus file gambar dari folder
    if ($row) {
        $pathGambar = '../img/' . basename($row['gambar_menu']);

        if (file_exists($pathGambar)) {
            unlink($pathGambar);
        }
    }

    // hapus data menu dari database
    mysqli_query($conn, "DELETE FROM menu WHERE id_menu = $id_menu");
    return mysqli_affected_rows($conn);
}

function editMenu($data)
{
    global $conn;

    $id_menu = $data["id_menu"];
    $nama_menu = htmlspecialchars($data["nama-menu"]);
    $hargaRaw = $data["harga-menu"];
    $hargaClean = preg_replace('/[^0-9]/', '', $hargaRaw);

    if (strlen($hargaClean) > 6 || strlen($hargaClean) == 0) {
        return false;
    }

    $harga = intval($hargaClean);

    // Validasi range
    if ($harga < 0 || $harga > 999999) {
        return false;
    }

    $gambar_menu = $data["gambarLama"];
    $uploadGambarBaru = false;
    if ($_FILES['gambar-menu']['error'] !== 4) {
        $gambar_baru = upload();
        if ($gambar_baru) {
            $gambar_menu = $gambar_baru;
            $uploadGambarBaru = true;
        } else {
            return false; // upload gagal
        }
    }

    if ($uploadGambarBaru) {
        $pathGambarLama = '../img/' . basename($data["gambarLama"]);
        if (file_exists($pathGambarLama)) {
            unlink($pathGambarLama);
        }
    }

    $query = "UPDATE menu SET
                nama_menu = '$nama_menu',
                harga_menu = '$harga',
                gambar_menu = '$gambar_menu'
              WHERE id_menu = $id_menu";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function tambahBahanBaku($data)
{
    global $conn;
    $fk_menu = htmlspecialchars($data['fk_menu']);
    $nama_bahan = htmlspecialchars($data['nama_bahan']);
    $jumlah_default = htmlspecialchars($data['jumlah_default']);
    $satuan = htmlspecialchars($data['satuan']);

    $query = "INSERT INTO bahan_baku VALUES
            (NULL, '$fk_menu', '$nama_bahan', '$jumlah_default', '$satuan')
    ";

    mysqli_query($conn, $query);

    // jika gagal -1, jika berhasil 1
    return mysqli_affected_rows($conn);
}

function hapusBahan($id_bahan)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM bahan_baku WHERE id_bahan = $id_bahan");
    return mysqli_affected_rows($conn);
}

function editBahan($data)
{
    global $conn;

    $id_bahan = $data["id_bahan"];
    $nama_bahan = htmlspecialchars($data['nama_bahan']);
    $jumlah_default = htmlspecialchars($data['jumlah_default']);
    $satuan = htmlspecialchars($data['satuan']);

    $query = "UPDATE bahan_baku SET
                nama_bahan = '$nama_bahan',
                jumlah_default = '$jumlah_default',
                satuan = '$satuan'
              WHERE id_bahan = $id_bahan";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function tambahPesanan($data)
{
    global $conn;
    $nama_pelanggan = htmlspecialchars($data['nama_pelanggan']);
    $menu_fk = htmlspecialchars($data['menu_fk']);
    $porsi = htmlspecialchars($data['porsi']);
    $takaran = htmlspecialchars($data['takaran']);
    $packing = htmlspecialchars($data['packing']);
    $harga_menu = htmlspecialchars($data['harga_menu']);
    $harga_total = htmlspecialchars($data['harga_menu']) * htmlspecialchars($data['porsi']);
    $status_pemesanan = htmlspecialchars($data['status_pemesanan']);
    $tanggal_pesan = htmlspecialchars($data['tanggal_pesan']);
    $catatan_khusus_pemesanan = htmlspecialchars($data['catatan_khusus_pemesanan']);
    $tanggal_antar = htmlspecialchars($data['tanggal_antar']);

    $query = "INSERT INTO pesanan VALUES
            (NULL, , '$porsi','$harga_total', $harga_menu','$status_pemesanan',
            '$tanggal_pesan','$catatan_khusus_pemesanan',
            '$tanggal_antar', '$menu_fk')
    ";

    mysqli_query($conn, $query);

    // jika gagal -1, jika berhasil 1
    return mysqli_affected_rows($conn);
}

function hapusPesanan($id_pesanan)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = $id_pesanan");
    return mysqli_affected_rows($conn);
}

function editPesanan($data)
{
    global $conn;

    $id_pesanan = htmlspecialchars($data['id_pesanan']);
    $nama_pelanggan = htmlspecialchars($data['nama_pelanggan']);
    $fk_menu = htmlspecialchars($data['fk_menu']);
    $porsi = htmlspecialchars($data['porsi']);
    $harga_menu = htmlspecialchars($data['harga_menu']);
    $harga_total = htmlspecialchars($data['harga_menu']) * htmlspecialchars($data['porsi']);
    $status_pemesanan = htmlspecialchars($data['status_pemesanan']);
    $tanggal_pesan = htmlspecialchars($data['tanggal_pesan']);
    $catatan_khusus_pemesanan = htmlspecialchars($data['catatan_khusus_pemesanan']);
    $tanggal_antar = htmlspecialchars($data['tanggal_antar']);

    $query = "UPDATE pesanan SET
               nama_pelanggan = '$nama_pelanggan',
               porsi = '$porsi',
               harga_menu = '$harga_menu',
               status_pemesanan = '$status_pemesanan',
               tanggal_pesan = '$tanggal_pesan',
               catatan_khusus_pemesanan = '$catatan_khusus_pemesanan',
               tanggal_antar = '$tanggal_antar'
              WHERE id_pesanan = $id_pesanan";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function upload_profil()
{
    $namaFile = $_FILES['profil_foto']['name'];
    $ukuranFile = $_FILES['profil_foto']['size'];
    $error = $_FILES['profil_foto']['error'];
    $tmpName = $_FILES['profil_foto']['tmp_name'];

    // cek apakah ada gambar yang diupload
    if ($error == 4) {
        echo "<script>alert('Silakan unggah foto profil terlebih dahulu.')</script>";
        return false;
    }

    $ekstensiGambarValid = ['jpg', 'png', 'jpeg', 'webp'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>alert('Silakan unggah gambar dengan format png, jpg, webp atau jpeg')</script>";
        return false;
    }

    if ($ukuranFile > 5000000) {
        echo "<script>alert('Ukuran gambar terlalu besar. Ukuran maksimal adalah 5 MB')</script>";
        return false;
    }

    $namaFileBaru = uniqid();
    $namaFileBaru .= '.' . $ekstensiGambar;

    move_uploaded_file($tmpName, '../img/' . $namaFileBaru);

    return '/RBPL-AdaRasa/img/' . $namaFileBaru;
}

function tambahPelanggan($data)
{
    global $conn;
    $nama_pelanggan = htmlspecialchars($data["nama_pelanggan"]);
    $no_hp = htmlspecialchars($data["no_hp"]);
    $alamat = htmlspecialchars($data["alamat"]);
    // upload gambar
    $profil_foto = upload_profil();
    if (!$profil_foto) {
        return false; // insert tidak dijalankan
    }

    $query = "INSERT INTO customer VALUES
            (NULL, '$nama_pelanggan', '$no_hp', '$alamat','$profil_foto')
    ";

    mysqli_query($conn, $query);

    // jika gagal -1, jika berhasil 1
    return mysqli_affected_rows($conn);
}

function editPelanggan($data)
{
    global $conn;

    $id_pelanggan = $data["id_pelanggan"];
    $nama_pelanggan = htmlspecialchars($data["nama_pelanggan"]);
    $no_hp = htmlspecialchars($data["no_hp"]);
    $alamat = htmlspecialchars($data['alamat']);

    if ($_FILES['profil_foto']['error'] === 4) {
        $profil_foto = $data["gambarLama"];
    } else {
        $profil_foto = upload_profil();
        if (!$profil_foto) {
            return false; // jika upload gagal, hentikan proses edit
        }
    }

    $pathGambarLama = '../img/' . basename($data["gambarLama"]);
    if (file_exists($pathGambarLama)) {
        unlink($pathGambarLama);
    }

    $query = "UPDATE menu SET
                nama_pelanggan = '$nama_pelanggan',
                no_hp = '$no_hp',
                alamat = '$alamat',
                profil_foto = '$profil_foto'
              WHERE id_pelanggan = $id_pelanggan";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapusPelanggan($id_pelanggan)
{
    global $conn;

    // ambil path gambar
    $result = mysqli_query($conn, "SELECT profil_foto FROM customer WHERE id_pelanggan = $id_pelanggan");
    $row = mysqli_fetch_assoc($result);

    // hapus file gambar dari folder
    if ($row) {
        $pathGambar = '../img/' . basename($row['profil_foto']);

        if (file_exists($pathGambar)) {
            unlink($pathGambar);
        }
    }

    // hapus data menu dari database
    mysqli_query($conn, "DELETE FROM customer WHERE id_pelanggan = $id_pelanggan");
    return mysqli_affected_rows($conn);
}
