@extends('layouts.template', ['activeMenu' => 'inputlimbaholah'])

@section('content')
    <div class="content">
        <div class="container-fluid">

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

            <!-- Form Input Limbah -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Input Limbah Diolah</h5>
                    <div class="card-tools">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-excel me-1"></i> Import Excel
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('limbahdiolah.store') }}" method="POST" id="limbahForm">
                        @csrf
                        <div class="mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>

                        <table class="table table-bordered" id="limbahTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mesin</th>
                                    <th>Kode Limbah</th>
                                    <th>Berat (Kg)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="detail[0][mesin_id]" class="form-control" required>
                                            <option value="">Pilih Mesin</option>
                                            @foreach ($mesin as $m)
                                                <option value="{{ $m->id }}">{{ $m->no_mesin }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="detail[0][kode_limbah_id]" class="form-control select2" required>
                                            <option value="">Pilih Kode Limbah</option>
                                            @foreach ($kodeLimbah as $kode)
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
                                        <button type="button" class="btn btn-sm btn-danger remove-row">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" id="addRow" class="btn btn-outline-primary mb-3">
                            <i class="fas fa-plus me-1"></i> Tambah Baris
                        </button>

                        <button type="submit" class="btn btn-success mb-3">
                            <i class="fas fa-save me-1"></i> Submit
                        </button>
                    </form>
                </div>
            </div>

            <!-- Antrean Limbah -->
            <div class="card card-secondary card-outline mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Antrean Limbah</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="antreanTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Limbah (deskripsi)</th>
                                    <th>Sisa Berat (Kg)</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Lama Di Gudang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($antreanLimbah as $antrean)
                                    <tr>
                                        <td>{{ $antrean['kode'] }} - {{ $antrean['deskripsi'] }}</td>
                                        <td>{{ number_format($antrean['sisa_berat'], 2) }}</td>
                                        <td>{{ $antrean['tanggal_masuk'] }}</td>
                                        <td>
                                            @if ($antrean['hari_menunggu'] >= 2)
                                                <span class="badge bg-danger">{{ $antrean['hari_menunggu'] }} hari</span>
                                            @elseif ($antrean['hari_menunggu'] >= 1)
                                                <span class="badge bg-warning">{{ $antrean['hari_menunggu'] }} hari</span>
                                            @else
                                                <span class="badge bg-info">{{ $antrean['hari_menunggu'] }} hari</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($antrean['status'] == 'Prioritas')
                                                <span class="badge bg-danger">{{ $antrean['status'] }}</span>
                                            @elseif ($antrean['status'] == 'Segera Diproses')
                                                <span class="badge bg-warning">{{ $antrean['status'] }}</span>
                                            @else
                                                <span class="badge bg-primary">{{ $antrean['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada limbah dalam antrean
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('admin2.import')
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih kode limbah",
                width: '100%',
            });

            let rowIndex = 1;

            $('#addRow').on('click', function() {
                const tbody = $('#limbahTable tbody');
                const newRow = $(`
                <tr>
                    <td>
                        <select name="detail[${rowIndex}][mesin_id]" class="form-control" required>
                            <option value="">Pilih Mesin</option>
                            @foreach ($mesin as $m)
                                <option value="{{ $m->id }}">{{ $m->no_mesin }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="detail[${rowIndex}][kode_limbah_id]" class="form-control select2" required>
                            <option value="">Pilih Kode Limbah</option>
                        @foreach ($kodeLimbah as $kode)
                            <option value="{{ $kode->id }}">{{ $kode->kode }} - {{ $kode->deskripsi }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="detail[${rowIndex}][berat_kg]" class="form-control" required min="1">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">Hapus</button>
            </td>
            </tr>
            `);

                tbody.append(newRow);
                newRow.find('.select2').select2({
                    placeholder: "Pilih Kode limbah",
                    width: '100%'
                });

                rowIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>

    <style>
        /* Samakan tinggi Select2 dan input */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #696666 !important;
            color: #ffffff !important;
        }

        .btn-close-custom {
            position: absolute;
            top: 0.25rem;
            right: 0.5rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: center;
            color: #fff;
            opacity: 0.8;
        }

        .btn-close-custom:hover {
            opacity: 1;
        }
    </style>
@endpush
