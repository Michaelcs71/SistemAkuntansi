<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";

$data = Tampil_Data("transaksi");

if (!is_array($data)) {
    $data = [];
}

$selectedMonth = isset($_POST['month']) && $_POST['month'] !== '' ? $_POST['month'] : date('m');
$selectedYear  = isset($_POST['year']) && $_POST['year'] !== '' ? $_POST['year'] : date('Y');

$filteredData = array_filter($data, function ($item) use ($selectedMonth, $selectedYear) {
    if (!isset($item->tanggal) || !isset($item->status)) return false;
    if (strtolower($item->status) !== 'aktif') return false;

    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $item->tanggal);
    if (!$dt) {
        $dt2 = DateTime::createFromFormat('Y-m-d', $item->tanggal);
        if (!$dt2) return false;
        $dt = $dt2;
    }

    return $dt->format('m') === $selectedMonth && $dt->format('Y') === $selectedYear;
});

$totalMasuk = 0.0;
$totalKeluar = 0.0;
$totalTransaksi = 0;

foreach ($filteredData as $item) {
    $jumlahStr = isset($item->jumlah) ? $item->jumlah : '0';
    $jumlah = (float) str_replace(['.', ','], ['', '.'], $jumlahStr);

    $jenis = isset($item->jenis_kategori) ? trim($item->jenis_kategori) : '';

    if (strtolower($jenis) === 'kas masuk' || stripos($jenis, 'masuk') !== false) {
        $totalMasuk += $jumlah;
    } elseif (strtolower($jenis) === 'kas keluar' || stripos($jenis, 'keluar') !== false) {
        $totalKeluar += $jumlah;
    }
    $totalTransaksi++;
}


$saldoAkhir = $totalMasuk - $totalKeluar;

$chartLabels = ['Kas Masuk', 'Kas Keluar'];
$chartData = [round($totalMasuk, 2), round($totalKeluar, 2)];

$perKategori = [];
foreach ($filteredData as $item) {
    $cat = isset($item->nama_kategori) && $item->nama_kategori !== '' ? $item->nama_kategori : 'Lainnya';

    $jumlahStr = isset($item->jumlah) ? $item->jumlah : '0';
    $val = (float) str_replace(['.', ','], ['', '.'], $jumlahStr);

    if (!isset($perKategori[$cat])) $perKategori[$cat] = 0;
    $perKategori[$cat] += $val;
}
arsort($perKategori);
$kategoriLabels = array_slice(array_keys($perKategori), 0, 8);
$kategoriValues = array_slice(array_values($perKategori), 0, 8);


function format_rp($value)
{
    return 'Rp ' . number_format($value, 2, ',', '.');
}
?>

<div class="main-content bg">
    <div class="page-content">
        <div class="container-fluid">


            <div class="row mb-3">
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <h4>Dashboard</h4>
                    <form method="POST" class="d-flex align-items-center" id="filterForm">
                        <select name="month" class="form-select me-2" style="width:150px">
                            <?php for ($m = 1; $m <= 12; $m++):
                                $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                                $label = date("F", mktime(0, 0, 0, $m, 10));
                            ?>
                                <option value="<?= $val ?>" <?= $selectedMonth === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endfor; ?>
                        </select>

                        <select name="year" class="form-select me-2" style="width:120px">
                            <?php for ($y = date("Y") - 2; $y <= date("Y"); $y++): ?>
                                <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>

                        <button class="btn btn-primary me-2" type="submit">Terapkan</button>
                        <a href="" class="btn btn-secondary">Reset</a>

                    </form>
                </div>
            </div>
            <div class="marquee">
                <p>Selamat Datang di Sistem Keuangan Sevenshop</p>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="small-box bg-blue text-white shadow-primary">
                        <div class="inner">
                            <h1 class="text-white" id="cardMasuk"><?= format_rp($totalMasuk) ?></h1>
                            <p>Total Kas Masuk (<?= date("F Y", strtotime($selectedYear . '-' . $selectedMonth . '-01')) ?>)</p>
                        </div>

                    </div>
                </div>
                <div class="col-3">
                    <div class="small-box bg-red text-white shadow-primary">
                        <div class="inner">
                            <h1 class="text-white" id="cardKeluar"><?= format_rp($totalKeluar) ?></h1>
                            <p>Total Kas Keluar (<?= date("F Y", strtotime($selectedYear . '-' . $selectedMonth . '-01')) ?>)</p>
                        </div>

                    </div>
                </div>
                <div class="col-3">
                    <div class="small-box bg-yellow text-white shadow-primary">
                        <div class="inner">
                            <h1 class="text-white" id="cardSaldo"><?= format_rp($saldoAkhir) ?></h1>
                            <p>Saldo Akhir (Periode)</p>
                        </div>

                    </div>
                </div>
                <div class="col-3">
                    <div class="small-box bg-green text-white shadow-primary">
                        <div class="inner">
                            <h1 class="text-white" id="cardTotalTrans"><?= $totalTransaksi ?></h1>
                            <p>Total Transaksi (Periode)</p>
                        </div>

                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Perbandingan Kas Masuk vs Kas Keluar</h5>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center" style="height: 300px;">
                            <canvas id="pieChart" style="max-height: 100%; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Pendapatan per Kategori (Top)</h5>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center" style="height: 300px;">
                            <canvas id="barChart" style="max-height: 100%; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>


                <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
    </div>
</div>
<script>
    // Data for Pie Chart (Masuk vs Keluar)
    const pieLabels = <?= json_encode($chartLabels) ?>;
    const pieData = <?= json_encode($chartData) ?>;

    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: [
                    'rgba(40,167,69,0.8)', // hijau untuk masuk
                    'rgba(220,53,69,0.8)' // merah untuk keluar
                ],
                borderColor: ['rgba(255,255,255,0.9)', 'rgba(255,255,255,0.9)'],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const value = ctx.raw || 0;
                            const formatter = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 2
                            });
                            return ctx.label + ': ' + formatter.format(value);
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Data for Bar Chart (Per Kategori Top)
    const barLabels = <?= json_encode(array_values($kategoriLabels)) ?>;
    const barValues = <?= json_encode(array_values($kategoriValues)) ?>;

    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Total (Rp)',
                data: barValues,
                backgroundColor: 'rgba(54,162,235,0.6)',
                borderColor: 'rgba(54,162,235,1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(ctx.raw);
                        }
                    }
                }
            }
        }
    });
</script>

<link rel="stylesheet" href="assets/libs/glightbox/css/glightbox.min.css">
<script src="assets/libs/glightbox/js/glightbox.min.js"></script>
<script src="assets/js/pages/lightbox.init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>