<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Analisis Laporan</title>
    <link rel="stylesheet" href="../pesanan.css">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Aleo', serif;
        background-color: #ffffff;
        margin: 0;
        padding: 20px;
    }

    h5 {
        font-weight: 590;
        margin-top: 20px;
        font-size: 20px;
    }

    h6 {
        margin-top: 20px;
        font-size: 18px;
        justify-content: center;
        display: flex;
    }

    p {
        font-weight: 400;
        margin-left: 10px;
        margin-top: 10px;
        font-size: 16px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 5px;
    }

    .menu-item .icon {
        flex-shrink: 0;
        width: 24px;
    }

    .menu-item .label {
        flex: 1;
        margin-left: 8px;
    }

    .menu-item .arrow-icon {
        flex-shrink: 0;
    }

    a {
        text-decoration: none;
        color: black;
    }

    .grid {
        outline: 1px solid #000;
    }

    .periode-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-auto-rows: 150px;
        gap: 16px;
        padding: 16px;
    }

    .periode-card {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 16px;
        border: 1px solid #000000;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        justify-content: center;
        align-items: center;
    }

    .periode-card:hover {
        background-color: #f5f5f5;
        transform: translateX(4px);
    }

    .periode-text {
        font-size: 16px;
        font-weight: 500;
        font-family: 'Aleo', serif;
    }

    .periode-card:nth-child(2) {
        grid-row: span 2;
    }

    .periode-card:nth-child(3) {
        grid-row: span 2;
    }
</style>

<body>
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3 mt-3">
            <a href="laporan.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">LAPORAN</h5>
        </div>
        <h6 style="margin:0;"> Pilih Periode Laporan</h6>


        <div class="periode-grid">
            <div class="periode-card">
                <a href="detailAnalisis.php?periode=1hari" style="text-decoration: none; color: black;">
                    <div class="periode-text">1 Hari</div>
                </a>
            </div>
            <div class="periode-card">
                <a href="detailAnalisis.php?periode=1minggu" style="text-decoration: none; color: black;">
                    <div class="periode-text">1 Minggu</div>
                </a>
            </div>
            <div class="periode-card">
                <a href="detailAnalisis.php?periode=1bulan" style="text-decoration: none; color: black;">
                    <div class="periode-text">1 Bulan</div>
                </a>
            </div>
            <div class="periode-card">
                <a href="detailAnalisis.php?periode=1tahun" style="text-decoration: none; color: black;">
                    <div class="periode-text">1 Tahun</div>
                </a>
            </div>
        </div>
    </div>
</body>

</html>