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

// untuk menu
function tambahMenu($data)
{
    global $conn;

    $nama_menu = htmlspecialchars($data["nama-menu"]);
    // Konversi harga menu (bersihin dari non-angka)
    $harga_menu_clean = preg_replace('/[^0-9]/', '', $data["harga-menu"]);
    $harga_menu = intval($harga_menu_clean);

    $gambar_menu = upload();
    if (!$gambar_menu) {
        return false;
    }

    $query = "INSERT INTO menu (nama_menu, harga_menu, gambar_menu) 
              VALUES ('$nama_menu', '$harga_menu', '$gambar_menu')";
    mysqli_query($conn, $query);
    $id_menu = mysqli_insert_id($conn);

    // Insert varian
    if (isset($data['takaran']) && is_array($data['takaran'])) {
        for ($i = 0; $i < count($data['takaran']); $i++) {
            $takaran = trim($data['takaran'][$i]);
            // Konversi harga tambahan (bersihin dari non-angka)
            $harga_tambahan_clean = preg_replace('/[^0-9]/', '', $data['harga_tambahan'][$i] ?? '0');
            $harga_tambahan = intval($harga_tambahan_clean);

            if (!empty($takaran)) {
                $queryVarian = "INSERT INTO menu_varian (fk_menu_varian, takaran, harga_varian) 
                                VALUES ('$id_menu', '$takaran', '$harga_tambahan')";
                mysqli_query($conn, $queryVarian);
            }
        }
    }

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

    mysqli_query($conn, "DELETE FROM menu_varian WHERE fk_menu_varian = $id_menu");

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
            return false;
        }
    }

    if ($uploadGambarBaru) {
        $pathGambarLama = '../img/' . basename($data["gambarLama"]);
        if (file_exists($pathGambarLama)) {
            unlink($pathGambarLama);
        }
    }

    // Update menu
    $query = "UPDATE menu SET
                nama_menu = '$nama_menu',
                harga_menu = '$harga',
                gambar_menu = '$gambar_menu'
              WHERE id_menu = $id_menu";
    mysqli_query($conn, $query);

    // Hapus varian lama
    mysqli_query($conn, "DELETE FROM menu_varian WHERE fk_menu_varian = $id_menu");

    // Insert varian baru
    if (isset($data['takaran']) && is_array($data['takaran'])) {
        for ($i = 0; $i < count($data['takaran']); $i++) {
            $takaran = trim($data['takaran'][$i]);
            $harga_tambahan = isset($data['harga_tambahan'][$i]) ? (int)$data['harga_tambahan'][$i] : 0;

            if (!empty($takaran)) {
                $queryVarian = "INSERT INTO menu_varian (fk_menu_varian, takaran, harga_varian) 
                                VALUES ('$id_menu', '$takaran', '$harga_tambahan')";
                mysqli_query($conn, $queryVarian);
            }
        }
    }

    return mysqli_affected_rows($conn);
}

function parseFraksi($str)
{
    $str = trim($str);
    if (strpos($str, '/') !== false) {
        [$pembilang, $penyebut] = explode('/', $str);
        return $penyebut != 0 ? floatval($pembilang) / floatval($penyebut) : 0;
    }
    return floatval(str_replace(',', '.', $str));
}

function tambahBahanBaku($data)
{
    global $conn;
    $fk_menu = (int)$data['fk_menu'];
    $nama_bahan = htmlspecialchars($data['nama_bahan']);
    $satuan = htmlspecialchars($data['satuan']);

    // Cek/insert stok
    $cek = mysqli_query($conn, "SELECT id_stok FROM stok_bahan WHERE nama_bahan_stok = '$nama_bahan' LIMIT 1");
    $stok = mysqli_fetch_assoc($cek);
    if ($stok) {
        $id_stok = $stok['id_stok'];
    } else {
        mysqli_query($conn, "INSERT INTO stok_bahan (nama_bahan_stok, stok_tersedia, satuan) VALUES ('$nama_bahan', 0, '$satuan')");
        $id_stok = mysqli_insert_id($conn);
    }

    // Loop setiap baris varian
    $jumlah_list = $data['jumlah_default'];
    $varian_list = $data['fk_varian_bahan'];
    $berhasil = 0;

    foreach ($jumlah_list as $i => $jumlah_raw) {
        if ($jumlah_raw === '' || $jumlah_raw === null) continue;
        $jumlah = parseFraksi($jumlah_raw);
        $fk_varian = !empty($varian_list[$i]) ? (int)$varian_list[$i] : 'NULL';

        $query = "INSERT INTO bahan_baku (fk_menu_bahan, nama_bahan, jumlah_default, satuan, fk_bahan_stok, fk_varian_bahan) 
              VALUES ('$fk_menu', '$nama_bahan', '$jumlah', '$satuan', '$id_stok', $fk_varian)";
        mysqli_query($conn, $query);
        $berhasil += mysqli_affected_rows($conn);
    }

    return $berhasil;
}

