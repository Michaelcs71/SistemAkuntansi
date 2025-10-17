<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data filter dan hasil filter dari session
$filteredData  = isset($_SESSION['laporan_filtered']) ? $_SESSION['laporan_filtered'] : [];
$selectedMonth = isset($_SESSION['laporan_bulan']) ? $_SESSION['laporan_bulan'] : '';
$selectedYear  = isset($_SESSION['laporan_tahun']) ? $_SESSION['laporan_tahun'] : '';
$selectedJenis = isset($_SESSION['laporan_jenis']) ? $_SESSION['laporan_jenis'] : '';

// Hitung total pemasukan dan pengeluaran
$totalMasuk = 0;
$totalKeluar = 0;

foreach ($filteredData as $item) {
    if ($item->jenis_kategori === 'masuk') {
        $totalMasuk += $item->jumlah;
    } elseif ($item->jenis_kategori === 'keluar') {
        $totalKeluar += $item->jumlah;
    }
}

$saldoAkhir = $totalMasuk - $totalKeluar;
?>

<!-- MODAL CETAK / DOWNLOAD -->
<div class="modal fade" id="cetakModal" tabindex="-1" aria-labelledby="cetakModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Laporan Kas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="printArea">
                <h4 class="text-center">PT. Sevenshop</h4>
                <h5 class="text-center">Laporan Kas Masuk & Keluar</h5>
                <h6 class="text-center">
                    Periode:
                    <?= htmlspecialchars(($selectedMonth ?: '-') . '/' . ($selectedYear ?: '-'), ENT_QUOTES, 'UTF-8') ?>
                    <?= $selectedJenis ? " - {$selectedJenis}" : '' ?>
                </h6>

                <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;" border="1">
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
                        <?php if (!empty($filteredData)): ?>
                            <?php $no = 1;
                            foreach ($filteredData as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($item->tanggal, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($item->jenis_kategori, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($item->nama_kategori, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($item->keterangan, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end">Rp <?= number_format($item->jumlah, 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data untuk periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px;">
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