<?php
// config.php

// Cek apakah kita sedang berjalan di Railway
// Railway otomatis menyediakan variabel MYSQLHOST, MYSQLUSER, dll.
if (getenv('MYSQLHOST') !== false) {
    $db_host = getenv('MYSQLHOST');
    $db_user = getenv('MYSQLUSER');
    $db_pass = getenv('MYSQLPASSWORD');
    $db_name = getenv('MYSQLDATABASE');
    $db_port = getenv('MYSQLPORT') ?: 3306;
} else {
    // Jika tidak ada variabel Railway, berarti kita sedang di Laragon (Lokal)
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "adarasa";
    $db_port = 3306;
}
