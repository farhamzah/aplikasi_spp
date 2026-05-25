<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect("localhost", "root", "", "db_spp");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

$koneksi = $conn;
