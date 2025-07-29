@extends('layouts.template', ['activeMenu' => 'dashboard'])

@section('title', $page->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Card Limbah Masuk -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $limbahmasuk }}</h3>
                    <p>Limbah Masuk (Hari Ini)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-biohazard"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Card Limbah Diolah -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $limbahdiolah }}</h3>
                    <p>Limbah Diolah (Hari Ini)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-recycle"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Card Sisa Limbah -->
        <div class="col-lg-4 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $sisalimbah }}</h3>
                    <p>Limbah Sisa (Hari Ini)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Grafik Limbah Masuk -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Limbah Masuk</h3>
        </div>
        <div class="card-body">
            <canvas id="chartMasuk" height="100"></canvas>
        </div>
    </div>

    <!-- Grafik Limbah Diolah -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar"></i> Limbah Diolah</h3>
        </div>
        <div class="card-body">
            <canvas id="chartDiolah" height="100"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($bulan);

    const dataMasuk = {
        labels: labels,
        datasets: [{
            label: 'Limbah Masuk (kg)',
            data: @json($limbahMasukBulanan),
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    const dataDiolah = {
        labels: labels,
        datasets: [{
            label: 'Limbah Diolah (kg)',
            data: @json($limbahDiolahBulanan),
            backgroundColor: 'rgba(255, 206, 86, 0.5)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    };

    new Chart(document.getElementById('chartMasuk'), {
        type: 'bar',
        data: dataMasuk,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('chartDiolah'), {
        type: 'bar',
        data: dataDiolah,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
