<?php
// ini dibuat biar nanti di setiap menu gak perlu buat koneksi lagi dan tinggal panggil saja function nya
// koneksi ke database ("namahost", "username", "password", "nama_database")
// $conn = mysqli_connect(
//     "sql110.infinityfree.com",
//     "if0_41989402",
//     "O91jQP0TwP",
//     "if0_41989402_adarasa"
// );
require_once __DIR__ . '/config.php';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
$GLOBALS['conn'] = $conn;

// $conn = mysqli_connect(
//     "localhost",
//     "root",
//     "",
//     "adarasa"
// );

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
    $email = htmlspecialchars(stripslashes($data["email"]));

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
    mysqli_query($conn, "INSERT INTO user (username, password, email, role) VALUES ('$username', '$password', '$email', 'user')");

    return mysqli_affected_rows($conn);
}

function ganti_pw($username, $password_lama, $password_baru)
{
    global $conn;

    $result = mysqli_query($conn, "SELECT password FROM user WHERE username = '$username'");
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        return false;
    }

    if (password_verify($password_lama, $row['password'])) {
        $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE user SET password = '$hash_baru' WHERE username = '$username'");
        return true;
    } else {
        return 'wrong';
    }
}

function ganti_usn($username_lama, $username_baru)
{
    global $conn;

    // Cek apakah username baru sudah dipakai
    $cek = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username_baru'");
    if (mysqli_num_rows($cek) > 0) {
        return 'exists'; // Username sudah ada
    }

    // Update username
    $query = "UPDATE user SET username = '$username_baru' WHERE username = '$username_lama'";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        return true;
    }
    return false;
}

