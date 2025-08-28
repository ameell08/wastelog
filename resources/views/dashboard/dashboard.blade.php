@extends('layouts.template', ['activeMenu' => 'dashboard'])

@section('title', $page->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Card Limbah Masuk -->
        <div class="col-lg-3 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $limbahmasuk }}</h3>
                    <p>Limbah Masuk (Hari Ini)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-biohazard"></i>
                </div>
            </div>
        </div>

        <!-- Card Limbah Diolah -->
        <div class="col-lg-3 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $limbahdiolah }}</h3>
                    <p>Limbah Diolah (Hari Ini)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-recycle"></i>
                </div>
            </div>
        </div>

        <!-- Card Sisa Limbah -->
        <div class="col-lg-3 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $sisalimbah }}</h3>
                    <p>Sisa Limbah</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>

        <!-- Card Residu Limbah -->
        <div class="col-lg-3 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalResiduLimbah, 2) }}</h3>
                    <p>Residu Limbah</p>
                </div>
                <div class="icon">
                    <i class="fa fa-random"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Gabungan  -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafik Limbah Masuk & Limbah Diolah</h3>
            <div class="d-flex justify-content-between align-items-center mb-0 flex-wrap gap-2 gap-sm-3">
                    <h6 id="tanggalDetail" class="fw-bold mb-0"></h6>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <!-- Filter Neraca -->
                        <div class="d-flex align-items-center me-3">
                            <select id="filterBulan" class="form-select form-select-sm me-2" style="width: 140px;">
                                <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                            </select>
                            <select id="filterTahun" class="form-select form-select-sm " style="width: 110px;">
                                @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button id="exportNeracaPdfBtn" class="btn btn-danger btn-sm border border-2 border-light"
                                    style="font-weight:bold; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <i class="fas fa-file-pdf"></i> Export Neraca PDF
                            </button>
                            <button id="exportNeracaBtn" class="btn btn-success btn-sm border border-2 border-light"
                                    style="font-weight:bold; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                <i class="fas fa-file-excel"></i> Export Neraca Excel
                            </button>
                        </div>
                    </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-wrapper">
                <canvas id="chartGabungan"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Chart Sumber -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Distribusi Berat Berdasarkan Sumber</h3>
            <div class="d-flex justify-content-end align-items-center">
                <div class="d-flex align-items-center">
                    <label for="filterKategori" class="form-label me-2 mb-0">Filter Kategori:</label>
                    <select id="filterKategori" class="form-select form-select-sm" style="width: 180px;">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriSumber as $kategori)
                            <option value="{{ $kategori }}">{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-wrapper-pie">
                <canvas id="chartSumber"></canvas>
            </div>
            <div id="noDataMessage" class="text-center text-muted" style="display: none;">
                <i class="fas fa-info-circle"></i> Tidak ada data untuk kategori yang dipilih
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Function untuk export neraca excel
    function exportNeracaExcel() {
        const bulan = document.getElementById('filterBulan').value;
        const tahun = document.getElementById('filterTahun').value;
        window.location.href = "{{ url('/dashboard/export_neraca_excel') }}/" + bulan + "/" + tahun;
    }

     // export neraca pdf
    function exportNeracaPdf() {
        const bulan = document.getElementById('filterBulan').value;
        const tahun = document.getElementById('filterTahun').value;
        window.location.href = "{{ url('/dashboard/export_neraca_pdf') }}/" + bulan + "/" + tahun;
    }

    document.getElementById('exportNeracaBtn').addEventListener('click', exportNeracaExcel);
    document.getElementById('exportNeracaPdfBtn').addEventListener('click', exportNeracaPdf);
    
    function exportPDF(type, bulanIndex) {
    let bulan = bulanIndex +1;
    if (type === 'masuk') {
        window.location.href = "{{ url('/dashboard/export_limbahmasuk_pdf') }}/" + bulan;
    } else if (type === 'diolah') {
        window.location.href = "{{ url('/dashboard/export_limbahdiolah_pdf') }}/" + bulan;
    }
}
function exportExcel(type, bulanIndex) {
    let bulan = bulanIndex + 1;
    if (type === 'masuk') {
        window.location.href = "{{ url('/dashboard/export_limbahmasuk_excel') }}/" + bulan;
    } else if (type === 'diolah') {
        window.location.href = "{{ url('/dashboard/export_limbahdiolah_excel') }}/" + bulan;
    }
}
    const labels = @json($bulan);

    const dataGabungan = {
        labels: labels,
        datasets: [{
            label: 'Limbah Masuk (kg)',
            data: @json($limbahMasukBulanan),
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        },
        {
            label: 'Limbah Diolah (kg)',
            data: @json($limbahDiolahBulanan),
            backgroundColor: 'rgba(255, 206, 86, 0.5)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        },
        {
            label: 'Sisa Limbah (kg)',
            data: @json($sisaLimbahBulanan),
            backgroundColor: 'rgba(220, 53, 69, 0.5)',
            borderColor: 'rgba(220, 53, 69, 1)',
            borderWidth: 1
        }
    ]
    };

let hideTooltipTimeout = null;
let tooltipEl = null;

function showTooltip() {
    if (tooltipEl) tooltipEl.style.opacity = 1;
}
function hideTooltip() {
    if (tooltipEl) tooltipEl.style.opacity = 0;
}

const ctx = document.getElementById('chartGabungan').getContext('2d');
const chartGabungan = new Chart(ctx, {
    type: 'bar',
    data: dataGabungan,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        rezizeDelay: 100,
        plugins: {
            tooltip: {
                enabled: false,
                external: function(context) {
                    let tooltipEl = document.getElementById('chartjs-tooltip');
                    if (!tooltipEl) {
                        tooltipEl = document.createElement('div');
                        tooltipEl.id = 'chartjs-tooltip';
                        tooltipEl.innerHTML = '<div></div>';
                        document.body.appendChild(tooltipEl);
                    }

                    const tooltipModel = context.tooltip;

                    // delay Hide
                    if (tooltipModel.opacity === 0) {
                        if (hideTooltipTimeout) clearTimeout(hideTooltipTimeout);
                        hideTooltipTimeout = setTimeout(() => {
                            tooltipEl.style.opacity = 0;
                        }, 1200); 
                        return;
                    }
        
                    if (hideTooltipTimeout) clearTimeout(hideTooltipTimeout);

                     if (tooltipModel.body) {
                        const title = tooltipModel.title[0] || '';
                        const bodyLines = tooltipModel.body.map(b => b.lines);
                        const dp = tooltipModel.dataPoints[0];
                        const datasetIndex = dp.datasetIndex;
                        const bulanIndex = dp.dataIndex;
                        
                        let buttonsHtml = '';
                        
                        if (datasetIndex === 0) {
                        
                            // Limbah Masuk
                        buttonsHtml = `
                            <hr style="margin:6px 0; border-color:#444;">
                            <button onclick="exportPDF('masuk', ${bulanIndex})" style="background:#fff; color:#d32f2f; border:none; padding:2px 6px; border-radius:3px; font-size:12px; margin-right:4px;">
                                <i class="fas fa-file-pdf" style="font-size:13px;"></i> PDF
                            </button>
                            <button onclick="exportExcel('masuk', ${bulanIndex})" style="background:#fff; color:#388e3c; border:none; padding:2px 6px; border-radius:3px; font-size:12px;">
                                <i class="fas fa-file-excel" style="font-size:13px;"></i> Excel
                            </button>`;
                        } else if (datasetIndex === 1) {
                            // Limbah Diolah
                        buttonsHtml = `
                            <hr style="margin:6px 0; border-color:#444;">
                            <button onclick="exportPDF('diolah', ${bulanIndex})" style="background:#fff; color:#d32f2f; border:none; padding:2px 6px; border-radius:3px; font-size:12px; margin-right:4px;">
                                <i class="fas fa-file-pdf" style="font-size:13px;"></i> PDF
                            </button>
                            <button onclick="exportExcel('diolah', ${bulanIndex})" style="background:#fff; color:#388e3c; border:none; padding:2px 6px; border-radius:3px; font-size:12px;">
                                <i class="fas fa-file-excel" style="font-size:13px;"></i> Excel
                            </button>`;
                        }

                       const innerHtml = `
                        <div style="
                            background:#222; color:#fff; padding:8px 12px; border-radius:6px;
                            box-shadow:0 2px 8px rgba(0,0,0,0.15); min-width:140px; font-size:13px;
                        ">
                            <div style="font-weight:bold; margin-bottom:4px;">${title}</div>
                            <div>${bodyLines.join('<br>')}</div>
                            ${buttonsHtml}
                        </div>`;
                    tooltipEl.querySelector('div').innerHTML = innerHtml;
                }

                    const position = context.chart.canvas.getBoundingClientRect();
                    tooltipEl.style.opacity = 1;
                    tooltipEl.style.position = 'absolute';
                    tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                    tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                    tooltipEl.style.pointerEvents = 'auto';
                    tooltipEl.style.zIndex = 9999;
                }
            }
        }
    }
});

window.addEventListener('resize', () => chartGabungan.resize());
if (window.$) {
    $(document).on('collapsed.lte.pushmenu expanded.lte.pushmenu', () => {
      setTimeout(() => chartGabungan.resize(), 150);
    });
  }

// Data untuk pie chart sumber
const dataSumber = @json($dataSumber);
const dataSumberDetail = @json($dataSumberDetail);
let chartSumber = null;

// Fungsi untuk membuat pie chart
function createPieChart(data) {
    const ctx = document.getElementById('chartSumber').getContext('2d');
    
    // Destroy chart yang sudah ada
    if (chartSumber) {
        chartSumber.destroy();
    }

    if (data.length === 0) {
        document.getElementById('noDataMessage').style.display = 'block';
        document.getElementById('chartSumber').style.display = 'none';
        return;
    }

    document.getElementById('noDataMessage').style.display = 'none';
    document.getElementById('chartSumber').style.display = 'block';

    // Generate warna untuk setiap slice
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
        '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
    ];

    const chartData = {
        labels: data.map(item => item.nama || item.nama_sumber),
        datasets: [{
            data: data.map(item => item.total_berat),
            backgroundColor: colors.slice(0, data.length),
            borderColor: '#fff',
            borderWidth: 2
        }]
    };

    chartSumber = new Chart(ctx, {
        type: 'pie',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 20,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value.toLocaleString() + ' kg (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Inisialisasi pie chart dengan semua data
createPieChart(dataSumber);

// Event listener untuk filter kategori
document.getElementById('filterKategori').addEventListener('change', function() {
    const selectedKategori = this.value;
    let filteredData;

    if (selectedKategori === '') {
        // Jika tidak ada filter, menampilkan data kategori
        filteredData = dataSumber;
    } else {
        // Jika di filter, menampilkan detail sumber dari kategori yang dipilih
        const kategoriData = dataSumber.find(item => item.kategori === selectedKategori);
        if (kategoriData && kategoriData.detail_sumber) {
            filteredData = kategoriData.detail_sumber;
        } else {
            filteredData = [];
        }
    }

    createPieChart(filteredData);
});

// Resize handler untuk pie chart
window.addEventListener('resize', () => {
    if (chartSumber) {
        chartSumber.resize();
    }
});

if (window.$) {
    $(document).on('collapsed.lte.pushmenu expanded.lte.pushmenu', () => {
        setTimeout(() => {
            if (chartSumber) {
                chartSumber.resize();
            }
        }, 150);
    });
}

</script>
<style>
  .chart-wrapper{
    position: relative;
    width: 100%;
    height: 320px;          
  }
  .chart-wrapper-pie{
    position: relative;
    width: 100%;
    height: 400px;          
  }
  @media (min-width: 768px){  
    .chart-wrapper{ height: 360px; } 
    .chart-wrapper-pie{ height: 450px; }
  }
  @media (min-width: 1200px){ 
    .chart-wrapper{ height: 420px; } 
    .chart-wrapper-pie{ height: 500px; }
  }
</style>

@endsection
