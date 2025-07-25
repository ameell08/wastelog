@extends('layouts.template', ['activeMenu' => 'inputlimbaholah'])

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
                <h5 class="card-title mb-0">Form Input Limbah Diolah</h5>
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
                                    <select name="detail[0][kode_limbah_id]" class="form-control" required>
                                        <option value="">Pilih Kode Limbah</option>
                                        @foreach ($kodeLimbah as $kode)
                                            <option value="{{ $kode->id }}">{{ $kode->kode }} - {{ $kode->deskripsi }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="detail[0][berat_kg]" class="form-control" required min="1">
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
                                        @if ($antrean['status'] == 'Segera Diproses')
                                            <span class="badge bg-danger">{{ $antrean['status'] }}</span>
                                        @else
                                            <span class="badge bg-primary">{{ $antrean['status'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada limbah dalam antrean</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    let rowIndex = 1;

    document.getElementById('addRow').addEventListener('click', function() {
        const tbody = document.querySelector('#limbahTable tbody');
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td>
                <select name="detail[${rowIndex}][mesin_id]" class="form-control" required>
                    <option value="">Pilih Mesin</option>
                    @foreach ($mesin as $m)
                        <option value="{{ $m->id }}">{{ $m->no_mesin }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="detail[${rowIndex}][kode_limbah_id]" class="form-control" required>
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
        `;

        tbody.appendChild(newRow);
        rowIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('remove-row') || e.target.closest('.remove-row'))) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endpush
