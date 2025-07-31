<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - WasteLog </title>
    <link rel="icon" href="{{ asset('logo2.png') }}" type="image/png" />
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('{{ asset('img/background.jpg') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            color: white;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 28px;
        }

        .logo {
            width: 110px;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
        }

        button {
            width: 50%;
            min-width: 150px;
            max-width: 250px;
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            margin: 15px auto 0 auto;
            cursor: pointer;
            display: block;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: #ffaaaa;
            margin-top: 10px;
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

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Sign In</button>
        </form>
    </div>
</body>

</html>
