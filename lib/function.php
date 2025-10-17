<?php

function Insert_Data($table, $data)
{
    global $koneksi;

    $columns = implode(", ", array_keys($data));
    $escaped_values = array_map(function ($value) use ($koneksi) {
        return "'" . mysqli_real_escape_string($koneksi, $value) . "'";
    }, array_values($data));
    $values = implode(", ", $escaped_values);

    $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";

    echo $sql;

    if (mysqli_query($koneksi, $sql)) {
        return $koneksi->insert_id;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
        return false;
    }
}




function Tampil_Data($namaApi)
{
    global $baseURL;
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    $apiURL = 'http://localhost/SistemAkuntansi/webservice/api/' . $namaApi . '.php';

    $response = file_get_contents($apiURL, false, stream_context_create($arrContextOptions));


    return json_decode($response);
}



function Tampil_Data_Cetak($endpoint)
{
    $url = "http://localhost/SistemAkuntansi/webservice/api/$endpoint.php";
    $json = file_get_contents($url);
    return json_decode($json, true);
}

function Update_Data_Status($table, $data, $conditions)
{
    global $koneksi;

    if (!is_array($data) || !is_array($conditions)) {
        die("Data dan kondisi harus berupa array.");
    }

    $setClauses = [];
    foreach ($data as $column => $value) {
        $setClauses[] = "$column = '" . mysqli_real_escape_string($koneksi, $value) . "'";
    }
    $setQuery = implode(", ", $setClauses);

    $whereClauses = [];
    foreach ($conditions as $column => $value) {
        $whereClauses[] = "$column = '" . mysqli_real_escape_string($koneksi, $value) . "'";
    }
    $whereQuery = implode(" AND ", $whereClauses);

    $query = "UPDATE $table SET $setQuery WHERE $whereQuery";

    if (mysqli_query($koneksi, $query)) {
        return true;
    } else {
        die("Error updating record: " . mysqli_error($koneksi));
    }
}

function Delete_Data($table, $conditions)
{
    global $koneksi;

    if (!is_array($conditions)) {
        die("Kondisi harus berupa array.");
    }

    // Buat klausa WHERE
    $whereClauses = [];
    foreach ($conditions as $column => $value) {
        $whereClauses[] = "$column = '" . mysqli_real_escape_string($koneksi, $value) . "'";
    }
    $whereQuery = implode(" AND ", $whereClauses);

    // Query delete
    $query = "DELETE FROM `$table` WHERE $whereQuery";

    if (mysqli_query($koneksi, $query)) {
        return true;
    } else {
        die("Error deleting record: " . mysqli_error($koneksi));
    }
}