// untuk menu
function tambahMenu($data)
{
    global $conn;

    $nama_menu = htmlspecialchars($data["nama-menu"]);
    // Konversi harga menu (bersihin dari non-angka)
    $harga_menu_clean = preg_replace('/[^0-9]/', '', $data["harga-menu"]);
    $harga_menu = intval($harga_menu_clean);
    $kategori = htmlspecialchars($data["kategori"]);

    $gambar_menu = upload();
    if (!$gambar_menu) {
        return false;
    }

    $query = "INSERT INTO menu (nama_menu, harga_menu, gambar_menu, kategori) 
              VALUES ('$nama_menu', '$harga_menu', '$gambar_menu', '$kategori')";
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
    if ($error == 4) {
        echo "<script>alert('Silakan unggah gambar terlebih dahulu.');</script>";
        return false;
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
    move_uploaded_file($tmpName, __DIR__ . '/img/' . $namaFileBaru);

    return $namaFileBaru;
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
    $kategori = htmlspecialchars($data["kategori"]);
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
    if (isset($_FILES['gambar-menu']) && $_FILES['gambar-menu']['error'] === UPLOAD_ERR_OK) {
        $gambar_baru = upload();
        if (is_string($gambar_baru) && $gambar_baru !== '') {
            $gambar_menu = $gambar_baru;
            $uploadGambarBaru = true;
        } else {
            return false;
        }
    }

    if ($uploadGambarBaru && mysqli_affected_rows($conn) >= 0) {
        $pathGambarLama = __DIR__ . '/img/' . basename($data["gambarLama"]);
        if (file_exists($pathGambarLama)) {
            unlink($pathGambarLama);
        }
    }

    // Update menu
    $query = "UPDATE menu SET
                nama_menu = '$nama_menu',
                harga_menu = '$harga',
                gambar_menu = '$gambar_menu',
                kategori = '$kategori'
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
    $id_bahan_utama = (int)$data["id_bahan"]; // Ini id_bahan yang dilempar dari form
    $nama_bahan = htmlspecialchars($data['nama_bahan']);
    $satuan = htmlspecialchars($data['satuan']);

    // Ambil data menu dari id_bahan utama (untuk dapat fk_menu_bahan)
    $q_utama = mysqli_query($conn, "SELECT fk_menu_bahan FROM bahan_baku WHERE id_bahan = $id_bahan_utama");
    $menu = mysqli_fetch_assoc($q_utama);
    $fk_menu = $menu['fk_menu_bahan'];

    $jumlah_list = $data['jumlah_default'];
    $varian_list = $data['fk_varian_bahan'];
    $berhasil = 0;

    // Looping setiap baris inputan
    foreach ($jumlah_list as $i => $jumlah_raw) {
        $jumlah = floatval($jumlah_raw); // Input number pakai titik (desimal)
        $fk_varian = !empty($varian_list[$i]) ? (int)$varian_list[$i] : 'NULL';

        // Cari id_bahan yang sesuai dengan varian ini
        if ($fk_varian == 'NULL') {
            // Kalau tidak ada varian, update baris utama
            $cek = mysqli_query($conn, "SELECT id_bahan FROM bahan_baku WHERE id_bahan = $id_bahan_utama");
        } else {
            // Kalau ada varian, cari id_bahan yang menampung varian tersebut di menu ini
            $cek = mysqli_query($conn, "SELECT id_bahan FROM bahan_baku WHERE fk_menu_bahan = $fk_menu AND nama_bahan = '$nama_bahan' AND fk_varian_bahan = $fk_varian");
        }

        $row = mysqli_fetch_assoc($cek);

        // Kalau datanya sudah ada, UPDATE
        if ($row) {
            $id_update = $row['id_bahan'];
            $query = "UPDATE bahan_baku SET
                        nama_bahan = '$nama_bahan',
                        jumlah_default = '$jumlah',
                        satuan = '$satuan'
                      WHERE id_bahan = $id_update";
            mysqli_query($conn, $query);
            $berhasil += mysqli_affected_rows($conn);
        } else if ($fk_varian != 'NULL') {
            // Kalau datanya belum ada (misal varian baru ditambahkan), INSERT
            $query = "INSERT INTO bahan_baku (fk_menu_bahan, nama_bahan, jumlah_default, satuan, fk_varian_bahan) 
                      VALUES ('$fk_menu', '$nama_bahan', '$jumlah', '$satuan', $fk_varian)";
            mysqli_query($conn, $query);
            $berhasil += mysqli_affected_rows($conn);
        }
    }

    return $berhasil;
}

// function editBahan($data)
// {
//     global $conn;
//     $id_bahan = $data["id_bahan"];
//     $nama_bahan = htmlspecialchars($data['nama_bahan']);
//     $satuan = htmlspecialchars($data['satuan']);

//     if (isset($data['jumlah_default']) && is_array($data['jumlah_default'])) {
//         $jumlah_default_raw = $data['jumlah_default'][0] ?? '0';
//         $fk_varian_bahan = isset($data['fk_varian_bahan'][0]) ? (int)$data['fk_varian_bahan'][0] : 'NULL';
//     } else {
//         $jumlah_default_raw = $data['jumlah_default'] ?? '0';
//         $fk_varian_bahan = !empty($data['fk_varian_bahan']) ? (int)$data['fk_varian_bahan'] : 'NULL';
//     }

//     $jumlah_default = parseFraksi($jumlah_default_raw);

//     $query = "UPDATE bahan_baku SET
//                 nama_bahan = '$nama_bahan',
//                 jumlah_default = '$jumlah_default',
//                 satuan = '$satuan',
//                 fk_varian_bahan = $fk_varian_bahan
//               WHERE id_bahan = $id_bahan";

//     mysqli_query($conn, $query);
//     return mysqli_affected_rows($conn);
// }

function hapusBahan($id_bahan)
{
    global $conn;
    $id_bahan_list = $_GET['id_bahan']; // "60,61"
    $ids = implode(',', array_map('intval', explode(',', $id_bahan_list)));
    mysqli_query($conn, "DELETE FROM bahan_baku WHERE id_bahan IN ($ids)");
    return mysqli_affected_rows($conn);
}

// function harga_menu($fk_menu, $fk_pesanan_varian)
// {
//     global $conn;

//     //ambil harga menu dasar
//     $queryMenu = "SELECT harga_menu FROM menu WHERE id_menu = $fk_menu";
//     $resultMenu = mysqli_query($conn, $queryMenu);
//     $menu = mysqli_fetch_assoc($resultMenu);
//     $harga_dasar = $menu['harga_menu'] ?? 0;

//     //ambil harga varian
//     $queryVarian = "SELECT harga_varian FROM menu_varian WHERE id_varian = $fk_pesanan_varian";
//     $resultVarian = mysqli_query($conn, $queryVarian);
//     $varian = mysqli_fetch_assoc($resultVarian);
//     $harga_varian = $varian['harga_varian'] ?? 0;

//     // total harga menu dengan varian
//     $harga_menu = $harga_dasar + $harga_varian;

//     return $harga_menu;
// }

function harga_menu($fk_menu, $fk_pesanan_varian)
{
    global $conn;

    // 1. Ambil harga menu dasar
    $queryMenu = "SELECT harga_menu FROM menu WHERE id_menu = $fk_menu";
    $resultMenu = mysqli_query($conn, $queryMenu);
    $menu = mysqli_fetch_assoc($resultMenu);
    $harga_dasar = $menu['harga_menu'] ?? 0;

    // 2. Ambil harga varian (HANYA JIKA varian tidak NULL atau kosong)
    $harga_varian = 0;
    if (!is_null($fk_pesanan_varian) && $fk_pesanan_varian !== '' && $fk_pesanan_varian > 0) {
        $queryVarian = "SELECT harga_varian FROM menu_varian WHERE id_varian = $fk_pesanan_varian";
        $resultVarian = mysqli_query($conn, $queryVarian);
        $varian = mysqli_fetch_assoc($resultVarian);
        $harga_varian = $varian['harga_varian'] ?? 0;
    }

    // 3. Total harga menu dengan varian
    $total_harga = $harga_dasar + $harga_varian;

    return $total_harga;
}

function tambahPesanan($data)
{
    global $conn;

    $fk_customer = (int)$data['fk_customer'];

    // Pastikan $data['fk_menu'] ini dikirim dari inputBahanPesan.php
    $fk_menu = (int)$data['fk_menu'];

    $fk_pesanan_varian = isset($data['fk_pesanan_varian']) && $data['fk_pesanan_varian'] !== null
        ? (int)$data['fk_pesanan_varian']
        : null;

    $fk_varian_value = is_null($fk_pesanan_varian) ? "NULL" : "'$fk_pesanan_varian'";
    $jumlah = (int)$data['jumlah'];
    $catatan = mysqli_real_escape_string($conn, $data['catatan']);
    $tanggal_antar = mysqli_real_escape_string($conn, $data['tanggal_antar']);
    $metode = mysqli_real_escape_string($conn, $data['metode']);

    // Hitung harga total
    $harga_satuan = harga_menu($fk_menu, $fk_pesanan_varian);
    $harga_total = $harga_satuan * $jumlah;

    // Tentukan status
    $status = 'Diterima';

    // QUERY INSERT (9 kolom, 9 nilai)
    $query = "INSERT INTO pesanan (
        jumlah, 
        harga_total, 
        status_pemesanan, 
        tanggal_pesan, 
        catatan_khusus_pemesanan, 
        metode_pengantaran, 
        tanggal_antar,
        fk_pesanan_varian, 
        fk_pesanan_customer,
        fk_menu 
    ) VALUES (
        '$jumlah',
        '$harga_total',
        '$status',
        NOW(),
        '$catatan',
        '$metode',
        '$tanggal_antar',
        $fk_varian_value,
        '$fk_customer',
        '$fk_menu'
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

function simpanDetailPesanan(
    $id_pesanan,
    $bahan_dipilih,
    $packing,
    $jumlah_pesanan,
    $bahan_tambahan_idx = [],
    $bahan_tambahan_sess = []
) {
    global $conn;
    if (empty($packing) || $packing == '0') {
        $packing = 'Box';
    }
    $inserted = 0;
    foreach ($bahan_dipilih as $id_bahan) {
        $sql = "SELECT id_bahan, nama_bahan, jumlah_default, satuan
                     FROM bahan_baku
                     WHERE id_bahan = $id_bahan LIMIT 1";
        $res = mysqli_query($conn, $sql);
        $b   = mysqli_fetch_assoc($res);
        if (!$b) {
            continue;
        }
        $jumlah_dipakai = $b['jumlah_default'] * $jumlah_pesanan;

        $sqlStok = "SELECT id_stok
                    FROM stok_bahan
                    WHERE nama_bahan_stok = '{$b['nama_bahan']}' LIMIT 1";
        $resStok = mysqli_query($conn, $sqlStok);
        $stok    = mysqli_fetch_assoc($resStok);
        if (!$stok) {
            continue;
        }

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO detail_pesanan_bahan
                (fk_detail_pesanan, fk_bahan_detail, fk_stok_detail,
                 packing, jumlah_dipakai)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "iiisd",
            $id_pesanan,
            $b['id_bahan'],
            $stok['id_stok'],
            $packing,
            $jumlah_dipakai
        );
        mysqli_stmt_execute($stmt);
        $inserted += mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
    }

    foreach ($bahan_tambahan_idx as $idx) {

        if (!isset($bahan_tambahan_sess[$idx])) {
            continue;
        }
        $bt = $bahan_tambahan_sess[$idx];
        $jumlah_dipakai = $bt['jumlah'] * $jumlah_pesanan;

        $nama = mysqli_real_escape_string($conn, $bt['nama_bahan']);
        $sqlStok = "SELECT id_stok
                    FROM stok_bahan
                    WHERE nama_bahan_stok = '$nama' LIMIT 1";
        $resStok = mysqli_query($conn, $sqlStok);
        $stok    = mysqli_fetch_assoc($resStok);
        if (!$stok) {
            mysqli_query(
                $conn,
                "INSERT INTO stok_bahan (nama_bahan_stok, stok_tersedia, satuan)
                 VALUES ('$nama', 0, '{$bt['satuan']}')"
            );
            $stok['id_stok'] = mysqli_insert_id($conn);
        }

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO detail_pesanan_bahan
                (fk_detail_pesanan, fk_bahan_detail, fk_stok_detail,
                 packing, jumlah_dipakai)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param(
            $stmt,
            "iiisd",
            $id_pesanan,
            $bt['id_bahan'],
            $stok['id_stok'],
            $packing,
            $jumlah_dipakai
        );
        mysqli_stmt_execute($stmt);
        $inserted += mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
    }
    return $inserted;
}

function editPesanan($data)
{
    global $conn;

    $id_pesanan = isset($data['id_pesanan']) ? (int)$data['id_pesanan'] : 0;
    if ($id_pesanan === 0) return -1;

    // Data yang diambil dari form
    $id_pelanggan = (int)$data['nama_pelanggan'];
    $fk_menu = (int)$data['fk_menu_pilih'];
    $jumlah = (int)$data['jumlah']; // Jumlah baru
    $catatan = mysqli_real_escape_string($conn, $data['catatan_khusus_pemesanan']);
    $tanggal_antar = date('Y-m-d H:i:s', strtotime($data['tanggal_antar']));
    $metode = mysqli_real_escape_string($conn, $data['metode_pengantaran']);
    $packing = mysqli_real_escape_string($conn, $data['packing'] ?? '');
    $fk_pesanan_varian = !empty($data['fk_pesanan_varian']) ? (int)$data['fk_pesanan_varian'] : null;

    // 1. Hitung ulang harga total
    $harga_satuan = harga_menu($fk_menu, $fk_pesanan_varian);
    $harga_total = $harga_satuan * $jumlah;

    // Status otomatis
    if ($metode == 'Kurir Catering') {
        $status = 'Diterima';
    } else {
        $status = 'Selesai';
    }

    $varian_value = is_null($fk_pesanan_varian) ? "NULL" : "'$fk_pesanan_varian'";

    // 2. Update tabel pesanan
    $query = "UPDATE pesanan SET
        fk_pesanan_customer = '$id_pelanggan',
        fk_menu = '$fk_menu',
        fk_pesanan_varian = $varian_value,
        jumlah = '$jumlah',
        harga_total = '$harga_total',
        catatan_khusus_pemesanan = '$catatan',
        tanggal_antar = '$tanggal_antar',
        metode_pengantaran = '$metode',
        status_pemesanan = '$status'
        WHERE id_pesanan = $id_pesanan";

    mysqli_query($conn, $query);

    // 3. Update tabel detail_pesanan_bahan (Hitung ulang jumlah_dipakai)
    // Ambil semua bahan yang terikat di pesanan ini
    $detail_lama = query("SELECT * FROM detail_pesanan_bahan WHERE fk_detail_pesanan = $id_pesanan");

    foreach ($detail_lama as $d) {
        // Cari jumlah_default asli dari tabel bahan_baku
        $bahan_asli = query("SELECT jumlah_default FROM bahan_baku WHERE id_bahan = {$d['fk_bahan_detail']}");
        if (!empty($bahan_asli)) {
            $jumlah_default = $bahan_asli[0]['jumlah_default'];

            // Hitung jumlah baru
            $jumlah_baru = $jumlah_default * $jumlah;

            // Update ke tabel detail
            mysqli_query($conn, "UPDATE detail_pesanan_bahan SET 
                jumlah_dipakai = '$jumlah_baru', 
                packing = '$packing' 
                WHERE id_detail = {$d['id_detail']}");
        }
    }

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

    move_uploaded_file($tmpName, __DIR__ . '/img/' . $namaFileBaru);

    return $namaFileBaru;
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

    $koordinat = getCoordinates($alamat);
    $latitude = $koordinat ? $koordinat['lat'] : 'NULL';
    $longitude = $koordinat ? $koordinat['lng'] : 'NULL';

    $query = "INSERT INTO customer (nama_pelanggan, no_hp, alamat, profil_foto, latitude, longitude) 
              VALUES ('$nama_pelanggan', '$no_hp', '$alamat', '$profil_foto', $latitude, $longitude)";

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

    // Ambil koordinat dari alamat baru
    $koordinat = getCoordinates($alamat);
    $latitude = $koordinat ? $koordinat['lat'] : 'NULL';
    $longitude = $koordinat ? $koordinat['lng'] : 'NULL';

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
                profil_foto = '$profil_foto',
                latitude = $latitude,
                longitude = $longitude
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
    $cek = mysqli_query($conn, "SELECT COUNT(*) as total FROM bahan_baku WHERE fk_bahan_stok = $id_stok");
    $row = mysqli_fetch_assoc($cek);

    if ($row['total'] > 0) {
        return -1;
    }

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

function upload_foto_profil()
{
    $namaFile = $_FILES['foto_profil_user']['name'];
    $ukuranFile = $_FILES['foto_profil_user']['size'];
    $error = $_FILES['foto_profil_user']['error'];
    $tmpName = $_FILES['foto_profil_user']['tmp_name'];

    if ($error == 4) {
        return false;
    }

    $ekstensiGambarValid = ['jpg', 'png', 'jpeg', 'webp'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        return false;
    }

    if ($ukuranFile > 5000000) {
        return false;
    }

    // Generate nama file unik
    $namaFileBaru = uniqid() . '.' . $ekstensiGambar;

    // Pindahkan file
    move_uploaded_file($tmpName, __DIR__ . '/img/' . $namaFileBaru);

    // Return path lengkap
    return $namaFileBaru;
}

function editProfil($data, $username_lama)
{
    global $conn;
    $nama_depan = htmlspecialchars($data['nama_depan'] ?? '');
    $nama_belakang = htmlspecialchars($data['nama_belakang'] ?? '');
    $email = htmlspecialchars($data['email'] ?? '');
    $foto_profil_user = $data['gambarLama'] ?? '';

    if (isset($_FILES['foto_profil_user']) && $_FILES['foto_profil_user']['error'] !== 4) {
        $upload_result = upload_foto_profil();

        if ($upload_result && $upload_result !== false) {
            if (!empty($foto_profil_user)) {
                $file_lama = basename($foto_profil_user);
                $path_lama = '../img/' . $file_lama;

                if (file_exists($path_lama)) {
                    unlink($path_lama);
                }
            }
            $foto_profil_user = $upload_result;
        }
    }

    // Query update
    $query = "UPDATE user SET 
              nama_depan = '$nama_depan',
              nama_belakang = '$nama_belakang',
              email = '$email',
              foto_profil_user = '$foto_profil_user'
              WHERE username = '$username_lama'";

    $hasil = mysqli_query($conn, $query);

    if ($hasil) {
        return ['success' => true, 'message' => 'Data berhasil diedit!'];
    } else {
        return ['success' => false, 'message' => 'Data gagal diedit!'];
    }
}

function getCoordinates($address)
{
    $address = urlencode($address);
    $url = "https://nominatim.openstreetmap.org/search?q={$address}&format=json&limit=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AdaRasa-Catering/1.0 (email@gmail.com)');
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data)) {
        return [
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon']
        ];
    }
    return null;
}

function tanggal_indonesia($tanggal)
{
    $hari = date('l', strtotime($tanggal));
    $tanggal_num = date('d', strtotime($tanggal));
    $bulan = date('F', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));

    $hari_indonesia = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];

    $bulan_indonesia = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];

    return $hari_indonesia[$hari] . ', ' . $tanggal_num . ' ' . $bulan_indonesia[$bulan] . ' ' . $tahun;
}

