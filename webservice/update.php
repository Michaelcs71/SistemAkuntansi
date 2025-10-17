<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";
$time = date('Y-m-d H:i:s'); // Atau format waktu lain sesuai kebutuhan Anda



if (isset($_POST['update_transaksi'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_POST['id_transaksi']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    $data = [
        'status' => $status
    ];

    $conditions = [
        'id_transaksi' => $id_transaksi
    ];

    // Panggil fungsi general untuk update data
    Update_Data_Status("transaksi", $data, $conditions);

    // Redirect setelah update berhasil
    header("Location: " . $baseURL . "/index.php?link=transaksi");
    exit();
}


if (isset($_POST['update_status_user'])) {
    $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    $data = [
        'status' => $status
    ];

    $conditions = [
        'user_id' => $user_id
    ];

    // Panggil fungsi general untuk update data
    Update_Data_Status("user", $data, $conditions);

    // Redirect setelah update berhasil
    header("Location: " . $baseURL . "/index.php?link=user");
    exit();
}

if (isset($_POST['update_status_kategori'])) {
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    $data = [
        'status' => $status
    ];

    $conditions = [
        'id_kategori' => $id_kategori
    ];

    Update_Data_Status("master_kategori", $data, $conditions);

    header("Location: " . $baseURL . "/index.php?link=kategori");
    exit();
}


if (isset($_POST['update_user'])) {
    $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $data = [
        'username' => $username,
        'password' => $password
    ];

    $conditions = [
        'user_id' => $user_id
    ];

    Update_Data_Status("user", $data, $conditions);

    header("Location: " . $baseURL . "/index.php?link=user");
    exit();
}