function editBahan($data)
{
    global $conn;
    $id_bahan = $data["id_bahan"];
    $nama_bahan = htmlspecialchars($data['nama_bahan']);
    $jumlah_default = parseFraksi($data['jumlah_default']);
    $satuan = htmlspecialchars($data['satuan']);
    $fk_varian_bahan = !empty($data['fk_varian_bahan']) ? (int)$data['fk_varian_bahan'] : 'NULL';

    $query = "UPDATE bahan_baku SET
                nama_bahan = '$nama_bahan',
                jumlah_default = '$jumlah_default',
                satuan = '$satuan',
                fk_varian_bahan = $fk_varian_bahan
              WHERE id_bahan = $id_bahan";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function hapusBahan($id_bahan)
{
    global $conn;
    $id_bahan_list = $_GET['id_bahan']; // "60,61"
    $ids = implode(',', array_map('intval', explode(',', $id_bahan_list)));
    mysqli_query($conn, "DELETE FROM bahan_baku WHERE id_bahan IN ($ids)");
    return mysqli_affected_rows($conn);
}

function harga_menu($fk_menu, $fk_pesanan_varian)
{
    global $conn;

    //ambil harga menu dasar
    $queryMenu = "SELECT harga_menu FROM menu WHERE id_menu = $fk_menu";
    $resultMenu = mysqli_query($conn, $queryMenu);
    $menu = mysqli_fetch_assoc($resultMenu);
    $harga_dasar = $menu['harga_menu'] ?? 0;

    //ambil harga varian
    $queryVarian = "SELECT harga_varian FROM menu_varian WHERE id_varian = $fk_pesanan_varian";
    $resultVarian = mysqli_query($conn, $queryVarian);
    $varian = mysqli_fetch_assoc($resultVarian);
    $harga_varian = $varian['harga_varian'] ?? 0;

    // total harga menu dengan varian
    $harga_menu = $harga_dasar + $harga_varian;

    return $harga_menu;
}

function tambahPesanan($data)
{
    global $conn;

    $fk_customer = (int)$data['fk_customer'];
    $fk_pesanan_varian = isset($data['fk_pesanan_varian']) && $data['fk_pesanan_varian'] !== null
        ? (int)$data['fk_pesanan_varian']
        : null;
    $fk_varian_value = is_null($fk_pesanan_varian) ? "NULL" : "'$fk_pesanan_varian'";
    $jumlah = (int)$data['jumlah'];
    $catatan = mysqli_real_escape_string($conn, $data['catatan']);
    $tanggal_antar = mysqli_real_escape_string($conn, $data['tanggal_antar']);
    $metode = mysqli_real_escape_string($conn, $data['metode']);

    // Hitung harga total
    $fk_menu = (int)$data['fk_menu'];
    $harga_satuan = harga_menu($fk_menu, $fk_pesanan_varian);
    $harga_total = $harga_satuan * $jumlah;

    // Tentukan status
    $status = ($metode == 'Kurir Catering') ? 'Diterima' : 'Selesai';

    // Query yang benar
    $query = "INSERT INTO pesanan (
        jumlah, 
        harga_total, 
        status_pemesanan, 
        tanggal_pesan, 
        catatan_khusus_pemesanan, 
        metode_pengantaran, 
        tanggal_antar,
        fk_pesanan_varian, 
        fk_pesanan_customer
    ) VALUES (
        '$jumlah',
        '$harga_total',
        '$status',
        NOW(),
        '$catatan',
        '$metode',
        '$tanggal_antar',
        $fk_varian_value,
        '$fk_customer'
    )";

    mysqli_query($conn, $query);

    if (mysqli_error($conn)) {
        echo "Error: " . mysqli_error($conn);
        return 0;
    }

    return mysqli_insert_id($conn);
}

function hapusPesanan($id_pesanan)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = $id_pesanan");
    return mysqli_affected_rows($conn);
}

function simpanDetailPesanan($id_pesanan, $bahan_dipilih, $packing, $jumlah_pesanan, $bahan_tambahan_dipilih = [], $bahan_tambahan_session = [])
{
    global $conn;

    // Loop bahan default
    foreach ($bahan_dipilih as $id_bahan) {
        $result = mysqli_query($conn, "SELECT * FROM bahan_baku WHERE id_bahan = $id_bahan");
        $bahan = mysqli_fetch_assoc($result);

        if ($bahan) {
            $jumlah_dipakai = $bahan['jumlah_default'] * $jumlah_pesanan;

            $queryStok = "SELECT id_stok FROM stok_bahan WHERE nama_bahan_stok = '{$bahan['nama_bahan']}' LIMIT 1";
            $resultStok = mysqli_query($conn, $queryStok);
            $stok = mysqli_fetch_assoc($resultStok);

            if (!$stok) continue;

            $id_stok = $stok['id_stok'];

            mysqli_query($conn, "INSERT INTO detail_pesanan_bahan (
                fk_detail_pesanan,
                fk_stok_detail,
                fk_bahan_detail,
                jumlah_dipakai,
                packing
            ) VALUES (
                '$id_pesanan',
                '$id_stok',
                '$id_bahan',
                '$jumlah_dipakai',
                '$packing'
            )");
        }
    }

    // Loop bahan tambahan dari session
    foreach ($bahan_tambahan_dipilih as $i) {
        if (!isset($bahan_tambahan_session[$i])) continue;
        $bt = $bahan_tambahan_session[$i];
        $nama = mysqli_real_escape_string($conn, $bt['nama_bahan']);
        $jumlah_dipakai = $bt['jumlah'] * $jumlah_pesanan;

        $cek = mysqli_query($conn, "SELECT id_stok FROM stok_bahan WHERE nama_bahan_stok = '$nama' LIMIT 1");
        $stok = mysqli_fetch_assoc($cek);
        if (!$stok) continue;

        $id_stok = $stok['id_stok'];
        mysqli_query($conn, "INSERT INTO detail_pesanan_bahan (
            fk_detail_pesanan,
            fk_stok_detail,
            fk_bahan_detail,
            jumlah_dipakai,
            packing
        ) VALUES (
            '$id_pesanan',
            '$id_stok',
            NULL,
            '$jumlah_dipakai',
            '$packing'
        )");
    }

    return mysqli_affected_rows($conn);
}

