<div class="modal fade" id="detailDiolahModal" tabindex="-1" aria-labelledby="detailDiolahLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="detailDiolahLabel">DETAIL LIMBAH DIOLAH</h5>
                <h5 class="ms-auto">Mesin <span id="mesinTitle">-</span></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
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
                                    <th>Total (Kg)</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody id="resumeTableBody">
                                <!-- akan diisi via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Keterangan Perhitungan Residu:</strong><br>
                        • <strong>Bottom Ash:</strong> 2% dari berat total limbah yang diolah<br>
                        • <strong>Fly Ash:</strong> 0.4% dari berat Bottom Ash<br>
                        • <strong>Flue Gas:</strong> 1% dari berat Fly Ash<br>
                        <em>Catatan: Semua persentase dihitung sesuai dengan presentase yang ditentukan.</em>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showDetailLimbahDiolah(mesin_id, no_mesin) {
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
                    resumeBody.innerHTML = `<tr><td colspan="3" class="text-center">Tidak ada data residu</td></tr>`;
                } else {
                    // Variables untuk menghitung total residu
                    let totalBottomAsh = 0;
                    let totalFlyAsh = 0;
                    let totalFlueGas = 0;

                    data.forEach((item, index) => {
                        // Semua limbah menghasilkan residu sesuai persentase yang ditentukan
                        const bottomAshDisplay = `${item.bottom_ash} Kg (2%)`;
                        const flyAshDisplay = `${item.fly_ash} Kg (0.4%)`;
                        const flueGasDisplay = `${item.flue_gas} Kg (1%)`;

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
                        return num == parseInt(num) ? parseInt(num).toString() : parseFloat(num.toFixed(4)).toString();
                    }

                    resumeBody.innerHTML += `
                        <tr>
                            <td><i class="fas fa-circle text-danger"></i> Bottom Ash</td>
                            <td>${formatNumber(totalBottomAsh)} Kg</td>
                            <td>2% dari total limbah diolah</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-circle text-warning"></i> Fly Ash</td>
                            <td>${formatNumber(totalFlyAsh)} Kg</td>
                            <td>0.4% dari total Bottom Ash</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-circle text-info"></i> Flue Gas</td>
                            <td>${formatNumber(totalFlueGas)} Kg</td>
                            <td>1% dari total Fly Ash</td>
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
</script>
@endpush
