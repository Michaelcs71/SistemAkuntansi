<?php

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['level'])) {
    // Kalau belum login, arahkan ke halaman login
    header("Location: index.php?link=login");
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/webservice/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/lib/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/pages/master/add/user.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/SistemAkuntansi/pages/master/update/user.php";
// Debugging to ensure file includes are correct
if (function_exists('Tampil_Data')) {
    echo "Function Tampil_Data exists.";
} else {
    echo "Function Tampil_Data does not exist.";
}




?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Akun</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data User</h4>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-primary mb-sm-2" data-bs-toggle="modal"
                                data-bs-target="#insertModal">Tambah Data</button>

                            <table id="datatable-buttons"
                                class="table table-bordered dt-responsive nowrap w-100 table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $data = Tampil_Data("user");
                                    $no = 1;
                                    if ($data !== null) {
                                        foreach ($data as $j) {
                                            $iduser = $j->user_id;
                                            $username = $j->username;
                                            $password = $j->password;
                                            $level = $j->level;
                                            $status = $j->status;

                                            if ($_SESSION['level'] !== "super admin" && $level === "super admin") {
                                                continue;
                                            }
                                    ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $username ?></td>
                                                <td><?= $password ?></td>
                                                <td><?= $level ?></td>
                                                <td>
                                                    <form method="POST" action="webservice/update.php" style="display:inline;">
                                                        <input type="hidden" name="user_id" value="<?= $iduser ?>">
                                                        <input type="hidden" name="status" value="<?= $status === 'Aktif' ? 'Nonaktif' : 'Aktif' ?>">
                                                        <button type="submit" name="update_status_user"
                                                            class="btn btn-sm <?= $status === 'Aktif' ? 'btn-success' : 'btn-secondary' ?>">
                                                            <?= $status ?>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"
                                                        id="updateModal"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateModalUser"
                                                        data-idpkrja="<?= $iduser ?>"
                                                        data-stts="<?= $status ?>">Update
                                                    </button>
                                                </td>
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

            $.post('webservice/update_status_user.php', {
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