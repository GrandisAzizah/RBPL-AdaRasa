<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$id_pesanan = (int)$_GET['id_pesanan'];
$pelanggan = query("SELECT * FROM customer");
$menu = query("SELECT * FROM menu");
$varian = query("SELECT * FROM menu_varian");
$pesanan = query("SELECT p.*, dp.packing, mv.fk_menu_varian as fk_menu 
    FROM pesanan p 
    JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
    LEFT JOIN detail_pesanan_bahan dp ON p.id_pesanan = dp.fk_detail_pesanan
    WHERE p.id_pesanan = $id_pesanan")[0];

$daftar_pelanggan = query("SELECT * FROM customer ORDER BY nama_pelanggan");
$daftar_menu = query("SELECT * FROM menu ORDER BY nama_menu");
$daftar_varian = query("SELECT * FROM menu_varian ORDER BY takaran");
$id_menu = $pesanan['fk_menu'];
$fk_varian = $pesanan['fk_pesanan_varian'];
$jumlah = $pesanan['jumlah'];
if (!empty($fk_varian)) {
    $bahan = query("SELECT * FROM bahan_baku 
        WHERE fk_menu_bahan = $id_menu 
        AND (fk_varian_bahan = $fk_varian OR fk_varian_bahan IS NULL)");
} else {
    $bahan = query("SELECT * FROM bahan_baku 
        WHERE fk_menu_bahan = $id_menu 
        AND fk_varian_bahan IS NULL");
}

$pesan = '';
$tipe = '';

if (isset($_POST["submit"])) {
    // Proses update pesanan
    $hasil = editPesanan($_POST);
    if ($hasil > 0) {
        $pesan = 'Data berhasil diedit!';
        $tipe = 'success';
    } else {
        $pesan = 'Data gagal diedit!';
        $tipe = 'danger';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pesananAdmin.css">
    <!-- CSS Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <!-- JS Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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

    .alert {
        width: 290px;
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
        width: 290px;
    }

    .menu-item .icon {
        flex-shrink: 0;
        width: 24px;
    }

    h5 {
        font-weight: 600;
        margin-top: 20px;
        text-align: center;
    }

    label {
        font-weight: 600;
        margin-top: 5px;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        margin-top: 20px;
    }

    input:focus {
        outline: 1px solid #B3B3B3 !important;
        border-color: #B3B3B3 !important;
        box-shadow: none !important;
    }
</style>

<body>
    <div class="container-main">
        <div class="header-nav-input mt-3">
            <a href="showDetailPesananAdmin.php?id_pesanan=<?= $pesanan['id_pesanan'] ?>" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title-input" style="margin: 0;">EDIT PEMESANAN</h5>
        </div>

        <div class="container">
            <form id="form-pesanan" method="POST">
                <!-- INPUT NAMA MENU -->
                <label for="nama_pelanggan">Nama Pelanggan</label>
                <select name="nama_pelanggan" id="nama_pelanggan" required>
                    <?php foreach ($daftar_pelanggan as $pelanggan): ?>
                        <option value="<?= $pelanggan['id_pelanggan'] ?>" <?= $pesanan['fk_pesanan_customer'] == $pelanggan['id_pelanggan'] ? 'selected' : '' ?>>
                            <?= $pelanggan['nama_pelanggan'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- INPUT PESANAN -->
                <!-- INPUT PESANAN - ganti ini -->
                <label for="nama_pesanan">Pesanan</label>
                <select name="fk_menu_pilih" id="nama_pesanan" required>
                    <?php foreach ($daftar_menu as $m): ?>
                        <option value="<?= $m['id_menu'] ?>" <?= $m['id_menu'] == $id_menu ? 'selected' : '' ?>>
                            <?= $m['nama_menu'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="fk_menu" value="<?= $id_menu ?>">

                <!-- INPUT JUMLAH -->
                <label for="jumlah">Jumlah:<br></label>
                <input type="text" name="jumlah" value="<?= $jumlah ?>" id="jumlah" min="0" max="100" step="1" required
                    oninput="this.value = Math.floor(this.value); if(this.value < 0) this.value = 0; if(this.value > 500) this.value = 500;">

                <label for="takaran">Takaran</label>
                <select name="fk_pesanan_varian" id="takaran" required>
                    <?php foreach ($daftar_varian as $v): ?>
                        <option value="<?= $v['id_varian'] ?>" <?= $v['id_varian'] == $fk_varian ? 'selected' : '' ?>>
                            <?= $v['takaran'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="packing-takaran-group">
                    <div class="packing-takaran-item">
                        <label for="packing">Packing:</label>
                        <select name="packing" id="packing">
                            <option value="Box" <?= $pesanan['packing'] == 'Box' ? 'selected' : '' ?>>Box</option>
                            <option value="Mika" <?= $pesanan['packing'] == 'Mika' ? 'selected' : '' ?>>Mika</option>
                            <option value="Kertas Minyak" <?= $pesanan['packing'] == 'Kertas Minyak' ? 'selected' : '' ?>>Kertas Minyak</option>
                            <option value="Plastik" <?= $pesanan['packing'] == 'Plastik' ? 'selected' : '' ?>>Plastik</option>
                            <option value="Toples Kotak" <?= $pesanan['packing'] == 'Toples Kotak' ? 'selected' : '' ?>>Toples Kotak</option>
                            <option value="Toples Lingkaran" <?= $pesanan['packing'] == 'Toples Lingkaran' ? 'selected' : '' ?>>Toples Lingkaran</option>
                            <option value="Toples Hati" <?= $pesanan['packing'] == 'Toples Hati' ? 'selected' : '' ?>>Toples Hati</option>
                            <option value="Toples Tabung" <?= $pesanan['packing'] == 'Toples Tabung' ? 'selected' : '' ?>>Toples Tabung</option>
                        </select>
                    </div>
                </div>

                <label>Bahan yang Diperlukan</label>
                <?php foreach ($bahan as $i => $b):
                    $jumlah_bahan = $b['jumlah_default'] * $jumlah;
                ?>
                    <div class="bahan-item">
                        <label for="bahan<?= $i ?>" class="bahan-label">
                            <span class="bahan-nama"><?= $b['nama_bahan'] ?></span>
                            <span class="bahan-jumlah" id="jumlah-bahan-<?= $i ?>">
                                <?= $jumlah_bahan ?> <?= $b['satuan'] ?>
                            </span>
                        </label>
                    </div>
                <?php endforeach; ?>

                <label for="metode_pengantaran">Metode Pengantaran</label>
                <select name="metode_pengantaran" id="metode_pengantaran">
                    <option value="Kurir Catering" <?= $pesanan['metode_pengantaran'] == 'Kurir Catering' ? 'selected' : '' ?>>Kurir Catering</option>
                    <option value="Ojek Online" <?= $pesanan['metode_pengantaran'] == 'Ojek Online' ? 'selected' : '' ?>>Ojek Online</option>
                </select><br>
                <label>Tanggal Pengiriman</label>
                <input type="datetime-local" name="tanggal_antar" value="<?= date('Y-m-d\TH:i', strtotime($pesanan['tanggal_antar'])) ?>">

                <label for="catatan_khusus_pemesanan">Catatan</label>
                <textarea name="catatan_khusus_pemesanan" maxlength="255"><?= $pesanan['catatan_khusus_pemesanan'] ?></textarea>
        </div>
        <!-- SUBMIT BUTTON -->
        <button type="button" onclick="validasiDanNext()" class="btn btn-outline-dark input-next">Next</button>
        </form>
    </div>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tomSelect = new TomSelect('#nama_pesanan', {
            create: false,
            sortField: 'text',
            searchField: 'text',
            placeholder: 'Pilih menu...',
            allowEmptyOption: true,
            onChange: function(value) {
                updateTakaran(value);
            },
            render: {
                option: function(data, escape) {
                    if (data.disabled) return null;
                    return '<div>' + escape(data.text) + '</div>';
                },
                item: function(data, escape) {
                    if (data.disabled) return null;
                    return '<div>' + escape(data.text) + '</div>';
                }
            }
        });

        setTimeout(function() {
            tomSelect.clear();
            tomSelect.setValue('');
        }, 10);
    });

    function updateJam() {
        const now = new Date();
        const dd = String(now.getDate()).padStart(2, '0');
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const yyyy = now.getFullYear();
        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('jam-sekarang').textContent = `${dd}/${mm}/${yyyy} ${hh}:${min}`;
    }

    updateJam();
    setInterval(updateJam, 1000);

    document.getElementById('jumlah').addEventListener('keyup', function(e) {
        if (this.value.includes('.')) {
            this.value = Math.floor(this.value);
        }
    });

    function validasiDanNext() {
        const form = document.getElementById('form-pesanan');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        form.action = 'simpanSession.php';
        form.submit();
    }

    // Simpan semua varian dari PHP ke JS
    const semuaVarian = <?= json_encode(
                            query("SELECT * FROM menu_varian ORDER BY id_varian ASC")
                        ) ?>;

    function updateTakaran(id_menu) {
        const select = document.getElementById('takaran');
        select.innerHTML = ''; // kosongkan dulu

        const filtered = semuaVarian.filter(v => v.fk_menu_varian == id_menu);

        if (filtered.length === 0) {
            select.innerHTML = '<option value="">Tidak ada takaran</option>';
            return;
        }

        filtered.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v.id_varian;
            opt.textContent = v.takaran;
            select.appendChild(opt);
        });
    }

    // Jalankan saat halaman load dan saat menu berubah
    document.getElementById('nama_pesanan').addEventListener('change', function() {
        updateTakaran(this.value);
    });

    // Trigger saat pertama load
    updateTakaran(document.getElementById('nama_pesanan').value);
</script>