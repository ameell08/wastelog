<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('limbahdiolah.import') }}" method="POST" id="form-import" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Limbah Diolah</h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                @if(isset($mesin) && $mesin->count() == 0)
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Peringatan!</strong> Tidak ada mesin yang aktif saat ini. Import tidak dapat dilakukan.
                    </div>
                @else
                    <div class="form-group mb-3">
                        <label>Download Template</label>
                            <a href="{{ route('limbahdiolah.template') }}" class="btn btn-info btn-sm" download>
                            <i class="fas fa-file-excel"></i> Download
                            <small id="error-file_limbah_olah" class="error-text form-text text-danger"></small>
                        </a>
                    </div>
                <div class="form-group mb-3">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file_limbah_olah" id="file_limbah_olah" class="form-control" accept=".xlsx,.xls" required>
                        <small id="error-file_limbah_olah" class="error-text form-text text-danger"></small>
                    </div>
                @endif
                
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-secondary">Batal</button>
                @if(isset($mesin) && $mesin->count() > 0)
                    <button type="submit" class="btn btn-primary">Import</button>
                @endif
            </div>
        </div>
    </form>
  </div>
</div>
