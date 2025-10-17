<?php
// definisikan koneksi ke database
$baseURL = "http://localhost/SistemAkuntansi";
$server = "localhost";
$username = "root";
$password = "";
$database = "sistem_akuntansi";

// Koneksi dan memilih database di server
$koneksi = new mysqli($server, $username, $password, $database);

// Check connection
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
// tess