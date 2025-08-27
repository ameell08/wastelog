@extends('layouts.template', ['activeMenu' => 'datasumber'])

@section('content')
    <div class="content">
        <div class="container-fluid px-0">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if (@session('status') || @session('error'))
                <script>
                    autoHideAlert();
                </script>
            @endif
            <!--import excel -->
            <div class="mb-3">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-file-import"></i> Import Excel
                </button>
            </div>

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Sumber Limbah</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#tambahModal">
                            Tambah Data <i class="fas fa-plus ms-1"></i>
                        </button>
                    </div>
                </div>
                <div id="success-alert" class="alert alert-success d-none"></div>

                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari nama sumber limbah atau kategori limbah...">
                    </div>
                    <table class="table table-bordered" id="sumberTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Sumber</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataSumber as $index => $item)
                                <tr id="row-{{ $item->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="namasumber">{{ $item->nama_sumber }}</td>
                                    <td class="kategori">{{ $item->kategori}}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit"
                                            data-id="{{ $item->id }}"
                                            data-namasumber="{{ $item->nama_sumber }}"
                                            data-kategori="{{ $item->kategori }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $item->id }}">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('superadmin.datasumber.create')
    @include('superadmin.datasumber.update')
    @include('superadmin.datasumber.delete')
    @include('superadmin.datasumber.import')
@endsection

@section('scripts')
    <script>
        // Pencarian
        $('#searchInput').on('keyup', function() {
            let keyword = $(this).val().toLowerCase();
            $('#sumberTable tbody tr').filter(function() {
                let namasumber = $(this).find('.namasumber').text().toLowerCase();
                let kategori = $(this).find('.kategori').text().toLowerCase();
                $(this).toggle(namasumber.includes(keyword) || kategori.includes(keyword));
            });
        });

        // Tambah
        $('#formTambahData').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();
            $.ajax({
                url: "{{ route('sumber.store') }}",
                method: "POST",
                data: formData,
                success: function(data) {
                    let index = $('#sumberTable tbody tr').length + 1;
                    $('#sumberTable tbody').append(`
                        <tr id="row-${data.id}">
                            <td>${index}</td>
                            <td class="namasumber">${data.nama_sumber}</td>
                            <td class="kategori">${data.kategori}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="${data.id}"
                                    data-namasumber="${data.nama_sumber}"
                                    data-kategori="${data.kategori}">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="${data.id}">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    `);
                    $('#tambahModal').modal('hide');
                    form[0].reset();
                    showAlert("Data berhasil ditambahkan");
                },
                error: function(xhr) {
                    let messages = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                    $('#form-errors').removeClass('d-none').html(messages);
                }
            });
        });

        // Edit
        $(document).on('click', '.btn-edit', function() {
            $('#edit-id').val($(this).data('id'));
            $('#edit-namasumber').val($(this).data('namasumber'));
            $('#edit-kategori').val($(this).data('kategori'));
            $('#editModal').modal('show');
        });

        $('#formEditData').submit(function(e) {
            e.preventDefault();
            let id = $('#edit-id').val();
            let formData = $(this).serialize();
            $.ajax({
                url: `/datasumber/${id}`,
                method: "POST",
                data: formData,
                success: function(data) {
                    let row = $(`#row-${id}`);
                    row.find('.namasumber').text(data.nama_sumber);
                    row.find('.kategori').text(data.kategori);

                    row.find('.btn-edit')
                        .attr('data-namasumber', data.nama_sumber)
                        .attr('data-kategori', data.kategori);

                    $('#editModal').modal('hide');
                    showAlert("Data berhasil diperbarui");
                }
            });
        });

        // Hapus
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            $('#delete-id').val(id);
            $('#deleteModal').modal('show');
        });

        $('#formDeleteData').submit(function(e) {
            e.preventDefault();
            let id = $('#delete-id').val();
            $.ajax({
                url: `/datasumber/${id}`,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $(`#row-${id}`).remove();
                    $('#deleteModal').modal('hide');
                    showAlert("Data berhasil dihapus");
                },
                error: function() {
                    alert('Gagal menghapus data.');
                }
            });
        });

        // Fungsi untuk menampilkan dan menghilangkan alert
        function showAlert(message) {
            $('#success-alert')
                .text(message)
                .removeClass('d-none')
                .fadeIn()
                .delay(2000)
                .fadeOut('slow', function() {
                    $(this).addClass('d-none');
                });
        }

        // Handle Import Form
        $('#form-import').submit(function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.text();
            
            // Disable submit button dan ubah text
            submitBtn.prop('disabled', true).text('Mengimpor...');
            
            // Clear previous errors
            $('.error-text').text('');
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#importModal').modal('hide');
                    showAlert('Data berhasil diimpor dari Excel!');
                    
                    // Reload halaman untuk menampilkan data baru
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            $('#error-' + key).text(messages[0]);
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert('Error: ' + xhr.responseJSON.message);
                    } else {
                        alert('Terjadi kesalahan saat mengimpor data.');
                    }
                },
                complete: function() {
                    // Re-enable submit button dan kembalikan text
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Auto-hide untuk alert dari session (server-side)
        $(document).ready(function() {
            if ($('.alert').length) {
                setTimeout(function() {
                    $('.alert').fadeOut('slow', function() {
                        $(this).addClass('d-none');
                    });
                }, 2000);
            }
        });
    </script>
@endsection
