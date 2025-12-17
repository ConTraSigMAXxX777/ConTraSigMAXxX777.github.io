<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_iot_energy";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Connection Failed: " . mysqli_connect_error());
}
date_default_timezone_set('Asia/Jakarta');
?>