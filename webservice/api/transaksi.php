<?php
include "../config.php";

$hasil = mysqli_query($koneksi, "SELECT 
    tp.*, 
    mk.nama_kategori
FROM 
    transaksi tp
LEFT JOIN 
    master_kategori mk ON mk.id_kategori = tp.id_kategori ;");

$jsonRespon = array();
if (mysqli_num_rows($hasil) > 0) {
    while ($row = mysqli_fetch_assoc($hasil)) {
        $jsonRespon[] = $row;
    }
}


echo json_encode($jsonRespon, JSON_PRETTY_PRINT);
