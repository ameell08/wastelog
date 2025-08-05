<!-- Modal Detail -->    
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="detailModalLabel">Detail Limbah Masuk</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 id="tanggalDetail" class="fw-bold mb-0"></h6>
                        <button id="exportExcelBtn" class="btn btn-success btn-sm" onclick="exportDetailExcel()">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr>
                                <th>Plat Nomor</th>
                                <th>Kode Limbah (Deskripsi)</th>
                                <th>Berat (Kg)</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
    function exportDetailExcel() {
       const tanggalText = document.getElementById('tanggalDetail').innerText.trim();
       const tanggal = tanggalText.replace('Tanggal: ' , '').trim();
        if (!tanggal) {
            alert('Tanggal tidak ditemukan');
            return;
        }
        const url = `/detaillimbahmasuk/exportexceldetail/${tanggal}`;
        window.location.href = url;
    }
</script>