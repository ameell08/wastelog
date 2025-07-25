<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WasteLog - Sistem Limbah B3</title>
    <link rel="icon" href="{{ asset('logo2.png') }}" type="image/png" /> 
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .bg-header {
            background: url('img/background.jpg') no-repeat center center;
            background-size: cover;
            padding: 150px 0;
            color: #FFA500;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
        }

        .bg-header h1 {
            font-weight: bold;
        }

        .btn-login {
            background-color: #196D4B;
            color: #feed7c;
            font-weight: 500;
            padding: 8px 25px;
            border-radius: 5px;
            font-size: 17px;
            text-decoration: none;
        }

        .fitur-header {
            background-color: #e5e5e5;
        }

        .fitur-box {
            border-radius: 6px;
        }

        .fitur-icon {
            width: 48px;
            height: 48px;
            object-fit: contain
        }

        .text-justify {
            text-align: justify;
            font-size: 17px;
            padding-left: 50px;
        }

        h4.fw-bold {
            font-size: 35px;
            color: #196D4B;
            padding-left: 50px;
        }

        h1.display-4 {
            font-size: 6rem;
            font-weight: 600;
        }

        .logo-pria {
            position: absolute;
            top: 30px;
            left: 70px;
            z-index: 10;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <section class="bg-header text-center position-relative">
        <div class="logo-pria">
            <img src="img/ptpria.png" alt="Logo PRIA" style="height: 100px; width: auto;"
                onerror="this.style.border='2px solid red'">
        </div>
        <div class="container">
            <h1 class="display-4">Waste Log</h1>
            <a href="/login" class="btn-login mt-3 d-inline-block">Login</a>
        </div>
    </section>

    <!-- Tentang Waste Log -->
    <section class="py-5 bg-section-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="img/logo.png" alt="Logo Waste Log" class="img-fluid" style="max-height: 200px;"
                        onerror="this.style.border='2px solid red'">
                </div>
                <div class="col-md-9">
                    <h4 class="fw-bold">Tentang Waste Log</h4>
                    <p class="text-justify" style="padding-top: 10px;">
                        Waste Log adalah platform berbasis web yang dirancang untuk memantau jumlah limbah B3 (Bahan
                        Berbahaya dan Beracun) yang masuk dan diolah setiap harinya. Sistem ini membantu pengelola,
                        tim operasional, dan manajemen dalam memantau data limbah secara akurat dan efisien.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Utama -->
    <section class="fitur-utama-wrapper py-0">
        <!-- Header Fitur -->
        <div class="fitur-header py-1 px-4 text-center">
            <h5 class="fw-bold text-success mb-0">Fitur Utama</h5>
        </div>

        <!-- Konten Fitur -->
        <div class="container py-4">
            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="fitur-box d-flex p-3 shadow-sm bg-white">
                        <img src="img/monitor.png" class="fitur-icon me-3" alt="Monitoring"
                            onerror="this.style.border='2px solid red'">
                        <div>
                            <h6 class="fw-bold text-success mb-1">Monitoring Harian</h6>
                            <p class="mb-0">Memantau jumlah limbah secara real-time</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fitur-box d-flex p-3 shadow-sm bg-white">
                        <img src="img/user.png" class="fitur-icon me-3" alt="Multi User"
                            onerror="this.style.border='2px solid red'">
                        <div>
                            <h6 class="fw-bold text-success mb-1">Akses Multi-User</h6>
                            <p class="mb-0">Bisa diakses operator, admin, dan pimpinan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fitur-box d-flex p-3 shadow-sm bg-white">
                        <img src="img/riwayat.png" class="fitur-icon me-3" alt="Riwayat"
                            onerror="this.style.border='2px solid red'">
                        <div>
                            <h6 class="fw-bold text-success mb-1">Riwayat Pengolahan</h6>
                            <p class="mb-0">Catatan limbah masuk dan olah</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fitur-box d-flex p-3 shadow-sm bg-white">
                        <img src="img/dokumen.png" class="fitur-icon me-3" alt="Ekspor"
                            onerror="this.style.border='2px solid red'">
                        <div>
                            <h6 class="fw-bold text-success mb-1">Ekspor-Impor Laporan</h6>
                            <p class="mb-0">PDF dan Excel</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: #f2f2f2; padding-top: 20px; ">
        <div class="container" style="display: flex; align-items: left; gap: 60px; padding: 0 70px;">
            <img src="img/ptpria.png" alt="Logo PRIA" style="height: 80px;" onerror="this.style.border='2px solid red'">
            <div style="text-align:left" style="color: #333;">
                <p style="font-weight: bold; margin: 0px; color: #7e7b7b;">PT PUTRA RESTU IBU ABADI</p>
                <p style="margin: 0px; font-size: 13px; color: #a5a5a5;">Jl. Raya Lakardowo Kecamatan Jetis Kabupaten
                    Mojokerto</p>
                <p style="margin: 0px; font-size: 13px; color: #a5a5a5;">Telepon : (0321) 3612123671212</p>
                <p style="margin: 0px; font-size: 13px; color: #a5a5a5;">Fax : (0321) 365322</p>
                <p style="margin: 0px; font-size: 13px; margin-bottom: 15px; color: #a5a5a5;">Email :
                    marketing@ptpria.com</p>
            </div>
        </div>
        <div
            style="background-color: #1f4e3d; color: #fff; padding: 5px 5px; font-size: 12px; display: flex; justify-content: space-between;">
            <div>Copyright © 2025 WasteLog. All rights reserved.</div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
