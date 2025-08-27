<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('sumber.import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Sumber Limbah</h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label>Pilih File Excel</label>
                    <input type="file" name="file_sumber" id="file_sumber" class="form-control" accept=".xlsx,.xls" required>
                    <small id="error-file_sumber" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </div>
    </form>
  </div>
</div>
