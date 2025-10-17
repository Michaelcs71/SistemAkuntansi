<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
include $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";

$baseURL = "http://localhost/SistemAkuntansi"; // Pastikan URL ini sesuai dengan path proyek Anda
date_default_timezone_set('Asia/Jakarta');
$time = date('Y-m-d H:i:s'); // Atau format waktu lain sesuai kebutuhan Anda



if (isset($_POST['insert_kategori'])) {
    $data = array(
        'nama_kategori' => mysqli_real_escape_string($koneksi, $_POST['nama_kategori']),
        'jenis_kategori' => mysqli_real_escape_string($koneksi, $_POST['jenis_kategori']),
    );

    // Call the Insert_Data function to insert data
    Insert_Data("master_kategori", $data);
    header("Location: " . $baseURL . "/index.php?link=kategori");
    exit();
}



if (isset($_POST['insert_user'])) {
    $data = array(
        'username' => mysqli_real_escape_string($koneksi, $_POST['username']),
        'password' => mysqli_real_escape_string($koneksi, $_POST['password']),
        'level' => mysqli_real_escape_string($koneksi, $_POST['level']),
        'status' => mysqli_real_escape_string($koneksi, $_POST['status']),
    );

    // Call the Insert_Data function to insert data
    Insert_Data("user", $data);
    header("Location: " . $baseURL . "/index.php?link=user");
    exit();
}




if (isset($_POST['insert_transaksi'])) {
    $data = array(
        'tanggal' => mysqli_real_escape_string($koneksi, $_POST['tanggal']),
        'jenis_kategori' => mysqli_real_escape_string($koneksi, $_POST['jenis_kategori']),
        'id_kategori' => mysqli_real_escape_string($koneksi, $_POST['nama_kategori']),
        'jumlah' => mysqli_real_escape_string($koneksi, $_POST['jumlah']),
        'keterangan' => mysqli_real_escape_string($koneksi, $_POST['keterangan']),
        'status' => mysqli_real_escape_string($koneksi, $_POST['status']),

    );

    // Call the Insert_Data function to insert data
    Insert_Data("transaksi", $data);
    header("Location: " . $baseURL . "/index.php?link=transaksi");
    exit();
}
