@extends('layouts.template', ['activeMenu' => 'datamesin'])

@section('content')
    <div class="content">
        <div class="container-fluid px-0">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close-custom" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close-custom" data-bs-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            @if (@session('status') || @session('error'))
                <script>
                    autoHideAlert();
                </script>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Mesin</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#tambahModal">
                            Tambah Data <i class="fas fa-plus ms-1"></i>
                        </button>
                    </div>
                </div>
                <div id="success-alert" class="alert alert-success d-none"></div>

                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari no mesin atau status...">
                    </div>
                    <table class="table table-bordered" id="mesinTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No Mesin</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataMesin as $index => $item)
                                <tr id="row-{{ $item->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="nomesin">{{ $item->no_mesin }}</td>
                                    <td class="status">{{ $item->status }}</td>
                                    <td class="keterangan">{{ $item->keterangan }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit"
                                            data-id="{{ $item->id }}"
                                            data-nomesin="{{ $item->no_mesin }}"
                                            data-status="{{ $item->status }}"
                                            data-keterangan="{{ $item->keterangan }}">
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

    @include('superadmin.datamesin.create')
    @include('superadmin.datamesin.update')
    @include('superadmin.datamesin.delete')
@endsection

@section('scripts')
    <script>
        // Pencarian
        $('#searchInput').on('keyup', function() {
            let keyword = $(this).val().toLowerCase();
            $('#mesinTable tbody tr').filter(function() {
                let nomesin = $(this).find('.nomesin').text().toLowerCase();
                let status = $(this).find('.status').text().toLowerCase();
                $(this).toggle(nomesin.includes(keyword) || status.includes(keyword));
            });
        });

        // Tambah
        $('#formTambahData').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();
            $.ajax({
                url: "{{ route('mesin.store') }}",
                method: "POST",
                data: formData,
                success: function(data) {
                    let index = $('#mesinTable tbody tr').length + 1;
                    $('#mesinTable tbody').append(`
                        <tr id="row-${data.id}">
                            <td>${index}</td>
                            <td class="nomesin">${data.no_mesin}</td>
                            <td class="status">${data.status}</td>
                            <td class="keterangan">${data.keterangan ?? ''}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="${data.id}"
                                    data-nomesin="${data.no_mesin}"
                                    data-status="${data.status}"
                                    data-keterangan="${data.keterangan ?? ''}">
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
                    showAlert("Data berhasil ditambahkan.");
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
            $('#edit-nomesin').val($(this).data('nomesin'));
            $('#edit-status').val($(this).data('status'));
            $('#edit-keterangan').val($(this).data('keterangan'));
            $('#editModal').modal('show');
        });

        $('#formEditData').submit(function(e) {
            e.preventDefault();
            let id = $('#edit-id').val();
            let formData = $(this).serialize();
            $.ajax({
                url: `/datamesin/${id}`,
                method: "POST",
                data: formData,
                success: function(data) {
                    let row = $(`#row-${id}`);
                    row.find('.nomesin').text(data.no_mesin);
                    row.find('.status').text(data.status);
                    row.find('.keterangan').text(data.keterangan ?? '');

                    row.find('.btn-edit')
                        .attr('data-nomesin', data.no_mesin)
                        .attr('data-status', data.status)
                        .attr('data-keterangan', data.keterangan ?? '');

                    $('#editModal').modal('hide');
                    showAlert("Data berhasil diperbarui.");
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
                url: `/datamesin/${id}`,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    $(`#row-${id}`).remove();
                    $('#deleteModal').modal('hide');
                    showAlert("Data berhasil dihapus.");
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
