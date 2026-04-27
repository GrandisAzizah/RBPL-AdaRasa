<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require '../functions.php';

// $bahan_perlu_beli = query("SELECT * FROM (
//     SELECT 
//         bb.nama_bahan,
//         bb.satuan,
//         SUM(dpb.jumlah_dipakai) as total_butuh,
//         COALESCE(sb.stok_tersedia, 0) as stok_tersedia,
//         (SUM(dpb.jumlah_dipakai) - COALESCE(sb.stok_tersedia, 0)) as perlu_beli,
//         m.nama_menu,
//         c.nama_pelanggan
//     FROM detail_pesanan_bahan dpb
//     JOIN bahan_baku bb ON dpb.fk_bahan_detail = bb.id_bahan
//     JOIN pesanan p ON dpb.fk_detail_pesanan = p.id_pesanan
//     JOIN menu_varian mv ON p.fk_pesanan_varian = mv.id_varian
//     JOIN menu m ON mv.fk_menu_varian = m.id_menu
//     JOIN customer c ON p.fk_pesanan_customer = c.id_pelanggan
//     LEFT JOIN stok_bahan sb ON sb.nama_bahan_stok = bb.nama_bahan
//     GROUP BY bb.id_bahan, bb.nama_bahan, bb.satuan, m.nama_menu, c.nama_pelanggan, sb.stok_tersedia
//     ORDER BY bb.nama_bahan
// ) as subquery
// WHERE total_butuh > stok_tersedia");

