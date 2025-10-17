<div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form Data Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="webservice/insert.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="datetime-local" class="form-control" name="tanggal" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jenis_kategori" class="form-label">Jenis Kategori</label>
                            <select class="form-select" name="jenis_kategori" id="jenis_kategori" required>
                                <option disabled selected>Pilih Jenis</option>
                                <option value="masuk">Masuk</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <select class="form-select" name="nama_kategori" id="nama_kategori" required disabled>
                                <option disabled selected>Pilih Nama</option>
                                <?php
                                // Ambil semua kategori aktif, nanti akan difilter di JS
                                $queryGetNama = "SELECT * FROM master_kategori WHERE status = 'Aktif'";
                                $getNama = mysqli_query($koneksi, $queryGetNama);
                                $kategoriList = [];
                                while ($nama = mysqli_fetch_assoc($getNama)) {
                                    $kategoriList[] = $nama; // simpan di array untuk JS
                                ?>
                                    <option value="<?= $nama['id_kategori'] ?>" data-jenis="<?= $nama['jenis_kategori'] ?>"><?= $nama['nama_kategori'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <input type="text" class="form-control" id="jumlah_rupiah" required>
                            <input type="hidden" name="jumlah" id="jumlah_hidden">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan" required>
                        </div>
                    </div>

                    <input type="hidden" name="status" value="Aktif">

                    <div class="d-flex justify-content-end">
                        <button name="insert_transaksi" type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const jenisSelect = document.getElementById('jenis_kategori');
    const namaSelect = document.getElementById('nama_kategori');

    // Simpan semua opsi kategori
    const allKategoriOptions = Array.from(namaSelect.options);

    jenisSelect.addEventListener('change', function() {
        const selectedJenis = this.value; // masuk / keluar

        // Aktifkan select nama kategori
        namaSelect.disabled = false;

        // Filter opsi kategori sesuai jenis
        namaSelect.innerHTML = '<option disabled selected>Pilih Nama</option>'; // reset
        allKategoriOptions.forEach(option => {
            if (option.dataset.jenis === selectedJenis) {
                namaSelect.appendChild(option);
            }
        });
    });

    // ---------------- Rupiah Input ----------------
    const rupiahInput = document.getElementById('jumlah_rupiah');
    const hiddenInput = document.getElementById('jumlah_hidden');

    rupiahInput.addEventListener('keyup', function(e) {
        let angka = this.value.replace(/[^,\d]/g, "").toString();
        let split = angka.split(",");
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        this.value = rupiah;
        hiddenInput.value = rupiah; // tetap dikirim ke database sebagai teks
    });
</script>