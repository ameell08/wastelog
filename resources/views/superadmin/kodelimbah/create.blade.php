<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <form id="formTambahData">
        @csrf
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">Tambah Data</h5>
          <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <div id="form-errors" class="alert alert-danger d-none"></div>
          <div class="form-group">
            <label>Kode Limbah</label>
            <input type="text" name="kode" class="form-control" required>
          </div>
          <div class="form-group mt-2">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