function updateStatusPesanan($id_pesanan, $status_baru)
{
    global $conn;

    $status_valid = ['Diterima', 'Diproses', 'Selesai', 'Diantar'];

    if ($id_pesanan <= 0 || !in_array($status_baru, $status_valid)) {
        return ['success' => false, 'message' => 'Parameter tidak valid'];
    }

    // 1. Update status_pemesanan
    $query = "UPDATE pesanan SET status_pemesanan = '$status_baru' WHERE id_pesanan = $id_pesanan";
    $hasil = mysqli_query($conn, $query);

    if (!$hasil) {
        return ['success' => false, 'message' => 'Gagal update database'];
    }

    // 2. Cek logika status pengantaran
    if ($status_baru == 'Selesai') {
        // Jika Selesai, tentukan status pengantaran berdasarkan metode
        $cek = query("SELECT metode_pengantaran FROM pesanan WHERE id_pesanan = $id_pesanan");
        $metode = $cek[0]['metode_pengantaran'] ?? '';

        if ($metode == 'Kurir Catering') {
            mysqli_query($conn, "UPDATE pesanan SET status_pengantaran = 'Diterima' WHERE id_pesanan = $id_pesanan");
        } else {
            mysqli_query($conn, "UPDATE pesanan SET status_pengantaran = 'Selesai' WHERE id_pesanan = $id_pesanan");
        }
    } else if ($status_baru == 'Diantar') {
        // Jika Diantar, set status pengantaran ke 'Sedang Diantar'
        mysqli_query($conn, "UPDATE pesanan SET status_pengantaran = 'Sedang Diantar' WHERE id_pesanan = $id_pesanan");
    } else {
        // JIKA STATUS DIUBAH KE 'Diterima' ATAU 'Diproses', RESET STATUS PENGANTARAN JADI NULL
        mysqli_query($conn, "UPDATE pesanan SET status_pengantaran = NULL WHERE id_pesanan = $id_pesanan");
    }

    return ['success' => true, 'message' => 'Status berhasil diupdate'];
}

