<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/pages/add/transaksi.php";


$data = Tampil_Data("transaksi");

// Get selected month and year from request
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : '';
$selectedYear = isset($_POST['year']) ? $_POST['year'] : '';

// Ensure $data is an array
if (!is_array($data)) {
    $data = []; // Default to empty array if $data is null or not an array
}

// Filter data if month and year are selected
if ($selectedMonth && $selectedYear) {
    $filteredData = array_filter($data, function ($item) use ($selectedMonth, $selectedYear) {
        // Pastikan field-nya sesuai dengan yang dipakai: $item->tanggal
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $item->tanggal);
        return $datetime
            && $datetime->format('m') === $selectedMonth
            && $datetime->format('Y') === $selectedYear;
    });
} else {
    $filteredData = $data;
}


if (empty($data)) {
    echo "Data is empty or could not be fetched.";
} else {
    echo "Data fetched successfully.";
}


?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Transaksi</h4>
                    </div>
                </div>
            </div>
            <!-- End page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <form method="POST">
                                        <div class="input-group">
                                            <select name="month" class="form-select">
                                                <option value="">Pilih Bulan</option>
                                                <?php for ($m = 1; $m <= 12; $m++) { ?>
                                                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $selectedMonth == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                                                <?php } ?>
                                            </select>
                                            <select name="year" class="form-select">
                                                <option value="">Pilih Tahun</option>
                                                <?php for ($y = date("Y") - 2; $y <= date("Y"); $y++) { ?>
                                                    <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                                                <?php } ?>
                                            </select>
                                            <button class="btn btn-primary" type="submit">Filter</button>
                                            <a href="" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary mb-sm-2" data-bs-toggle="modal" data-bs-target="#insertModal">Tambah Data</button>

                            <table id="datatable"
                                class="table table-bordered dt-responsive nowrap w-100 table-striped table-hover text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <?php if ($_SESSION['level'] === "super admin") { ?>

                                            <th>Aksi</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if (!empty($filteredData)) {
                                        foreach ($filteredData as $j) {
                                            $idtransaksi = $j->id_transaksi;
                                            $tanggal = $j->tanggal;
                                            $jeniskategori = $j->jenis_kategori;
                                            $namakategori = $j->nama_kategori;
                                            $jumlah = $j->jumlah;
                                            $keterangan = $j->keterangan;
                                            $status = $j->status;
                                    ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $tanggal ?></td>
                                                <td><?= $jeniskategori ?></td>
                                                <td><?= $namakategori ?></td>
                                                <td><?= $jumlah ?></td>
                                                <td><?= $keterangan ?></td>
                                                <td>
                                                    <form method="POST" action="webservice/update.php" style="display:inline;">
                                                        <input type="hidden" name="id_transaksi" value="<?= $idtransaksi ?>">
                                                        <input type="hidden" name="status" value="<?= $status === 'Aktif' ? 'Nonaktif' : 'Aktif' ?>">
                                                        <button type="submit" name="update_transaksi"
                                                            class="btn btn-sm <?= $status === 'Aktif' ? 'btn-success' : 'btn-secondary' ?>">
                                                            <?= $status ?>
                                                        </button>
                                                    </form>
                                                </td>

                                                </td>

                                                <?php if ($_SESSION['level'] === "super admin") { ?>

                                                    <td>

                                                        <form method="POST" action="webservice/delete.php" style="display:inline;"
                                                            onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                            <input type="hidden" name="id_transaksi" value="<?= $idtransaksi ?>">
                                                            <button type="submit" name="delete_transaksi" class="btn btn-danger btn-sm">
                                                                Hapus
                                                            </button>
                                                        </form>

                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data.</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End card -->
                </div> <!-- End col -->
            </div> <!-- End row -->
        </div> <!-- Container-fluid -->
    </div>
</div>



<script>
    $(document).ready(function() {

        $('.toggle-status').click(function() {
            let id = $(this).data('id');
            let currentStatus = $(this).data('status');
            let newStatus = currentStatus === 'Aktif' ? 'Nonaktif' : 'Aktif';

            $.post('webservice/update_status.php', {
                id: id,
                status: newStatus
            }, function(res) {
                location.reload();
            });
        });

        $('.delete-data').click(function() {
            let id = $(this).data('id');

            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.post('webservice/delete_transaksi.php', {
                    id: id
                }, function(res) {
                    location.reload();
                });
            }
        });

    });
</script>