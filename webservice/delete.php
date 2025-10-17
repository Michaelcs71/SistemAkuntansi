<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
include $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";

if (isset($_POST['delete_transaksi'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_transaksi']);

    Delete_Data("transaksi", ['id_transaksi' => $id]);

    header("Location: " . $baseURL . "/index.php?link=transaksi");
    exit();
} else {
    echo "Request tidak valid!";
}

if (isset($_POST['delete_user'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['user_id']);

    Delete_Data("user", ['user_id' => $id]);

    header("Location: " . $baseURL . "/index.php?link=user");
    exit();
} else {
    echo "Request tidak valid!";
}

if (isset($_POST['delete_kategori'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);

    Delete_Data("master_kategori", ['id_kategori' => $id]);

    header("Location: " . $baseURL . "/index.php?link=kategori");
    exit();
} else {
    echo "Request tidak valid!";
}
