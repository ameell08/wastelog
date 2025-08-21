@extends('layouts.template', ['activeMenu' => 'datalimbahmasuk'])

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
                    <button type="button" class="btn-close-custom" data-bs-dismiss="alert"
                        aria-label="Close">&times;</button>
                </div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Limbah Masuk</h5>
                    <div class="card-tools">
                        <a href="{{ route('datalimbahmasuk.export_excel') }}" class="btn btn-success btn-sm"><i
                                class="fas fa-file-excel"></i> Export Excel</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <form method="GET">
                                <label for="tanggal">Filter Tanggal:</label>
                                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                                    class="form-control mb-2">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                @if (request('tanggal'))
                                    <a href="{{ route('datalimbahmasuk.index') }}"
                                        class="btn  btn-outline-secondary btn-sm ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </form>
                        </div>

                    </div>

                    <table class="table table-bordered table-striped ">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Masuk</th>
                                <th>Berat (Kg)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($limbahMasuk as $index => $item)
                                <tr>
                                    <td>{{ $limbahMasuk->firstItem() + $index }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $item->total_kg }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm"
                                            onclick="showDetailByTanggal('{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}')">Detail</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $limbahMasuk->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    </div>
    @include('admin1.detail')
@endsection

@push('scripts')
    <script>
        function showDetailByTanggal(tanggal) {
            fetch(`/detaillimbahmasuk-by-tanggal/${tanggal}`)
                .then(res => res.json())
                .then(data => {
                    const parts = data.tanggal.split('-');
                    const formatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    document.getElementById('tanggalDetail').textContent = 'Tanggal: ' + formatted;

                    let tbody = document.getElementById('detailTableBody');
                    tbody.innerHTML = '';

                    data.data.forEach(item => {
                        let row = `
                        <tr>
                            <td>${item.plat_nomor}</td>
                            <td>${item.kode_limbah.kode} (${item.kode_limbah.deskripsi})</td>
                            <td>${item.berat_kg}</td>
                            <td>${item.kode_festronik && item.kode_festronik.trim() !== '' ? item.kode_festronik : '-'}</td>
                        </tr>
                    `;
                        tbody.innerHTML += row;
                    });

                    $('#detailModal').modal('show');
                })
                .catch(error => {
                    alert('Gagal mengambil detail. Pastikan server berjalan dan data tersedia.');
                    console.error(error);
                });
        }
    </script>

    <style>
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
