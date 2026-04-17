<?php

session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';
$bahan = query("SELECT * FROM bahan_baku WHERE fk_bahan_menu = $_GET[id_menu] ORDER BY nama_bahan ASC");
$id_menu = $_GET['id_menu'];
$menu = query("SELECT nama_menu FROM menu WHERE id_menu = $id_menu");
$nama_menu = $menu[0]['nama_menu'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Bahan Baku</title>
    <link rel="stylesheet" href="menu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container-main">
        <div class="header-nav mt-3">
            <a href="menu.php" class="header-nav-left">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title" style="margin: 0;">Bahan <?= $nama_menu ?></h5>
            <a href="inputbahanBaku.php?id_menu=<?= $id_menu ?>" class="header-nav-right">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.91669 18.9998H30.0834M19 7.9165V30.0832" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>


        <?php if (count($bahan) == 0) : ?>
            <p class="text-center mt-5" style="color: #979696; margin-top: 20px; height: 70vh; display: flex; align-items: center; justify-content: center;"">Belum ada bahan yang ditambahkan</p>
        <?php else : ?>
            <?php foreach ($bahan as $row) : ?>
                <div class=" container-menu mt-4">
            <div class="card">
                <div class="row g-0">
                    <!-- Isi -->
                    <div class="col">
                        <div class="card-body">
                            <h5 class="card-title"><?= $row['nama_bahan'] ?></h5>
                            <p class="card-text"><?= $row['jumlah_default'] . ' ' . $row['satuan'] ?></p>
                        </div>
                    </div>
                    <!-- Tombol Edit dan Hapus -->
                    <div class="col-auto menu-btn d-flex align-items-center gap-2 p-2 align-self-end">
                        <a href="editBahan.php?id_bahan=<?= $row['id_bahan'] ?>" class="edit-btn btn btn-dark btn-sm">Edit</a>
                        <a href="#" class="delete-btn btn btn-danger btn-sm" onclick="setHapusUrl('hapusBahan.php?id_bahan=<?= $row['id_bahan'] ?>&id_menu=<?= $id_menu ?>')">Hapus</a>
                    </div>
                </div>
            </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <p>Yakin ingin menghapus bahan ini?</p>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button class="btn btn-dark" data-bs-dismiss="modal" style="width: auto !important; height: auto !important; padding: 6px 20px !important;">Tidak</button>
                    <a id="btnYaHapus" href="#" class="btn btn-danger" style="width: auto !important; height: auto !important; padding: 6px 20px !important;">Ya</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setHapusUrl(url) {
        document.getElementById('btnYaHapus').href = url;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    }
</script>
</body>

</html>