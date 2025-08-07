<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - WasteLog </title>
    <link rel="icon" href="{{ asset('logo2.png') }}" type="image/png" />
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-image: url('{{ asset('img/background.jpg') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            color: white;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .logo {
            width: 110px;
            margin-bottom: 5px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .login-container h5 {
            margin-bottom: 30px;
            font-weight: 400;
            opacity: 0.9;
            font-size: 16px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #196D4B;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(25, 109, 75, 0.3);
            transform: translateY(-2px);
        }

        button {
            width: 60%;
            min-width: 150px;
            max-width: 250px;
            background: linear-gradient(135deg, #196D4B, #228B5A);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            margin: 20px auto 0 auto;
            cursor: pointer;
            display: block;
            font-size: 16px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(25, 109, 75, 0.3);
        }

        button:hover {
            background: linear-gradient(135deg, #145A3E, #1F7A4F);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(25, 109, 75, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: #ff6b6b;
            margin-top: 15px;
            background: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 6px;
            border-left: 4px solid #ff6b6b;
            font-size: 14px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .back-link {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e1e1;
        }

        .back-link a {
            color: #196D4B;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link a:hover {
            color: #145A3E;
            transform: translateX(-3px);
        }

        /* Loading animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s ease infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 25px;
            }
            
            .login-container h2 {
                font-size: 24px;
            }
            
            input[type="email"],
            input[type="password"] {
                padding: 10px 12px;
                font-size: 14px;
            }
            
            button {
                font-size: 14px;
                padding: 12px 18px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Selamat Datang</h2>
        <img src="{{ asset('img/ptpria.png') }}" alt="Logo PRIA" class="logo">
        <h5>Masukkan akun Anda untuk memulai sesi</h5>

        @if ($errors->has('login'))
            <div class="error">{{ $errors->first('login') }}</div>
        @endif

        <form method="POST" action="{{ route('login.authenticate') }}" id="loginForm">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" id="loginBtn">Sign In</button>
        </form>
        <div class="back-link">
                <a href="/">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>
    </div>

    <script>
        // Add loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.textContent = 'Signing In...';
            btn.disabled = true;
        });

        // Add input focus effects
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>