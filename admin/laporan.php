<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Laporan</title>
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
        font-size: 18px;
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
</style>

<body>
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3 mt-3">
            <a href="berandaAdmin.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Detail Laporan</h5>
        </div>

        <div class="report-card">
            <div class="row">
                <div class="col-sm-6 mb-3 mb-sm-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="laporan-img/nasibox-laporan.png" alt="Nasi Box" style="width: 100px; height: 100px;">
                            <p class="card-title">Nasi Box</p>
                            <p class="card-text">Jumlah Beli</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <img src="laporan-img/kuekering-laporan.png" alt="Kue Kering" style="width: 100px; height: 100px;">
                            <p class="card-title">Kue Kering</p>
                            <p class="card-text">Jumlah Beli</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <img src="laporan-img/cake-laporan.png" alt="Cake" style="width: 100px; height: 100px;">
                            <p class="card-title">Cake</p>
                            <p class="card-text">Jumlah Beli</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <img src="laporan-img/kuebrownies-laporan.png" alt="Kue Brownies" style="width: 100px; height: 100px;">
                            <p class="card-title">Kue Brownies</p>
                            <p class="card-text">Jumlah Beli</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-outline-dark mb-3 mt-3 justify-content-center item-align-center">Detail Analisis</button>
    </div>
</body>

</html>