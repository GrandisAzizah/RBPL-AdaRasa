<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

$id_pesanan = (int)$_GET['id_pesanan'];
$pesanan = query("SELECT p.*, mv.fk_menu_varian as fk_menu 
    FROM pesanan p 
    JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
    WHERE p.id_pesanan = $id_pesanan")[0];

$id_menu = $pesanan['fk_menu'];
$fk_varian = $pesanan['fk_pesanan_varian'];
$jumlah = $pesanan['jumlah'];
$bahan = query("SELECT * FROM bahan_baku 
    WHERE fk_menu_bahan = $id_menu 
    AND (fk_varian_bahan = $fk_varian OR fk_varian_bahan IS NULL)");

$pelanggan = query("SELECT * FROM customer");
$menu = query("SELECT * FROM menu");
$varian = query("SELECT * FROM menu_varian");

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
    <title>Pesanan & Pelanggan</title>
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
            <a href="pesanan.php" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title-input" style="margin: 0;">INPUT PEMESANAN</h5>
        </div>

        <div class="container">
            <form id="form-pesanan" method="POST">
                <!-- INPUT NAMA MENU -->
                <label for="nama_pelanggan">Nama Pelanggan</label>
                <select name="nama_pelanggan" required>
                    <?php foreach ($pelanggan as $p): ?>
                        <option value="<?= $p['id_pelanggan'] ?>" <?= $p['id_pelanggan'] == $pesanan['fk_pesanan_customer'] ? 'selected' : '' ?>>
                            <?= $p['nama_pelanggan'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <!-- INPUT PESANAN -->
                <label for="nama_pesanan">Pesanan</label>
                <select name="nama_pesanan" id="nama_pesanan" class="select2" style="width: 100%;" required>
                    <?php
                    $menu = query("SELECT * FROM menu ORDER BY nama_menu ASC");
                    foreach ($menu as $row):
                    ?>
                        <option value="<?= $row['id_menu']; ?>">
                            <?= $row['nama_menu']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- INPUT JUMLAH -->
                <label for="jumlah">Jumlah:<br></label>
                <input type="text" name="jumlah" value="<?= $jumlah ?>" id="jumlah" min="0" max="100" step="1" required
                    oninput="this.value = Math.floor(this.value); if(this.value < 0) this.value = 0; if(this.value > 500) this.value = 500;">

                <div class="packing-takaran-item">
                    <label for="takaran">Takaran</label>
                    <select name="takaran" id="takaran">
                        <?php
                        $varian = query("SELECT * FROM menu_varian ORDER BY id_varian ASC");
                        foreach ($varian as $row):
                        ?>
                            <option value="<?= $row['id_varian']; ?>">
                                <?= $row['takaran']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="packing-takaran-group">
                    <div class="packing-takaran-item">
                        <label for="packing">Packing</label>
                        <select name="packing" id="packing">
                            <option value="Box">Box</option>
                            <option value="Mika">Mika</option>
                            <option value="Kertas Minyak">Kertas Minyak</option>
                            <option value="Plastik">Plastik</option>
                            <option value="Toples Kotak">Toples Kotak</option>
                            <option value="Toples Lingkaran">Toples Lingkaran</option>
                            <option value="Toples Hati">Toples Hati</option>
                            <option value="Toples Tabung">Toples Tabung</option>
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

                <label>Tanggal Pemesanan</label>
                <div class="card card-tanggal-pesan">
                    <p id="jam-sekarang" style="padding: 5px; font-size: 13px; color: #000000;"></p>
                </div>

                <label for="metode_pengantaran">Metode Pengantaran</label>
                <select name="metode_pengantaran" id="metode_pengantaran">
                    <option value="Kurir Catering">Kurir Catering</option>
                    <option value="Ojek Online">Ojek Online</option>
                </select><br>

                <label>Tanggal Pengiriman</label>
                <input type="datetime-local" name="tanggal_antar" id="tanggal_antar" step="60">
                <label for="catatan_khusus_pemesanan" class="form-label">Catatan Khusus Pemesanan</label>
                <textarea type="text" name="catatan_khusus_pemesanan" id="catatan_khusus_pemesanan" maxlength="255"></textarea>
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