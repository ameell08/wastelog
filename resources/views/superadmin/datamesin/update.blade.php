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
                        <label>No Mesin</label>
                        <input type="text" id="edit-nomesin" name="no_mesin" class="form-control" required>
                    </div>

                    <div class="form-group mt-2">
                        <label>Status</label>
                        <select id="edit-status" name="status" class="form-control" required>
                            <option value="on">On</option>
                            <option value="off">Off</option>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label>Keterangan</label>
                        <textarea id="edit-keterangan" name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