// functions.php - tambahkan function ini
function updateStatusPengantaran($id_pesanan, $status_baru)
{
    global $conn;

    $status_valid = ['Diterima', 'Dalam Proses', 'Sedang Diantar', 'Selesai'];

    if ($id_pesanan <= 0 || !in_array($status_baru, $status_valid)) {
        return ['success' => false, 'message' => 'Invalid parameters'];
    }

    // 1. Update status_pengantaran
    $query = "UPDATE pesanan SET status_pengantaran = '$status_baru' WHERE id_pesanan = $id_pesanan";
    $hasil = mysqli_query($conn, $query);

    if ($hasil) {
        // 2. Jika dikembalikan ke 'Dalam Proses', berarti pesanan kembali ke dapur.
        // Kita harus ubah status_pemesanan menjadi 'Diproses' (atau 'Diterima')
        if ($status_baru == 'Dalam Proses') {
            mysqli_query($conn, "UPDATE pesanan SET status_pemesanan = 'Diproses' WHERE id_pesanan = $id_pesanan");
        }
        // Jika 'Diterima' (di halaman pengantaran maksudnya baru masuk antrean)
        else if ($status_baru == 'Diterima') {
            mysqli_query($conn, "UPDATE pesanan SET status_pemesanan = 'Selesai' WHERE id_pesanan = $id_pesanan");
        }

        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
    }
}

