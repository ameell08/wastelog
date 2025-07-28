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
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
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
                                        <td>{{ $item->total_kg }}</td>
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm">Detail</a>
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
@endsection
