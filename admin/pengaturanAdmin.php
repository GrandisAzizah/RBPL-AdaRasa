<?php
session_start();
// user belum login
if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Page Pengaturan Admin</title>
    <link rel="stylesheet" href="../pesanan.css">
    <link href="https://fonts.googleapis.com/css2?family=Aleo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<style>
    body {
        font-family: 'Aleo', serif;
        background-color: #F5F5F5;
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
    <div>
        <div style="display: flex; align-items: center; justify-content: center; position: relative;" class="mb-3">
            <a href="berandaAdmin.php" style="position: absolute; left: 0; flex-shrink: 0;">
                <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.6667 19H6.33337M6.33337 19L15.8334 9.5M6.33337 19L15.8334 28.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <h5 style="margin: 0;">Pengaturan Akun</h5>
        </div>

        <div class="menu-item mt-3">
            <a href="gantiUsernameAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" class="icon">
                    <path d="M8 8C6.9 8 5.95833 7.60833 5.175 6.825C4.39167 6.04167 4 5.1 4 4C4 2.9 4.39167 1.95833 5.175 1.175C5.95833 0.391667 6.9 0 8 0C9.1 0 10.0417 0.391667 10.825 1.175C11.6083 1.95833 12 2.9 12 4C12 5.1 11.6083 6.04167 10.825 6.825C10.0417 7.60833 9.1 8 8 8ZM0 14V13.2C0 12.6333 0.146 12.1127 0.438 11.638C0.73 11.1633 1.11733 10.8007 1.6 10.55C2.63333 10.0333 3.68333 9.646 4.75 9.388C5.81667 9.13 6.9 9.00067 8 9C9.1 8.99933 10.1833 9.12867 11.25 9.388C12.3167 9.64733 13.3667 10.0347 14.4 10.55C14.8833 10.8 15.271 11.1627 15.563 11.638C15.855 12.1133 16.0007 12.634 16 13.2V14C16 14.55 15.8043 15.021 15.413 15.413C15.0217 15.805 14.5507 16.0007 14 16H2C1.45 16 0.979333 15.8043 0.588 15.413C0.196666 15.0217 0.000666667 14.5507 0 14ZM2 14H14V13.2C14 13.0167 13.9543 12.85 13.863 12.7C13.7717 12.55 13.6507 12.4333 13.5 12.35C12.6 11.9 11.6917 11.5627 10.775 11.338C9.85833 11.1133 8.93333 11.0007 8 11C7.06667 10.9993 6.14167 11.112 5.225 11.338C4.30833 11.564 3.4 11.9013 2.5 12.35C2.35 12.4333 2.229 12.55 2.137 12.7C2.045 12.85 1.99933 13.0167 2 13.2V14ZM8 6C8.55 6 9.021 5.80433 9.413 5.413C9.805 5.02167 10.0007 4.55067 10 4C9.99933 3.44933 9.80367 2.97867 9.413 2.588C9.02233 2.19733 8.55133 2.00133 8 2C7.44867 1.99867 6.978 2.19467 6.588 2.588C6.198 2.98133 6.002 3.452 6 4C5.998 4.548 6.194 5.019 6.588 5.413C6.982 5.807 7.45267 6.00267 8 6Z" fill="black" />
                </svg>
            </a>
            <span class="label"><a href="gantiUsernameAdmin.php">Ganti Username</a></span>
            <a href="gantiUsernameAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24" fill="none" class="arrow-icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1569 12.7111L4.49994 18.3681L3.08594 16.9541L8.03594 12.0041L3.08594 7.05414L4.49994 5.64014L10.1569 11.2971C10.3444 11.4847 10.4497 11.739 10.4497 12.0041C10.4497 12.2693 10.3444 12.5236 10.1569 12.7111Z" fill="black" />
                </svg>
            </a>
        </div>

        <div class="menu-item">
            <a href="gantiPasswordAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="icon">
                    <path d="M12 10V14M10.268 11L13.732 13M13.732 11L10.267 13M6.732 10V14M5 11L8.464 13M8.464 11L5 13M17.268 10V14M15.536 11L19 13M19 11L15.535 13M22 12C22 15.771 22 17.657 20.828 18.828C19.656 19.999 17.771 20 14 20H10C6.229 20 4.343 20 3.172 18.828C2.001 17.656 2 15.771 2 12C2 8.229 2 6.343 3.172 5.172C4.344 4.001 6.229 4 10 4H14C17.771 4 19.657 4 20.828 5.172C21.482 5.825 21.771 6.7 21.898 8" stroke="black" stroke-width="1.5" stroke-linecap="round" />
                </svg></a>
            <span class="label"><a href="gantiPasswordAdmin.php">Ganti password</a></span>
            <a href="gantiPasswordAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24" fill="none" class="arrow-icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1569 12.7111L4.49994 18.3681L3.08594 16.9541L8.03594 12.0041L3.08594 7.05414L4.49994 5.64014L10.1569 11.2971C10.3444 11.4847 10.4497 11.739 10.4497 12.0041C10.4497 12.2693 10.3444 12.5236 10.1569 12.7111Z" fill="black" />
                </svg>
            </a>
        </div>

        <div class="menu-item">
            <a href="editProfilAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.4549 5.41583C21.5499 5.56004 21.5922 5.73261 21.5747 5.90442C21.5573 6.07622 21.481 6.23673 21.3589 6.35883L12.1659 15.5508C12.0718 15.6448 11.9545 15.7121 11.8259 15.7458L7.99689 16.7458C7.87032 16.7788 7.73732 16.7782 7.61109 16.7439C7.48485 16.7096 7.36978 16.6429 7.27729 16.5504C7.18479 16.4579 7.1181 16.3429 7.08382 16.2166C7.04955 16.0904 7.04888 15.9574 7.08189 15.8308L8.08189 12.0028C8.1118 11.8882 8.16679 11.7816 8.24289 11.6908L17.4699 2.46983C17.6105 2.32938 17.8011 2.25049 17.9999 2.25049C18.1986 2.25049 18.3893 2.32938 18.5299 2.46983L21.3589 5.29783C21.3938 5.33467 21.4259 5.37411 21.4549 5.41583ZM19.7679 5.82783L17.9999 4.06083L9.48189 12.5788L8.85689 14.9718L11.2499 14.3468L19.7679 5.82783Z" fill="black" />
                    <path d="M19.641 17.1601C19.9143 14.824 20.0016 12.47 19.902 10.1201C19.8997 10.0647 19.9088 10.0094 19.929 9.95778C19.9491 9.90614 19.9798 9.85925 20.019 9.82008L21.003 8.83608C21.0299 8.80904 21.064 8.79033 21.1013 8.78222C21.1385 8.77411 21.1774 8.77693 21.2131 8.79034C21.2488 8.80375 21.2798 8.82719 21.3025 8.85783C21.3252 8.88847 21.3386 8.92502 21.341 8.96308C21.5257 11.7543 21.4554 14.5566 21.131 17.3351C20.895 19.3571 19.271 20.9421 17.258 21.1671C13.7633 21.5538 10.2367 21.5538 6.74201 21.1671C4.73001 20.9421 3.10501 19.3571 2.86901 17.3351C2.45512 13.7905 2.45512 10.2097 2.86901 6.66508C3.10501 4.64308 4.72901 3.05808 6.74201 2.83308C9.39446 2.54012 12.0667 2.46888 14.731 2.62008C14.7691 2.62281 14.8057 2.63642 14.8363 2.65929C14.867 2.68215 14.8904 2.71332 14.9039 2.7491C14.9173 2.78487 14.9203 2.82376 14.9123 2.86115C14.9044 2.89854 14.8859 2.93287 14.859 2.96008L13.866 3.95208C13.8272 3.99092 13.7808 4.02136 13.7297 4.04149C13.6786 4.06162 13.6239 4.07101 13.569 4.06908C11.3458 3.99293 9.11993 4.07815 6.90901 4.32408C6.26295 4.39558 5.65986 4.6828 5.19717 5.13933C4.73447 5.59586 4.43919 6.19504 4.35901 6.84008C3.95787 10.2684 3.95787 13.7318 4.35901 17.1601C4.43919 17.8051 4.73447 18.4043 5.19717 18.8608C5.65986 19.3174 6.26295 19.6046 6.90901 19.6761C10.264 20.0511 13.736 20.0511 17.092 19.6761C17.7381 19.6046 18.3412 19.3174 18.8039 18.8608C19.2666 18.4043 19.5608 17.8051 19.641 17.1601Z" fill="black" />
                </svg>
            </a>
            <span class="label"><a href="editProfilAdmin.php">Edit Profil</a>
            </span>
            <a href="editProfilAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24" fill="none" class="arrow-icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1569 12.7111L4.49994 18.3681L3.08594 16.9541L8.03594 12.0041L3.08594 7.05414L4.49994 5.64014L10.1569 11.2971C10.3444 11.4847 10.4497 11.739 10.4497 12.0041C10.4497 12.2693 10.3444 12.5236 10.1569 12.7111Z" fill="black" />
                </svg>
            </a>
        </div>

        <div class="menu-item">
            <a href="../logout.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="icon">
                    <path d="M13.496 21H6.5C5.395 21 4.5 19.849 4.5 18.429V5.57C4.5 4.151 5.395 3 6.5 3H13.5M16 15.5L19.5 12L16 8.5M9.5 11.996H19.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <span class="label"><a href="../logout.php">Log out</a></span>
            <a href="../logout.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24" fill="none" class="arrow-icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1569 12.7111L4.49994 18.3681L3.08594 16.9541L8.03594 12.0041L3.08594 7.05414L4.49994 5.64014L10.1569 11.2971C10.3444 11.4847 10.4497 11.739 10.4497 12.0041C10.4497 12.2693 10.3444 12.5236 10.1569 12.7111Z" fill="black" />
                </svg>
            </a>
        </div>

        <h5>Info Lainnya</h5>

        <div class="menu-item">
            <a href="tentangAPKAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C17.5229 2 22 6.47713 22 12C22 17.5228 17.5229 22 12 22C6.47717 22 2 17.5228 2 12C2 6.47713 6.47717 2 12 2ZM13.0022 11H11.0022V17H13.0022V11ZM12.0102 6.74998C11.2811 6.74998 10.7522 7.27592 10.7522 7.98612C10.7522 8.72506 11.2671 9.25002 12.0102 9.25002C12.7243 9.25002 13.2522 8.72502 13.2522 8C13.2522 7.27597 12.7243 6.74998 12.0102 6.74998Z" fill="black" />
                </svg>
            </a>
            <span class="label"><a href="tentangAPKAdmin.php">Tentang Aplikasi</a></span>
            <a href="tentangAPKAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24" fill="none" class="arrow-icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1569 12.7111L4.49994 18.3681L3.08594 16.9541L8.03594 12.0041L3.08594 7.05414L4.49994 5.64014L10.1569 11.2971C10.3444 11.4847 10.4497 11.739 10.4497 12.0041C10.4497 12.2693 10.3444 12.5236 10.1569 12.7111Z" fill="black" />
                </svg>
            </a>
        </div>

        <div class="menu-item">
            <a href="kebijakanPrivasiAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 10.8181 20.7672 9.64778 20.3149 8.55585C19.8626 7.46392 19.1997 6.47177 18.364 5.63604C17.5282 4.80031 16.5361 4.13738 15.4442 3.68508C14.3522 3.23279 13.1819 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 14.3869 3.94821 16.6761 5.63604 18.364C7.32387 20.0518 9.61305 21 12 21ZM11.768 15.64L16.768 9.64L15.232 8.36L10.932 13.519L8.707 11.293L7.293 12.707L10.293 15.707L11.067 16.481L11.768 15.64Z" fill="black" />
                </svg>
            </a>
            <span class="label"><a href="kebijakanPrivasiAdmin.php">Kebijakan Privasi</a></span>
            <a href="kebijakanPrivasiAdmin.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24" fill="none" class="arrow-icon">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1569 12.7111L4.49994 18.3681L3.08594 16.9541L8.03594 12.0041L3.08594 7.05414L4.49994 5.64014L10.1569 11.2971C10.3444 11.4847 10.4497 11.739 10.4497 12.0041C10.4497 12.2693 10.3444 12.5236 10.1569 12.7111Z" fill="black" />
                </svg>
            </a>
        </div>
    </div>
</body>

</html>