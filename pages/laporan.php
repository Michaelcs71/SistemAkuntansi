<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil filter dari POST
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';
$selectedYear = isset($_POST['year']) ? $_POST['year'] : '';
$selectedJenis = isset($_POST['jenis_kategori']) ? $_POST['jenis_kategori'] : '';

// Ambil data transaksi
$data = Tampil_Data("transaksi");
$filteredData = $data;

// Filter berdasarkan bulan dan tahun
if ($selectedMonth && $selectedYear) {
    $filteredData = array_filter($filteredData, function ($item) use ($selectedMonth, $selectedYear) {
        $tanggal = DateTime::createFromFormat('Y-m-d H:i:s', $item->tanggal);
        return $tanggal ? ($tanggal->format('m') === $selectedMonth && $tanggal->format('Y') === $selectedYear) : false;
    });
}

// Filter berdasarkan jenis
if (!empty($selectedJenis)) {
    $filteredData = array_filter($filteredData, function ($item) use ($selectedJenis) {
        return isset($item->jenis_kategori) && strtolower($item->jenis_kategori) === strtolower($selectedJenis);
    });
}

// Simpan ke session
unset($_SESSION['laporan_filtered'], $_SESSION['laporan_bulan'], $_SESSION['laporan_tahun'], $_SESSION['laporan_jenis']);
if (!empty($selectedMonth) && !empty($selectedYear)) {
    $_SESSION['laporan_filtered'] = $filteredData;
    $_SESSION['laporan_bulan'] = $selectedMonth;
    $_SESSION['laporan_tahun'] = $selectedYear;
    $_SESSION['laporan_jenis'] = $selectedJenis;
}

function rupiah_to_float($rupiah)
{
    $angka = str_replace(['.', ','], ['', '.'], $rupiah);
    return (float)$angka;
}

$totalMasuk = 0;
$totalKeluar = 0;
foreach ($filteredData as $item) {
    $jumlah = rupiah_to_float($item->jumlah ?? 0);
    if (($item->jenis_kategori ?? '') === 'masuk') $totalMasuk += $jumlah;
    if (($item->jenis_kategori ?? '') === 'keluar') $totalKeluar += $jumlah;
}
$saldoAkhir = $totalMasuk - $totalKeluar;
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
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
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selectedMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                            <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="year" class="form-select">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($y = date("Y") - 2; $y <= date("Y"); $y++): ?>
                                        <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
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

                    <!-- Tabel -->
                    <table class="table table-bordered dt-responsive nowrap w-100 table-striped table-hover text-center">
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
                            <?php if (!empty($filteredData)): $no = 1; ?>
                                <?php foreach ($filteredData as $t): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($t->tanggal) ?></td>
                                        <td><?= htmlspecialchars($t->jenis_kategori) ?></td>
                                        <td><?= htmlspecialchars($t->nama_kategori) ?></td>
                                        <td><?= htmlspecialchars($t->keterangan) ?></td>
                                        <td class="text-end">Rp <?= number_format(rupiah_to_float($t->jumlah), 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">Tidak ada data untuk filter yang dipilih.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Tombol Cetak -->
                    <?php if (!empty($selectedMonth) && !empty($selectedYear)): ?>
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#cetakModal">Cetak / Download PDF</button>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled title="Silakan pilih bulan dan tahun terlebih dahulu">Cetak / Download PDF</button>
                    <?php endif; ?>

                    <!-- Modal Cetak -->
                    <div class="modal fade" id="cetakModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Laporan Kas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body" id="printArea">
                                    <h4 class="text-center">PT. Sevenshop</h4>
                                    <h5 class="text-center">Laporan Kas Masuk & Keluar</h5>
                                    <h6 class="text-center">
                                        Periode: <?= htmlspecialchars(($selectedMonth ?: '-') . '/' . ($selectedYear ?: '-')) ?>
                                        <?= $selectedJenis ? " - {$selectedJenis}" : '' ?>
                                    </h6>
                                    <table border="1" style="width:100%; border-collapse:collapse; margin-top:20px; font-size:14px;">
                                        <thead>
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
                                            <?php if (!empty($filteredData)): $no = 1; ?>
                                                <?php foreach ($filteredData as $item): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td><?= htmlspecialchars($item->tanggal) ?></td>
                                                        <td><?= htmlspecialchars($item->jenis_kategori) ?></td>
                                                        <td><?= htmlspecialchars($item->nama_kategori) ?></td>
                                                        <td><?= htmlspecialchars($item->keterangan) ?></td>
                                                        <td class="text-end">Rp <?= number_format(rupiah_to_float($item->jumlah), 2, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data untuk periode ini.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <div style="margin-top:20px;">
                                        <p><strong>Total Kas Masuk:</strong> Rp <?= number_format($totalMasuk, 2, ',', '.') ?></p>
                                        <p><strong>Total Kas Keluar:</strong> Rp <?= number_format($totalKeluar, 2, ',', '.') ?></p>
                                        <p><strong>Saldo Akhir:</strong> Rp <?= number_format($saldoAkhir, 2, ',', '.') ?></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" onclick="printReport()">Cetak</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function printReport() {
                            var printContents = document.getElementById("printArea").innerHTML;
                            var originalContents = document.body.innerHTML;
                            document.body.innerHTML = printContents;
                            window.print();
                            document.body.innerHTML = originalContents;
                            window.location.reload();
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</div>