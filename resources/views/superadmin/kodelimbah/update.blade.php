<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-success">
      <form id="formEditData">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-id" name="id">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title">Edit Data</h5>
          <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Kode Limbah</label>
            <input type="text" id="edit-kode" name="kode" class="form-control" required>
          </div>
          <div class="form-group mt-2">
            <label>Deskripsi</label>
            <textarea id="edit-deskripsi" name="deskripsi" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Simpan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>
