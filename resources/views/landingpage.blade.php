<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WasteLog - Sistem Limbah B3</title>
    <link rel="icon" href="{{ asset('logo2.png') }}" type="image/png" /> 
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #196D4B;
            --secondary-color: #FFA500;
            --accent-color: #feed7c;
            --text-color: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --shadow: rgba(0, 0, 0, 0.1);
            --gradient-primary: linear-gradient(135deg, #196D4B 0%, #22c55e 100%);
            --gradient-secondary: linear-gradient(135deg, #FFA500 0%, #ff8c00 100%);
        }

        [data-bs-theme="dark"] {
            --primary-color: #22c55e;
            --secondary-color: #fbbf24;
            --accent-color: #fef3c7;
            --text-color: #e5e7eb;
            --text-light: #9ca3af;
            --bg-light: #1f2937;
            --shadow: rgba(255, 255, 255, 0.1);
        }

        * {
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }

        .bg-header {
            background: linear-gradient(135deg, rgba(25, 109, 75, 0.9), rgba(34, 197, 94, 0.8)), 
                        url('img/background.jpg') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .bg-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .bg-header h1 {
            font-weight: 700;
            font-size: clamp(3rem, 8vw, 7rem);
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff, #feed7c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-login {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 18px;
            text-decoration: none;
            border: none;
            box-shadow: 0 8px 25px rgba(25, 109, 75, 0.3);
            transform: translateY(0);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(25, 109, 75, 0.4);
        }

        .dark-mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 12px 16px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dark-mode-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .fitur-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px 0;
            position: relative;
        }

        .fitur-header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-secondary);
        }

        .fitur-box {
            border-radius: 20px;
            border: 1px solid rgba(25, 109, 75, 0.1);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            position: relative;
        }

        [data-bs-theme="dark"] .fitur-box {
            background: var(--bg-light);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .fitur-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .fitur-box:hover::before {
            transform: scaleX(1);
        }

        .fitur-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px var(--shadow);
        }

        .fitur-icon {
            width: 56px;
            height: 56px;
            object-fit: contain;
            padding: 8px;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-radius: 16px;
            transition: transform 0.3s ease;
        }

        [data-bs-theme="dark"] .fitur-icon {
            background: linear-gradient(135deg, #1f2937, #374151);
        }

        .fitur-box:hover .fitur-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .text-justify {
            text-align: justify;
            font-size: 18px;
            padding-left: 50px;
            line-height: 1.8;
            color: var(--text-light);
        }

        h4.fw-bold {
            font-size: 2.5rem;
            color: var(--primary-color);
            padding-left: 50px;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
        }

        h4.fw-bold::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50px;
            width: 60px;
            height: 4px;
            background: var(--gradient-secondary);
            border-radius: 2px;
        }

        .logo-pria {
            position: absolute;
            top: 30px;
            left: 70px;
            z-index: 10;
            transition: transform 0.3s ease;
        }

        .logo-pria:hover {
            transform: scale(1.05);
        }
        .address-link:hover,
        .footer-text span:hover {
            color: #fbbf24 !important;
            text-decoration: none !important;
            transform: translateX(5px);
        }

        .address-link,
        .footer-text span {
            transition: all 0.3s ease;
        }

        .logo-pria img {
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
        }

        .section-about {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
        }

        [data-bs-theme="dark"] .section-about {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }

        .section-about::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(25, 109, 75, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .about-logo {
            transition: transform 0.3s ease;
            filter: drop-shadow(0 10px 25px rgba(25, 109, 75, 0.2));
        }

        .about-logo:hover {
            transform: scale(1.05) rotate(2deg);
        }

        .footer-modern {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .footer-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .footer-logo {
            transition: transform 0.3s ease;
        }

        .footer-logo:hover {
            transform: scale(1.05);
        }

        .footer-text {
            transition: color 0.3s ease;
        }

        .footer-text:hover {
            color: #fbbf24;
        }

        .copyright-bar {
            background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-selection {
            user-select: text;
            cursor: text;
        }

        .speaking-indicator {
            position: fixed;
            top: 80px;
            right: 20px;
            background: var(--gradient-primary);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            z-index: 1000;
            display: none;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .text-justify, h4.fw-bold {
                padding-left: 15px;
            }
            
            .logo-pria {
                left: 20px;
            }
            
            .bg-header {
                background-attachment: scroll;
            }
        }
    </style>
</head>

<body>
    <!-- Dark Mode Toggle -->
    <div class="dark-mode-toggle" onclick="toggleDarkMode()">
        <i class="fas fa-moon" id="theme-icon"></i>
    </div>

    <!-- Speaking Indicator -->
    <div class="speaking-indicator" id="speakingIndicator">
        <i class="fas fa-volume-up"></i> Membacakan teks...
    </div>

    <!-- Header -->
    <section class="bg-header text-center position-relative">
        <div class="logo-pria">
            <img src="img/ptpria.png" alt="Logo PRIA" style="height: 100px; width: auto;"
                onerror="this.style.border='2px solid red'">
        </div>
        <div class="container">
            <h1 class="display-4 fade-in text-selection">Waste Log</h1>
            <p class="lead mb-4 text-selection" style="font-size: 1.2rem; opacity: 0.9;">
                Sistem Manajemen Limbah B3 Modern & Terpercaya
            </p>
            <a href="/login" class="btn-login mt-3 d-inline-block">
                <i></i>Login
            </a>
        </div>
    </section>

    <!-- Tentang Waste Log -->
    <section class="py-5 section-about">
        <div class="container">
            <div class="row align-items-center fade-in">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <img src="img/logo.png" alt="Logo Waste Log" class="img-fluid about-logo" style="max-height: 200px;"
                        onerror="this.style.border='2px solid red'">
                </div>
                <div class="col-md-9">
                    <h4 class="fw-bold text-selection">Tentang Waste Log</h4>
                    <p class="text-justify text-selection" style="padding-top: 10px;">
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
        <div class="fitur-header text-center">
            <div class="container">
                <h3 class="fw-bold mb-0 text-selection">
                    <i class="fas fa-star me-2"></i>Fitur Utama
                </h3>
                <p class="mb-0 opacity-75 text-selection">Solusi lengkap untuk pengelolaan limbah B3</p>
            </div>
        </div>

        <!-- Konten Fitur -->
        <div class="container py-5">
            <div class="row gy-4">
                <div class="col-md-6 fade-in">
                    <div class="fitur-box d-flex p-4 shadow-sm">
                        <div class="fitur-icon me-4">
                            <img src="img/monitor.png" class="w-100 h-100" alt="Monitoring"
                                onerror="this.style.border='2px solid red'">
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-2 text-selection">
                                <i class="fas fa-chart-line me-1"></i>Monitoring Harian
                            </h6>
                            <p class="mb-0 text-selection">Memantau jumlah limbah secara real-time dengan dashboard interaktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 fade-in">
                    <div class="fitur-box d-flex p-4 shadow-sm">
                        <div class="fitur-icon me-4">
                            <img src="img/user.png" class="w-100 h-100" alt="Multi User"
                                onerror="this.style.border='2px solid red'">
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-2 text-selection">
                                <i class="fas fa-users me-1"></i>Akses Multi-User
                            </h6>
                            <p class="mb-0 text-selection">Bisa diakses operator, admin, dan pimpinan dengan role yang berbeda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 fade-in">
                    <div class="fitur-box d-flex p-4 shadow-sm">
                        <div class="fitur-icon me-4">
                            <img src="img/riwayat.png" class="w-100 h-100" alt="Riwayat"
                                onerror="this.style.border='2px solid red'">
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-2 text-selection">
                                <i class="fas fa-history me-1"></i>Riwayat Pengolahan
                            </h6>
                            <p class="mb-0 text-selection">Catatan lengkap limbah masuk dan pengolahan dengan tracking detail</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 fade-in">
                    <div class="fitur-box d-flex p-4 shadow-sm">
                        <div class="fitur-icon me-4">
                            <img src="img/dokumen.png" class="w-100 h-100" alt="Ekspor"
                                onerror="this.style.border='2px solid red'">
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-2 text-selection">
                                <i class="fas fa-file-export me-1"></i>Ekspor-Impor Laporan
                            </h6>
                            <p class="mb-0 text-selection">Export ke PDF dan Excel dengan template yang dapat dikustomisasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <img src="img/ptpria.png" alt="Logo PRIA" class="footer-logo" style="height: 80px;" 
                         onerror="this.style.border='2px solid red'">
                </div>
                <div class="col-md-9">
                    <div class="footer-content">
                        <h5 class="fw-bold mb-3 text-selection" style="color: #fbbf24;">
                            <i class="fas fa-building me-2"></i>PT PUTRA RESTU IBU ABADI
                        </h5>
                        <div class="footer-info">
                            <p class="footer-text mb-2 text-selection">
                                <i class="fas fa-map-marker-alt me-2 text-warning"></i>
                                <span class="address-link" onclick="openGoogleMaps()" style="cursor: pointer; ">
                                    Jl. Raya Lakardowo Kecamatan Jetis Kabupaten Mojokerto
                                </span>
                            </p>
                            <p class="footer-text mb-2 text-selection">
                                <i class="fas fa-phone me-2 text-warning"></i>
                                Telepon: (0321) 3612123671212
                            </p>
                            <p class="footer-text mb-2 text-selection">
                                <i class="fas fa-fax me-2 text-warning"></i>
                                Fax: (0321) 365322
                            </p>
                            <p class="footer-text mb-0 text-selection">
                                <i class="fas fa-envelope me-2 text-warning"></i>
                                Email: marketing@ptpria.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="copyright-bar py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-selection">
                            <i class="fas fa-copyright me-1"></i>
                            Copyright © 2025 WasteLog. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            const currentTheme = html.getAttribute('data-bs-theme');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-bs-theme', 'light');
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            html.setAttribute('data-bs-theme', savedTheme);
            themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });

        // Text-to-Speech functionality
        let currentSpeech = null;
        const speakingIndicator = document.getElementById('speakingIndicator');

        function speakText(text) {
            // Stop any current speech
            if (currentSpeech) {
                speechSynthesis.cancel();
            }

            // Clean the text
            const cleanText = text.replace(/[^\w\s\.\,\!\?]/g, '').trim();
            
            if (cleanText.length > 0) {
                currentSpeech = new SpeechSynthesisUtterance(cleanText);
                currentSpeech.lang = 'id-ID'; // Indonesian language
                currentSpeech.rate = 0.8;
                currentSpeech.pitch = 1;
                currentSpeech.volume = 0.8;

                // Show speaking indicator
                speakingIndicator.style.display = 'block';

                currentSpeech.onend = function() {
                    speakingIndicator.style.display = 'none';
                    currentSpeech = null;
                };

                currentSpeech.onerror = function() {
                    speakingIndicator.style.display = 'none';
                    currentSpeech = null;
                };

                speechSynthesis.speak(currentSpeech);
            }
        }

        // Text selection listener
        document.addEventListener('mouseup', function() {
            const selection = window.getSelection();
            const selectedText = selection.toString().trim();
            
            if (selectedText.length > 0) {
                speakText(selectedText);
            }
        });

        // Touch selection for mobile
        document.addEventListener('touchend', function() {
            setTimeout(function() {
                const selection = window.getSelection();
                const selectedText = selection.toString().trim();
                
                if (selectedText.length > 0) {
                    speakText(selectedText);
                }
            }, 100);
        });

        // Stop speech when clicking elsewhere
        document.addEventListener('click', function(e) {
            if (!window.getSelection().toString().trim()) {
                if (currentSpeech) {
                    speechSynthesis.cancel();
                    speakingIndicator.style.display = 'none';
                    currentSpeech = null;
                }
            }
        });

        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe fade-in elements
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                observer.observe(el);
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Enhanced error handling for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.style.border = '2px dashed #dc3545';
                    this.style.padding = '10px';
                    this.style.backgroundColor = '#f8f9fa';
                    this.style.color = '#dc3545';
                    this.style.fontSize = '12px';
                    this.alt = 'Image not found: ' + this.src.split('/').pop();
                });
            });
        });

        // Add loading animation
        window.addEventListener('load', function() {
            document.body.style.opacity = '1';
        });

        // Initialize page with loading state
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease-in-out';

        // Open Google Maps
        function openGoogleMaps() {
            const address = "Jl. Raya Lakardowo Kecamatan Jetis Kabupaten Mojokerto";
            const encodedAddress = encodeURIComponent(address);
            const googleMapsUrl = `https://www.google.com/maps/place/PT.+Putra+Restu+Ibu+Abadi/@-7.3743779,112.4609433,18.1z/data=!4m15!1m8!3m7!1s0x2e780f8fd26e5d85:0xcaed677435e3d5e7!2sJl.+Raya+Lakardowo,+Kec.+Jetis,+Kabupaten+Mojokerto,+Jawa+Timur+61352!3b1!8m2!3d-7.3722244!4d112.4604678!16s%2Fg%2F11w9yznxd3!3m5!1s0x2e780f90f0dfce41:0xa9eb8a525d70919!8m2!3d-7.3752169!4d112.4630775!16s%2Fg%2F11bv1cch0f?entry=ttu&g_ep=EgoyMDI1MDgwNC4wIKXMDSoASAFQAw%3D%3D`;
            window.open(googleMapsUrl, '_blank');
        }
    </script>

</body>

</html>
