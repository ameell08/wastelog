@extends('layouts.template', ['activeMenu' => 'inputlimbahmasuk'])

@section('content')
<div class="container small">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
                            @foreach($truks as $truk)
                                <option value="{{ $truk->id }}">{{ $truk->plat_nomor }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="detail[0][kode_limbah_id]" class="form-control" required>
                            <option value="">Pilih Kode Limbah</option>
                            @foreach($kodeLimbahs as $kode)
                                <option value="{{ $kode->id }}">{{ $kode->kode }} - {{ $kode->deskripsi }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="detail[0][berat_kg]" class="form-control" required min="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="addRow" class="btn btn-primary">Tambah Baris</button>
        <button type="submit" class="btn btn-secondary">Submit</button>
    </form>
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
                    @foreach($truks as $truk)
                        <option value="{{ $truk->id }}">{{ $truk->plat_nomor }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="detail[${rowIndex}][kode_limbah_id]" class="form-control" required>
                    <option value="">Pilih Kode Limbah</option>
                    @foreach($kodeLimbahs as $kode)
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

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection

