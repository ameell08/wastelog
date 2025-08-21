@extends('layouts.template', ['activeMenu' => 'datapengirimanresidu'])

@section('content')
    <div class="content">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close-custom" data-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close-custom" data-dismiss="alert"
                        aria-label="Close">&times;</button>
                </div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Pengiriman Residu</h5>
                    <div class="card-tools">
                        <a href="{{ route('datapengirimanresidu.export_excel') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
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
                                    <a href="{{ route('datapengirimanresidu.index') }}"
                                        class="btn btn-outline-secondary btn-sm ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Total Berat (Kg)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengirimanResidu as $index => $item)
                                <tr>
                                    <td>{{ $pengirimanResidu->firstItem() + $index }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $totalBerat = $item->total_berat;
                                            echo $totalBerat == (int)$totalBerat ? 
                                                number_format($totalBerat, 0) : 
                                                rtrim(rtrim(number_format($totalBerat, 4), '0'), '.');
                                        @endphp
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm"
                                            onclick="showDetailByTanggal('{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d-m-Y') }}')">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data pengiriman residu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    {{ $pengirimanResidu->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    @include('admin2.detailPengirimanResidu')
@endsection

@push('scripts')
    <script>
        function showDetailByTanggal(tanggal) {
            fetch(`/detailpengirimanresidu-by-tanggal/${tanggal}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    const parts = data.tanggal.split('-');
                    const formatted = `${parts[0]}-${parts[1]}-${parts[2]}`;
                    document.getElementById('tanggalDetail').textContent = 'Tanggal: ' + formatted;

                    let tbody = document.getElementById('detailTableBody');
                    tbody.innerHTML = '';

                    data.data.forEach(item => {
                        let row = `
                        <tr>
                            <td>${item.plat_nomor}</td>
                            <td>${item.kode_limbah.kode} (${item.kode_limbah.deskripsi})</td>
                            <td>${item.tanggal_masuk}</td>
                            <td>${item.berat}</td>
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