$bahan_perlu_beli = query("
    SELECT *
    FROM (
    SELECT 
            bb.id_bahan,
            bb.nama_bahan,
            bb.satuan,
            SUM(dpb.jumlah_dipakai)            AS total_butuh,
            COALESCE(sb.stok_tersedia, 0)      AS stok_tersedia,
            (SUM(dpb.jumlah_dipakai) - COALESCE(sb.stok_tersedia,0)) AS perlu_beli,
            m.nama_menu,
            c.nama_pelanggan
        FROM detail_pesanan_bahan dpb
        JOIN bahan_baku bb   ON dpb.fk_bahan_detail = bb.id_bahan
        JOIN pesanan p       ON dpb.fk_detail_pesanan = p.id_pesanan
        JOIN menu_varian mv  ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m          ON mv.fk_menu_varian = m.id_menu
        JOIN customer c     ON p.fk_pesanan_customer = c.id_pelanggan
        LEFT JOIN stok_bahan sb ON sb.nama_bahan_stok = bb.nama_bahan
        GROUP BY bb.id_bahan, bb.nama_bahan, bb.satuan,
                 m.nama_menu, c.nama_pelanggan, sb.stok_tersedia
    ) AS sub
    WHERE total_butuh > stok_tersedia
    ORDER BY nama_bahan ASC
");

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'asc';
$next_sort = ($sort == 'asc') ? 'desc' : 'asc';

$bahan_perlu_beli = query("
    SELECT *
    FROM (
    SELECT 
            bb.id_bahan,
            bb.nama_bahan,
            bb.satuan,
            SUM(dpb.jumlah_dipakai)            AS total_butuh,
            COALESCE(sb.stok_tersedia, 0)      AS stok_tersedia,
            (SUM(dpb.jumlah_dipakai) - COALESCE(sb.stok_tersedia,0)) AS perlu_beli,
            m.nama_menu,
            c.nama_pelanggan
        FROM detail_pesanan_bahan dpb
        JOIN bahan_baku bb   ON dpb.fk_bahan_detail = bb.id_bahan
        JOIN pesanan p       ON dpb.fk_detail_pesanan = p.id_pesanan
        JOIN menu_varian mv  ON p.fk_pesanan_varian = mv.id_varian
        JOIN menu m          ON mv.fk_menu_varian = m.id_menu
        JOIN customer c     ON p.fk_pesanan_customer = c.id_pelanggan
        LEFT JOIN stok_bahan sb ON sb.nama_bahan_stok = bb.nama_bahan
        GROUP BY bb.id_bahan, bb.nama_bahan, bb.satuan,
                 m.nama_menu, c.nama_pelanggan, sb.stok_tersedia
    ) AS sub
    WHERE total_butuh > stok_tersedia
    ORDER BY nama_bahan $sort
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Bahan Baku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pesananAdmin.css">
</head>

<style>
    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        margin: 0 !important;
        margin-right: 20px !important;
        border-color: #6750A4 !important;
    }

    input[type="checkbox"]:checked {
        background-color: #6750A4;
        border: 1px solid #6750A4 !important;
        box-shadow: none !important;
    }

    input[type="checkbox"]:focus {
        outline: #6750A4 !important;
        box-shadow: none !important;
        border-color: #6750A4 !important;
    }
</style>

<body>
    <div class="container-main">
        <div class="header-nav mt-3">
            <a href="berandaAdmin.php" class="header-nav-left">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 class="header-nav-title" style="margin: 0;">Stok Bahan Baku</h5>
            <a href="inputStokBahan.php" class="header-nav-right">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.91669 18.9998H30.0834M19 7.9165V30.0832" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>

        <!-- tombol navigasi pelanggan dan pesanan -->
        <div class="btn-nav card row g-0">
            <div class="sort col-auto">
                <a href="?sort=<?= $next_sort ?>" style="color: #4A4459; text-decoration: none; font-weight: 600;"><?= ($sort == 'asc') ? '' : '' ?>
                    <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33333 9.16667V3.1875L1.1875 5.33333L0 4.16667L4.16667 0L8.33333 4.16667L7.14583 5.33333L5 3.1875V9.16667H3.33333ZM9.16667 16.6667L5 12.5L6.1875 11.3333L8.33333 13.4792V7.5H10V13.4792L12.1458 11.3333L13.3333 12.5L9.16667 16.6667Z" fill="black" />
                    </svg>
                    <span>Urutkan</span>
                </a>
            </div>
        </div>
        <div class="btn-group-bahan" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="btnradio1">
                <svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.275 9.01875L0 4.74375L1.06875 3.675L4.275 6.88125L11.1563 0L12.225 1.06875L4.275 9.01875Z" fill="#4A4459" />
                </svg>
                <a href="">List Belanja Bahan</a>
            </label>
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio2"><a href="listBahanTersedia.php">List Bahan Tersedia</a></label>
        </div>

        <?php if (empty($bahan_perlu_beli)): ?>
            <p class="text-center mt-5" style="color: #979696; margin-top: 20px; height: 70vh; display: flex; align-items: center; justify-content: center;">Belum ada data stok bahan baku yang perlu dibeli</p>
        <?php else: ?>
            <?php foreach ($bahan_perlu_beli as $b): ?>
                <div class="card-bahan">
                    <div class="row g-0">
                        <!-- Isi -->
                        <div class="col">
                            <div class="card-body-bahan">
                                <h5 class="card-title-bahan"><?= $b['nama_bahan'] ?></h5>
                                <p class="card-text-bahan">Perlu Beli: <?= $b['perlu_beli'] . ' ' . $b['satuan'] ?></p>
                                <p class="card-text-bahan text-muted" style="font-size: 10px;">
                                    Untuk pesanan: <?= $b['nama_menu'] ?> - <?= $b['nama_pelanggan'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <input
                                type="checkbox"
                                class="form-check-input checkbox-bahan"
                                data-id="<?= $b['nama_bahan'] ?>"
                                data-jumlah="<?= $b['perlu_beli'] ?>">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <script>
            function getCardElement(checkbox) {
                return checkbox.closest('.card-bahan');
            }

            document.querySelectorAll('.checkbox-bahan').forEach(cb => {
                cb.addEventListener('change', function() {
                    const nama = this.dataset.id;
                    const jumlah = this.dataset.jumlah;
                    const checked = this.checked ? 1 : 0;
                    this.disabled = true;

                    fetch('updateStok.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                nama,
                                jumlah,
                                checked
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const card = getCardElement(this);
                                if (card) {
                                    card.style.transition = 'opacity .3s, height .3s, margin .3s';
                                    card.style.opacity = '0';
                                    card.style.height = '0';
                                    card.style.margin = '0';
                                    setTimeout(() => card.remove(), 300);
                                }
                            } else {
                                alert(data.message || 'Gagal memperbarui stok');
                                this.checked = !this.checked;
                                this.disabled = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi error jaringan');
                            this.checked = !this.checked;
                            this.disabled = false;
                        });
                });
            });
        </script>
</body>

</html>