@extends('layouts.template', ['activeMenu' => 'inputpengirimanresidu'])

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

            <!-- Form Input Pengiriman Residu -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title mb-0">Form Input Pengiriman Residu</h5>
                </div>

                @if($truk->count() == 0)
                    <div class="alert alert-warning mx-3 mt-3" role="alert">
                        <h6><i class="fas fa-exclamation-triangle me-1"></i> Peringatan!</h6>
                        <p class="mb-0">Tidak ada truk yang terdaftar saat ini. Silakan hubungi administrator untuk menambahkan data truk terlebih dahulu.</p>
                    </div>
                @endif

                <div class="card-body">
                    @if($truk->count() == 0)
                        <div class="alert alert-danger" role="alert">
                            <h6><i class="fas fa-exclamation-circle me-1"></i> Peringatan!</h6>
                            <p class="mb-0">Tidak ada truk yang terdaftar saat ini. Silakan hubungi administrator untuk menambahkan data truk terlebih dahulu sebelum melakukan input pengiriman residu.</p>
                        </div>
                    @else
                        <form action="{{ route('pengiriman-residu.store') }}" method="POST" id="pengirimanForm">
                        @csrf
                        <div class="mb-3">
                            <label for="tanggal_pengiriman">Tanggal Pengiriman</label>
                            <input type="date" name="tanggal_pengiriman" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <table class="table table-bordered" id="pengirimanTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Truk</th>
                                    <th>Kode Limbah</th>
                                    <th>Berat (Kg)</th>
                                    <th>Stok Tersedia</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="detail[0][truk_id]" class="form-control" required>
                                            <option value="">Pilih Truk</option>
                                            @foreach ($truk as $t)
                                                <option value="{{ $t->id }}">{{ $t->plat_nomor }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="detail[0][kode_limbah_id]" class="form-control select2 kode-limbah-select" required data-row="0">
                                            <option value="">Pilih Kode Limbah</option>
                                        </select>
                                        <input type="hidden" name="detail[0][tanggal_masuk]" class="tanggal-masuk-hidden" data-row="0">
                                    </td>
                                    <td>
                                        <input type="number" name="detail[0][berat]" class="form-control berat-input" required
                                            min="0.0001" step="0.0001" data-row="0">
                                    </td>
                                    <td>
                                        <span class="badge bg-info stok-badge" data-row="0">0 kg</span>
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
                    @endif
                </div>
            </div>

            <!-- Antrean Residu -->
            <div class="card card-secondary card-outline mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Antrean Residu</h5>
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
                                @forelse($antreanResidu as $antrean)
                                    <tr>
                                        <td>{{ $antrean['kode_limbah'] }}</td>
                                        <td>{{ $antrean['sisa_berat'] }}</td>
                                        <td>{{ $antrean['tanggal_masuk'] }}</td>
                                        <td>
                                            @if ($antrean['hari_menunggu'] >= 7)
                                                <span class="badge bg-danger">{{ $antrean['hari_menunggu'] }} hari</span>
                                            @elseif ($antrean['hari_menunggu'] >= 3)
                                                <span class="badge bg-warning">{{ $antrean['hari_menunggu'] }} hari</span>
                                            @else
                                                <span class="badge bg-info">{{ $antrean['hari_menunggu'] }} hari</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($antrean['status'] == 'Prioritas')
                                                <span class="badge bg-danger">{{ $antrean['status'] }}</span>
                                            @else
                                                <span class="badge bg-primary">{{ $antrean['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada residu dalam antrean
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
@endsection

@push('js')
    <script>
        // Data antrean residu yang tersedia
        const antreanResiduData = @json($antreanResidu);
        
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih kode limbah",
                width: '100%',
            });

            let rowIndex = 1;

            // Populate initial kode limbah options
            updateKodeLimbahOptions(0);

            $('#addRow').on('click', function() {
                const tbody = $('#pengirimanTable tbody');
                const newRow = $(`
                <tr>
                    <td>
                        <select name="detail[${rowIndex}][truk_id]" class="form-control" required>
                            <option value="">Pilih Truk</option>
                            @foreach ($truk as $t)
                                <option value="{{ $t->id }}">{{ $t->plat_nomor }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="detail[${rowIndex}][kode_limbah_id]" class="form-control select2 kode-limbah-select" required data-row="${rowIndex}">
                            <option value="">Pilih Kode Limbah</option>
                        </select>
                        <input type="hidden" name="detail[${rowIndex}][tanggal_masuk]" class="tanggal-masuk-hidden" data-row="${rowIndex}">
                    </td>
                    <td>
                        <input type="number" name="detail[${rowIndex}][berat]" class="form-control berat-input" required min="0.0001" step="0.0001" data-row="${rowIndex}">
                    </td>
                    <td>
                        <span class="badge bg-info stok-badge" data-row="${rowIndex}">0 kg</span>
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

                // Update kode limbah options for new row
                updateKodeLimbahOptions(rowIndex);

                rowIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            // Handler untuk perubahan kode limbah
            $(document).on('change', '.kode-limbah-select', function() {
                const row = $(this).data('row');
                const kodeLimbahId = $(this).val();
                updateStokTersediaFifo(row, kodeLimbahId);
            });

            // Handler untuk input berat
            $(document).on('input', '.berat-input', function() {
                const row = $(this).data('row');
                validateBeratInput(row);
            });

            // Validasi form sebelum submit
            $('#pengirimanForm').on('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                $('.berat-input').each(function() {
                    const row = $(this).data('row');
                    const berat = parseFloat($(this).val()) || 0;
                    const stokText = $(`.stok-badge[data-row="${row}"]`).text();
                    const stok = parseFloat(stokText.replace(' kg', '')) || 0;

                    if (berat > stok) {
                        isValid = false;
                        errorMessage = `Berat yang diinput melebihi stok tersedia pada baris ${row + 1}`;
                        return false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
            });
        });

        function updateKodeLimbahOptions(row) {
            const select = $(`.kode-limbah-select[data-row="${row}"]`);
            select.empty().append('<option value="">Pilih Kode Limbah</option>');
            
            // Group antrean residu berdasarkan kode limbah
            const groupedResidu = {};
            antreanResiduData.forEach(residu => {
                if (!groupedResidu[residu.kode_limbah_id]) {
                    groupedResidu[residu.kode_limbah_id] = residu.kode_limbah;
                }
            });
            
            Object.keys(groupedResidu).forEach(kodeLimbahId => {
                select.append(`<option value="${kodeLimbahId}">${groupedResidu[kodeLimbahId]}</option>`);
            });

            if (select.hasClass('select2')) {
                select.trigger('change.select2');
            }
        }

        function updateTanggalMasukOptions(row, kodeLimbahId) {
            // Fungsi ini tidak digunakan lagi karena sistem FIFO otomatis
        }

        function updateStokTersediaFifo(row, kodeLimbahId) {
            const stokBadge = $(`.stok-badge[data-row="${row}"]`);
            const tanggalMasukHidden = $(`.tanggal-masuk-hidden[data-row="${row}"]`);
            
            if (kodeLimbahId) {
                // Cari residu tertua (FIFO) untuk kode limbah yang dipilih
                const filteredResidu = antreanResiduData.filter(residu => 
                    residu.kode_limbah_id == kodeLimbahId
                );
                
                if (filteredResidu.length > 0) {
                    // Ambil yang pertama (sudah diurutkan berdasarkan FIFO di controller)
                    const oldestResidu = filteredResidu[0];
                    
                    // Format angka tanpa trailing zero
                    const formatted = formatNumber(oldestResidu.sisa_berat_raw);
                    stokBadge.text(`${formatted} kg`);
                    stokBadge.removeClass('bg-info bg-warning').addClass('bg-success');
                    
                    // Set tanggal masuk secara otomatis
                    tanggalMasukHidden.val(oldestResidu.tanggal_masuk_raw);
                } else {
                    stokBadge.text('0 kg');
                    stokBadge.removeClass('bg-success bg-warning').addClass('bg-info');
                    tanggalMasukHidden.val('');
                }
            } else {
                stokBadge.text('0 kg');
                stokBadge.removeClass('bg-success bg-warning').addClass('bg-info');
                tanggalMasukHidden.val('');
            }
        }

        function validateBeratInput(row) {
            const beratInput = $(`.berat-input[data-row="${row}"]`);
            const stokText = $(`.stok-badge[data-row="${row}"]`).text();
            const stok = parseFloat(stokText.replace(' kg', '')) || 0;
            const berat = parseFloat(beratInput.val()) || 0;

            if (berat > stok) {
                beratInput.addClass('is-invalid');
                $(`.stok-badge[data-row="${row}"]`).removeClass('bg-success bg-info').addClass('bg-danger');
            } else {
                beratInput.removeClass('is-invalid');
                $(`.stok-badge[data-row="${row}"]`).removeClass('bg-danger bg-info').addClass('bg-success');
            }
        }

        function formatNumber(num) {
            // Konversi ke number dan format
            const number = parseFloat(num);
            
            // Jika bilangan bulat, tampilkan tanpa desimal
            if (number === Math.floor(number)) {
                return number.toString();
            }
            
            // Jika desimal, hilangkan trailing zero
            return number.toFixed(4).replace(/\.?0+$/, '');
        }
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

        .is-invalid {
            border-color: #dc3545;
        }

        .stok-badge {
            font-size: 0.9em;
        }
    </style>
@endpush
