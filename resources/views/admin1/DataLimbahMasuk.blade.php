@extends('layouts.template', ['activeMenu' => 'datalimbahmasuk'])

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
                                    <td>{{ \Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                                    <td>{{ $item->total_berat }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm"
                                            onclick="showDetail({{ $item->id }})">Detail</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $limbahMasuk->links() }}
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="detailModalLabel">Detail Limbah Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 id="tanggalDetail" class="mb-3 fw-bold"></h6>
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr>
                                <th>Plat Nomor</th>
                                <th>Kode Limbah (Deskripsi)</th>
                                <th>Berat (Kg)</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                            <!-- Data akan dimasukkan via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showDetail(id) {
            fetch(`/detail-limbah-masuk/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('tanggalDetail').textContent = 'Tanggal: ' + data.tanggal;

                    let tbody = document.getElementById('detailTableBody');
                    tbody.innerHTML = '';

                    data.data.forEach(item => {
                        let row = `
                        <tr>
                            <td>${item.truk.plat_nomor}</td>
                            <td>${item.kode_limbah.kode} (${item.kode_limbah.deskripsi})</td>
                            <td>${item.berat_kg}</td>
                        </tr>
                    `;
                        tbody.innerHTML += row;
                    });

                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                });
        }
    </script>
@endpush
