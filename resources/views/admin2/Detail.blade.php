<div class="modal fade" id="detailDiolahModal" tabindex="-1" aria-labelledby="detailDiolahLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="detailDiolahLabel">DETAIL LIMBAH DIOLAH</h5>
                <h5 class="ms-auto">Mesin <span id="mesinTitle">-</span></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal"
                    aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <label for="filterBulan" class="form-label me-2 mb-0">Filter Bulan:</label>
                        <select id="filterBulan" class="form-select form-select-sm fw-bold" onchange="filterByMonth()">
                            <option value="">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    <!-- Export Button (Kanan) -->
                    <button class="btn btn-success btn-sm" onclick="exportFilteredData()">
                        <i class="fas fa-file-excel"></i> Export ke Excel
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-info">
                            <tr>
                                <th>No</th>
                                <th>ID Limbah Diolah</th>
                                <th>Kode Limbah (Deskripsi)</th>
                                <th>Berat Input (Kg)</th>
                                <th>Tanggal Input</th>
                                <th>Bottom Ash (2%)</th>
                                <th>Fly Ash (0.4%)</th>
                                <th>Flue Gas (1%)</th>
                            </tr>
                        </thead>
                        <tbody id="detailDiolahTableBody">
                            <!-- akan diisi via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Summary Table -->
                <div class="mt-4">
                    <h6 class="text-primary"><i class="fas fa-chart-pie"></i> Ringkasan Residu Hasil Olahan</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Jenis Residu</th>
                                    <th>Persentase</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="resumeTableBody">
                                <!-- akan diisi via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let currentMesinId = null; // Variable untuk menyimpan mesin_id yang sedang aktif

        function exportFilteredData() {
            const bulanInput = document.getElementById('filterBulan').value;
            if (!bulanInput) {
                alert('Silakan pilih bulan terlebih dahulu.');
                return;
            }

            if (!currentMesinId) {
                alert('Data mesin tidak ditemukan.');
                return;
            }

            // Redirect ke route export dengan mesin_id dan bulan
            window.location.href = `/detaillimbahdiolah/export/${currentMesinId}/${bulanInput}`;
        }

        function showDetailLimbahDiolah(mesin_id, no_mesin) {
            currentMesinId = mesin_id; // Simpan mesin_id untuk digunakan di export

            fetch(`/detaillimbahdiolah/${mesin_id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('mesinTitle').textContent = no_mesin;
                    const tbody = document.getElementById('detailDiolahTableBody');
                    const resumeBody = document.getElementById('resumeTableBody');
                    tbody.innerHTML = '';
                    resumeBody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" class="text-center">Tidak ada data ditemukan</td></tr>`;
                        resumeBody.innerHTML =
                            `<tr><td colspan="3" class="text-center">Tidak ada data residu</td></tr>`;
                    } else {
                        // Variables untuk menghitung total residu
                        let totalBottomAsh = 0;
                        let totalFlyAsh = 0;
                        let totalFlueGas = 0;

                        data.forEach((item, index) => {
                            // Semua limbah menghasilkan residu sesuai persentase yang ditentukan
                            const bottomAshDisplay = `${item.bottom_ash} Kg `;
                            const flyAshDisplay = `${item.fly_ash} Kg `;
                            const flueGasDisplay = `${item.flue_gas} Kg `;

                            // Tambahkan ke total residu
                            totalBottomAsh += parseFloat(item.bottom_ash);
                            totalFlyAsh += parseFloat(item.fly_ash);
                            totalFlueGas += parseFloat(item.flue_gas);

                            const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.limbah_diolah_id}</td>
                                <td>${item.kode_limbah.kode} (${item.kode_limbah.deskripsi})</td>
                                <td>${item.berat_kg} Kg</td>
                                <td>${item.tanggal_input}</td>
                                <td class="text-center">${bottomAshDisplay}</td>
                                <td class="text-center">${flyAshDisplay}</td>
                                <td class="text-center">${flueGasDisplay}</td>
                            </tr>
                        `;
                            tbody.innerHTML += row;
                        });

                        // Tampilkan ringkasan residu (semua jenis limbah menghasilkan residu)
                        // Fungsi untuk format angka tanpa trailing zeros
                        function formatNumber(num) {
                            return num == parseInt(num) ? parseInt(num).toString() : parseFloat(num.toFixed(4))
                                .toString();
                        }

                        resumeBody.innerHTML += `
                        <tr>
                            <td><i class="fas fa-circle text-danger"></i> Bottom Ash</td>
                            <td>2% dari total limbah diolah</td>
                            <td>${formatNumber(totalBottomAsh)} Kg</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-circle text-warning"></i> Fly Ash</td>
                            <td>0.4% dari total Bottom Ash</td>
                            <td>${formatNumber(totalFlyAsh)} Kg</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-circle text-info"></i> Flue Gas</td>
                            <td>1% dari total Fly Ash</td>
                            <td>${formatNumber(totalFlueGas)} Kg</td>
                        </tr>
                    `;
                    }

                    new bootstrap.Modal(document.getElementById('detailDiolahModal')).show();
                })
                .catch(error => {
                    alert('Gagal mengambil data detail!');
                    console.error(error);
                });
        }

        let globalDetailData = [];

        function showDetailLimbahDiolah(mesin_id, no_mesin) {
            currentMesinId = mesin_id; // Simpan mesin_id untuk digunakan di export

            fetch(`/detaillimbahdiolah/${mesin_id}`)
                .then(res => res.json())
                .then(data => {
                    globalDetailData = data; // simpan data global untuk filter
                    document.getElementById('mesinTitle').textContent = no_mesin;
                    renderDetailTable(data);
                    new bootstrap.Modal(document.getElementById('detailDiolahModal')).show();
                })
                .catch(error => {
                    alert('Gagal mengambil data detail!');
                    console.error(error);
                });
        }

        function filterByMonth() {
            const selectedMonth = document.getElementById('filterBulan').value;
            const filteredData = selectedMonth ?
                globalDetailData.filter(item => item.tanggal_input.split('/')[1] === selectedMonth) :
                globalDetailData;
            renderDetailTable(filteredData);
        }

        function renderDetailTable(data) {
            const tbody = document.getElementById('detailDiolahTableBody');
            const resumeBody = document.getElementById('resumeTableBody');
            tbody.innerHTML = '';
            resumeBody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center">Tidak ada data ditemukan</td></tr>`;
                resumeBody.innerHTML = `<tr><td colspan="3" class="text-center">Tidak ada data residu</td></tr>`;
            } else {
                let totalBottomAsh = 0;
                let totalFlyAsh = 0;
                let totalFlueGas = 0;

                data.forEach((item, index) => {
                    totalBottomAsh += parseFloat(item.bottom_ash);
                    totalFlyAsh += parseFloat(item.fly_ash);
                    totalFlueGas += parseFloat(item.flue_gas);

                    const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.limbah_diolah_id}</td>
                        <td>${item.kode_limbah.kode} (${item.kode_limbah.deskripsi})</td>
                        <td>${item.berat_kg}</td>
                        <td>${item.tanggal_input}</td>
                        <td class="text-center">${item.bottom_ash} Kg</td>
                        <td class="text-center">${item.fly_ash} Kg</td>
                        <td class="text-center">${item.flue_gas} Kg</td>
                    </tr>
                `;
                    tbody.innerHTML += row;
                });

                function formatNumber(num) {
                    return num == parseInt(num) ? parseInt(num).toString() : parseFloat(num.toFixed(4)).toString();
                }

                resumeBody.innerHTML += `
                <tr>
                    <td><i class="fas fa-circle text-danger"></i> Bottom Ash</td>
                    <td>2% dari total limbah diolah</td>
                    <td>${formatNumber(totalBottomAsh)} Kg</td>
                </tr>
                <tr>
                    <td><i class="fas fa-circle text-warning"></i> Fly Ash</td>
                    <td>0.4% dari total Bottom Ash</td>
                    <td>${formatNumber(totalFlyAsh)} Kg</td>
                </tr>
                <tr>
                    <td><i class="fas fa-circle text-info"></i> Flue Gas</td>
                    <td>1% dari total Fly Ash</td>
                    <td>${formatNumber(totalFlueGas)} Kg</td>
                </tr>
            `;
            }
        }
    </script>
@endpush
