@extends('layouts.template')

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Limbah Diolah</h5>
                    <div class="card-tools">
                        <a href="{{ route('limbahdiolah.export') }}" class="btn btn-success btn-sm"><i
                                class="fas fa-file-excel"></i> Export Excel</a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter dengan Icon --}}
                    <form method="GET" action="{{ route('limbahdiolah.show') }}" class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa fa-filter"></i>
                                </span>
                                <select name="no_mesin" class="form-select" onchange="this.form.submit()">
                                    <option value="">No Mesin</option>
                                    @foreach ($mesinList as $mesin)
                                        <option value="{{ $mesin->no_mesin }}"
                                            {{ request('no_mesin') == $mesin->no_mesin ? 'selected' : '' }}>
                                            {{ $mesin->no_mesin }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (request('no_mesin'))
                            <a href="{{ route('limbahdiolah.show') }}" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Id</th>
                                    <th>Mesin</th>
                                    <th>Berat Total (Kg)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->mesin->no_mesin }}</td>
                                        <td>{{ number_format($item->total_kg, 2) }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                                onclick="showDetailLimbahDiolah({{ $item->mesin->id }}, '{{ $item->mesin->no_mesin }}')">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data tidak tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-3">
                            <small>Showing {{ count($data) }} entries</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin2.Detail')
@endsection
