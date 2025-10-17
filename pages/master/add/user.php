<div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Data Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="webservice/insert.php" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" name="password" id="password" required>
                    </div>

                    <?php if ($_SESSION['level'] === "super admin") { ?>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" name="level" id="level" required>
                                <option disabled selected>Pilih Level</option>
                                <option value="super admin">Super Admin</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    <?php } else { ?>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <input type="text" class="form-control" value="admin" disabled>
                            <input type="hidden" name="level" value="admin">
                        </div>
                    <?php } ?>

                    <input type="hidden" name="status" value="Aktif">


                    <div class="mb-3 d-flex flex-column">
                        <button name="insert_user" type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>