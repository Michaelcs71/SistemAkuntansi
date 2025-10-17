<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/pages/cetaklaporan.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';
$selectedYear = isset($_POST['year']) ? $_POST['year'] : '';
$selectedJenis = isset($_POST['jenis_kategori']) ? $_POST['jenis_kategori'] : '';

// Ambil data transaksi
$data = Tampil_Data("transaksi");

$filteredData = $data;

// Filter berdasarkan bulan dan tahun
if ($selectedMonth && $selectedYear) {
    $filteredData = array_filter($filteredData, function ($item) use ($selectedMonth, $selectedYear) {
        // Parsing tanggal dengan format datetime
        $tanggal = DateTime::createFromFormat('Y-m-d H:i:s', $item->tanggal);
        if ($tanggal) {
            return $tanggal->format('m') === $selectedMonth && $tanggal->format('Y') === $selectedYear;
        }
        return false;
    });
}

// Filter berdasarkan jenis (Masuk/Keluar)
// Filter berdasarkan jenis (Masuk/Keluar)
if (!empty($selectedJenis)) {
    $filteredData = array_filter($filteredData, function ($item) use ($selectedJenis) {
        return isset($item->jenis_kategori) && strtolower($item->jenis_kategori) === strtolower($selectedJenis);
    });
}


// Reset session dulu
unset($_SESSION['laporan_filtered']);
unset($_SESSION['laporan_bulan']);
unset($_SESSION['laporan_tahun']);
unset($_SESSION['laporan_jenis']);

// Simpan ke session hanya jika bulan & tahun dipilih
if (!empty($selectedMonth) && !empty($selectedYear)) {
    $_SESSION['laporan_filtered'] = $filteredData;
    $_SESSION['laporan_bulan'] = $selectedMonth;
    $_SESSION['laporan_tahun'] = $selectedYear;
    $_SESSION['laporan_jenis'] = $selectedJenis;
}

?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Filter Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title font-size-18">Laporan Kas Masuk & Keluar</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <select name="month" class="form-select">
                                    <option value="">Pilih Bulan</option>
                                    <?php for ($m = 1; $m <= 12; $m++) { ?>
                                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selectedMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                            <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="year" class="form-select">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($y = date("Y") - 2; $y <= date("Y"); $y++) { ?>
                                        <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- FILTER JENIS -->
                            <div class="col-md-3">
                                <select name="jenis_kategori" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="masuk" <?= $selectedJenis == "masuk" ? 'selected' : '' ?>>Kas Masuk</option>
                                    <option value="keluar" <?= $selectedJenis == "keluar" ? 'selected' : '' ?>>Kas Keluar</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-primary" type="submit">Filter</button>
                                <a href="" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <table id="datatable-custom" class="table table-bordered dt-responsive nowrap w-100 table-striped table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Keterangan</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (!empty($filteredData)) {
                                foreach ($filteredData as $t) {
                                    $tanggal = $t->tanggal;
                                    $jenis = $t->jenis_kategori;
                                    $kategori = $t->nama_kategori;
                                    $keterangan = $t->keterangan;
                                    $jumlah = $t->jumlah;
                            ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $tanggal ?></td>
                                        <td><?= $jenis ?></td>
                                        <td><?= $kategori ?></td>
                                        <td><?= $keterangan ?></td>
                                        <td class="text-end">Rp <?= number_format($jumlah, 2, ',', '.') ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Tidak ada data untuk filter yang dipilih.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Tombol Cetak -->
                    <?php if (!empty($selectedMonth) && !empty($selectedYear)) { ?>
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#cetakModal">
                            Cetak / Download PDF
                        </button>
                    <?php } else { ?>
                        <button class="btn btn-secondary" disabled title="Silakan pilih bulan dan tahun terlebih dahulu">
                            Cetak / Download PDF
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>