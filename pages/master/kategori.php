<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/pages/master/add/kategori.php";
if (function_exists('Tampil_Data')) {
    echo "Function Tampil_Data exists.";
} else {
    echo "Function Tampil_Data does not exist.";
}



// Debugging to ensure data fetch is correct
if ($data === null) {
    echo "Data is null.";
} else {
    echo "Data fetched successfully.";
}
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Kategori</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Kategori</h4>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-primary mb-sm-2" data-bs-toggle="modal"
                                data-bs-target="#insertModal">Tambah Data</button>

                            <table id="datatable-buttons"
                                class="table table-bordered dt-responsive nowrap w-100 table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Nama kategori</th>
                                        <th>Jenis Kategori</th>
                                        <th>Status</th>
                                        <?php if ($_SESSION['level'] === "super admin") { ?>

                                            <th>Aksi</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $data = Tampil_Data("kategori");
                                    $no = 1;
                                    if ($data !== null) {
                                        foreach ($data as $j) {
                                            $idkategori = $j->id_kategori;
                                            $namakategori = $j->nama_kategori;
                                            $jeniskategori = $j->jenis_kategori;
                                            $status = $j->status;

                                    ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $namakategori ?></td>
                                                <td><?= $jeniskategori ?></td>
                                                <td>
                                                    <form method="POST" action="webservice/update.php" style="display:inline;">
                                                        <input type="hidden" name="id_kategori" value="<?= $idkategori ?>">
                                                        <input type="hidden" name="status" value="<?= $status === 'Aktif' ? 'Nonaktif' : 'Aktif' ?>">
                                                        <button type="submit" name="update_status_kategori"
                                                            class="btn btn-sm <?= $status === 'Aktif' ? 'btn-success' : 'btn-secondary' ?>">
                                                            <?= $status ?>
                                                        </button>
                                                    </form>
                                                </td>
                                                <?php if ($_SESSION['level'] === "super admin") { ?>
                                                    <td>
                                                        <form method="POST" action="webservice/delete.php" style="display:inline;"
                                                            onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                            <input type="hidden" name="id_kategori" value="<?= $idkategori ?>">
                                                            <button type="submit" name="delete_kategori" class="btn btn-danger ">
                                                                Hapus
                                                            </button>
                                                        </form>

                                                    </td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
</div>


<script>
    $(document).ready(function() {

        $('.toggle-status').click(function() {
            let id = $(this).data('id');
            let currentStatus = $(this).data('status');
            let newStatus = currentStatus === 'Aktif' ? 'Nonaktif' : 'Aktif';

            $.post('webservice/update_status_kategori.php', {
                id: id,
                status: newStatus
            }, function(res) {
                location.reload();
            });
        });

        $('.delete-data').click(function() {
            let id = $(this).data('id');

            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                $.post('webservice/delete_kategori.php', {
                    id: id
                }, function(res) {
                    location.reload();
                });
            }
        });



    });
</script>