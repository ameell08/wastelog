@extends('layouts.template', ['activeMenu' => 'kodelimbah'])

@section('content')
    <div class="content">
        <div class="container-fluid px-0 ">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    {{-- <button type="button" class="btn-close-custom" data-bs-dismiss="alert" aria-label="Close">&times;</button> --}}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    {{-- <button type="button" class="btn-close-custom" data-bs-dismiss="alert" aria-label="Close">&times;</button> --}}
                </div>
            @endif

            @if (session('success') || session('error'))
                <script>
                    autoHideAlert();
                </script>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Kode Limbah</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tambahModal">
                            Tambah Data <i class= " fas fa-plus me-1"></i>
                        </button>
                    </div>
                </div>
                <div id="success-alert" class="alert alert-success d-none"></div>

                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari kode limbah atau deskripsi limbah...">
                    </div>
                    <div class="table-wrap">
                        <table class="table table-bordered" id="kodeTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kodeLimbah as $index => $item)
                                    <tr id="row-{{ $item->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td class="kode">{{ $item->kode }}</td>
                                        <td class="deskripsi">{{ $item->deskripsi }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $item->id }}"
                                                data-kode="{{ $item->kode }}" data-deskripsi="{{ $item->deskripsi }}">
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
    </div>



    @include('superadmin.kodelimbah.create')
    @include('superadmin.kodelimbah.update')
    @include('superadmin.kodelimbah.delete')
@endsection

@section('scripts')
    <script>
        // Fitur Pencarian Baris Tabel
        // 🔍 Fitur Pencarian Baris Tabel
        $('#searchInput').on('keyup', function() {
            let keyword = $(this).val().toLowerCase();
            $('#kodeTable tbody tr').filter(function() {
                let kode = $(this).find('.kode').text().toLowerCase();
                let deskripsi = $(this).find('.deskripsi').text().toLowerCase();
                $(this).toggle(kode.includes(keyword) || deskripsi.includes(keyword));
            });
        });
        // Tambah Data
        $('#formTambahData').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = form.serialize();
            $.ajax({
                url: "{{ route('kode-limbah.store') }}",
                method: "POST",
                data: formData,
                success: function(data) {
                    let index = $('#kodeTable tbody tr').length + 1;
                    $('#kodeTable tbody').append(`
                    <tr id="row-${data.id}">
                        <td>${index}</td>
                        <td class="kode">${data.kode}</td>
                        <td class="deskripsi">${data.deskripsi}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit" data-id="${data.id}" data-kode="${data.kode}" data-deskripsi="${data.deskripsi}">Edit</button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="${data.id}">Hapus</button>
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

        // Tampilkan Modal Edit
        $(document).on('click', '.btn-edit', function() {
            $('#edit-id').val($(this).data('id'));
            $('#edit-kode').val($(this).data('kode'));
            $('#edit-deskripsi').val($(this).data('deskripsi'));
            $('#editModal').modal('show');
        });

        // Simpan Edit
        $('#formEditData').submit(function(e) {
            e.preventDefault();
            let id = $('#edit-id').val();
            let formData = $(this).serialize();
            $.ajax({
                url: `/kodelimbah/${id}`,
                method: "POST",
                data: formData,
                success: function(data) {
                    let row = $(`#row-${id}`);
                    row.find('.kode').text(data.kode);
                    row.find('.deskripsi').text(data.deskripsi);
                    row.find('.btn-edit').data('kode', data.kode).data('deskripsi', data.deskripsi);
                    $('#editModal').modal('hide');
                    showAlert("Data berhasil diperbarui.");
                },
                error: function(xhr) {
                    alert('Gagal menyimpan data edit.');
                }
            });
        });

        // Tampilkan Modal Hapus
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            $('#delete-id').val(id);
            $('#deleteModal').modal('show');
        });

        // Hapus Data
        $('#formDeleteData').submit(function(e) {
            e.preventDefault();
            let id = $('#delete-id').val();
            $.ajax({
                url: `/kodelimbah/${id}`,
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
    <style>
        /* Area scroll khusus untuk tabel */
        .table-wrap {
            height: 100vh;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            border: 1px solid #e9ecef;
            border-radius: .25rem;
            background: #fff;
        }

        .table-wrap thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }

        .table-wrap thead { 
            position: sticky; 
            top:0; 
            z-index: 50; 
        }
            
        .table-wrap table {
            margin-bottom: 0;
            min-width: 1000px;
        }
    </style>
@endsection
