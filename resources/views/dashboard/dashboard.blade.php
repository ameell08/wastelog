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
            <div class="d-flex justify-content-between align-items-center mb-0">
                    <h6 id="tanggalDetail" class="fw-bold mb-0"></h6>
                    <div class="d-flex">
                        <a href="{{url('/')}}" class=" btn btn-danger btn-sm me-2" >
                        <i class="fas fa-file-pdf"></i> Export Neraca PDF </a>
                    <button id="exportExcelBtn" class="btn btn-success btn-sm border border-2 border-light" style="font-weight:bold; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                        <a href="{{ url('/dashboard/export_limbahdiolah_excel') }}" >
                        <i class="fas fa-file-excel"></i> Export Excel</a>
                    </button>
                     </div>
                </div>
        </div>
        <div class="card-body">
            <canvas id="chartGabungan" height="100"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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

new Chart(document.getElementById('chartGabungan'), {
    type: 'bar',
    data: dataGabungan,
    options: {
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

                    // Jika tooltip muncul, batalkan timeout hide
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

</script>
@endsection
