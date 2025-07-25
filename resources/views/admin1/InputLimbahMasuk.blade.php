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

                        <button type="submit" class="btn btn-success mb-3">
                            <i class="fas fa-save me-1"></i> Submit
                        </button>
                    </form>
                </div>
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
        });
    </script>
@endsection