// Untuk testing (tanpa upload gambar)
function tambahMenuTest($data, $skip_upload = false)
{
    global $conn;

    $nama_menu = htmlspecialchars($data["nama-menu"]);
    $harga_menu_clean = preg_replace('/[^0-9]/', '', $data["harga-menu"]);
    $harga_menu = intval($harga_menu_clean);
    $kategori = htmlspecialchars($data["kategori"]);

    // Skip upload untuk testing
    if ($skip_upload) {
        $gambar_menu = 'test.jpg';
    } else {
        $gambar_menu = upload();
        if (!$gambar_menu) {
            return false;
        }
    }

    $query = "INSERT INTO menu (nama_menu, harga_menu, gambar_menu, kategori) 
              VALUES ('$nama_menu', '$harga_menu', '$gambar_menu', '$kategori')";
    mysqli_query($conn, $query);
    $id_menu = mysqli_insert_id($conn);

    // Insert varian
    if (isset($data['takaran']) && is_array($data['takaran'])) {
        for ($i = 0; $i < count($data['takaran']); $i++) {
            $takaran = trim($data['takaran'][$i]);
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

function hapusBahanTest($id_bahan)
{
    global $conn;

    // Single id
    $ids = (int)$id_bahan;

    mysqli_query($conn, "DELETE FROM bahan_baku WHERE id_bahan IN ($ids)");
    return mysqli_affected_rows($conn);
}
