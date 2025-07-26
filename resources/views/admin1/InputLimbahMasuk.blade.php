@extends('layouts.template', ['activeMenu' => 'inputlimbahmasuk'])

@section('content')
    <div class="content">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form Input Limbah -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Input Limbah Masuk</h5>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-excel"></i> Import Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('limbahmasuk.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>

                        <table class="table table-bordered" id="limbahTable">
                            <thead>
                                <tr>
                                    <th>Plat Nomor</th>
                                    <th>Kode Limbah</th>
                                    <th>Berat (Kg)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="detail[0][truk_id]" class="form-control" required>
                                            <option value="">Pilih Plat Nomor</option>
                                            @foreach ($truks as $truk)
                                                <option value="{{ $truk->id }}">{{ $truk->plat_nomor }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="detail[0][kode_limbah_id]" class="form-control" required>
                                            <option value="">Pilih Kode Limbah</option>
                                            @foreach ($kodeLimbahs as $kode)
                                                <option value="{{ $kode->id }}">{{ $kode->kode }} -
                                                    {{ $kode->deskripsi }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detail[0][berat_kg]" class="form-control" required
                                            min="1">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" id="addRow" class="btn btn-outline-primary mb-3">
                            <i class= "fas fa-plus me-1"> </i>Tambah Baris
                        </button>

                        <button type="submit" class="btn btn-warning mb-3">
                            <i class="fas fa-save me-1"></i> Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Import -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ url('/limbahmasuk/import_ajax') }}" method="POST" id="form-import"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Data Limbah Masuk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>Download Template</label>
                                <a href="{{ asset('template_limbah_masuk.xlsx') }}" class="btn btn-info btn-sm" download><i
                                        class="fa fa-file-excel"></i> Download</a>
                                <small id="error-file_limbah_masuk" class="error-text form-text text-danger"></small>
                            </div>
                            <div class="form-group mb-3">
                                <label>Pilih File</label>
                                <input type="file" name="file_limbah_masuk" id="file_limbah_masuk" class="form-control"
                                    required>
                                <small id="error-file_limbah_masuk" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowIndex = 1;

        document.getElementById('addRow').addEventListener('click', () => {
            const tbody = document.querySelector('#limbahTable tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
            <td>
                <select name="detail[${rowIndex}][truk_id]" class="form-control" required>
                    <option value="">Pilih Plat Nomor</option>
                    @foreach ($truks as $truk)
                        <option value="{{ $truk->id }}">{{ $truk->plat_nomor }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="detail[${rowIndex}][kode_limbah_id]" class="form-control" required>
                    <option value="">Pilih Kode Limbah</option>
                    @foreach ($kodeLimbahs as $kode)
                        <option value="{{ $kode->id }}">{{ $kode->kode }} - {{ $kode->deskripsi }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="detail[${rowIndex}][berat_kg]" class="form-control" required min="1">
            </td>
            <td>
                <button type="button" class="btn btn-danger remove-row">Hapus</button>
            </td>
        `;

            tbody.appendChild(newRow);
            rowIndex++;
        });

        document.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-row')) {
                        e.target.closest('tr').remove();
                    }
                    $(document).ready(function() {
                        $("#form-import").validate({
                            rules: {
                                file_limbah_masuk: {
                                    required: true,
                                    extension: "xlsx"
                                },
                            },
                            submitHandler: function(form) {
                                var formData = new FormData(form);
                                $.ajax({
                                    url: form.action,
                                    type: form.method,
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status) {
                                            $('#importModal').modal('hide');
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: response.message
                                            });
                                            // reload datatable jika ada
                                            // tableBarang.ajax.reload();
                                        } else {
                                            $('.error-text').text('');
                                            $.each(response.msgField, function(prefix,
                                            val) {
                                                $('#error-' + prefix).text(val[0]);
                                            });
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Terjadi Kesalahan',
                                                text: response.message
                                            });
                                        }
                                    }
                                });
                                return false;
                            },
                            errorElement: 'span',
                            errorPlacement: function(error, element) {
                                error.addClass('invalid-feedback');
                                element.closest('.form-group').append(error);
                            },
                            highlight: function(element) {
                                $(element).addClass('is-invalid');
                            },
                            unhighlight: function(element) {
                                $(element).removeClass('is-invalid');
                            }
                        });
                    });
    </script>
@endsection
