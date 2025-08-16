<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <form id="formDeleteData">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-id" name="id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Hapus Data</h5>
                    <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