function editPesanan($data)
{
    global $conn;
    $id_pesanan = (int)$data['id_pesanan'];
    $fk_customer = (int)$data['fk_customer'];
    $fk_pesanan_varian = isset($data['fk_pesanan_varian']) && $data['fk_pesanan_varian'] !== null
        ? (int)$data['fk_pesanan_varian']
        : null;
    $jumlah = (int)$data['jumlah'];
    $catatan = mysqli_real_escape_string($conn, $data['catatan_khusus_pemesanan']);
    $tanggal_antar = mysqli_real_escape_string($conn, $data['tanggal_antar']);
    $metode = mysqli_real_escape_string($conn, $data['metode_pengantaran']);
    $status = mysqli_real_escape_string($conn, $data['status_pemesanan']);

    // Ambil tanggal_pesan yang lama agar tidak berubah
    $result = mysqli_query($conn, "SELECT tanggal_pesan FROM pesanan WHERE id_pesanan = $id_pesanan");
    $row = mysqli_fetch_assoc($result);
    $tanggal_pesan_lama = $row['tanggal_pesan'];

    $fk_menu = (int)$data['fk_menu'];
    $harga_satuan = harga_menu($fk_menu, $fk_pesanan_varian);
    $harga_total = $harga_satuan * $jumlah;

    $query = "UPDATE pesanan SET
                fk_pesanan_customer = '$fk_customer',
                fk_pesanan_varian = '$fk_pesanan_varian',
                jumlah = '$jumlah',
                harga_total = '$harga_total',
                catatan_khusus_pemesanan = '$catatan',
                tanggal_antar = '$tanggal_antar',
                metode_pengantaran = '$metode',
                status_pemesanan = '$status',
                 tanggal_pesan = '$tanggal_pesan_lama'
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

    // Cek apakah upload gambar baru
    if (isset($_FILES['profil_foto']) && $_FILES['profil_foto']['error'] !== 4) {
        $profil_foto = upload_profil();
        if (!$profil_foto) {
            return false;
        }

        // Hapus gambar lama
        if (!empty($data["gambarLama"])) {
            $pathGambarLama = '../img/' . basename($data["gambarLama"]);
            if (file_exists($pathGambarLama)) {
                unlink($pathGambarLama);
            }
        }
    } else {
        $profil_foto = $data["gambarLama"] ?? '';
    }

    $query = "UPDATE customer SET
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

function inputStokBahan($data)
{
    global $conn;

    $nama_bahan_stok = htmlspecialchars($data["nama_bahan_stok"]);

    // Konversi jumlah_stok (ganti koma dengan titik, lalu ke float)
    $jumlah_stok_raw = str_replace(',', '.', $data['stok_tersedia']);
    $stok_tersedia = floatval($jumlah_stok_raw);

    $satuan = htmlspecialchars($data['satuan']);

    $query = "INSERT INTO stok_bahan (nama_bahan_stok, stok_tersedia, satuan) 
    VALUES ('$nama_bahan_stok', '$stok_tersedia', '$satuan')";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function hapusStokBahan($id_stok)
{
    global $conn;

    $id_stok = (int)$id_stok;
    mysqli_query($conn, "DELETE FROM stok_bahan WHERE id_stok = $id_stok");
    return mysqli_affected_rows($conn);
}

function editStokBahan($data)
{
    global $conn;
    $id_stok = (int)$data["id_stok"];
    $nama_bahan_stok = htmlspecialchars($data["nama_bahan_stok"]);
    $stok_tersedia = floatval(str_replace(',', '.', $data['stok_tersedia']));
    $satuan = htmlspecialchars($data['satuan']);

    $query = "UPDATE stok_bahan SET
                nama_bahan_stok = '$nama_bahan_stok',
                stok_tersedia = '$stok_tersedia',
                satuan = '$satuan'
              WHERE id_stok = $id_stok";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}
