<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - PRIA</title>
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
            width: 100px;
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
        }

        .remember {
            display: flex;
            align-items: center;
            color: #ccc;
            font-size: 14px;
            margin-top: 10px;
            justify-content: flex-start;
        }

        .remember input {
            margin-right: 8px;
        }

        button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            margin-top: 15px;
            cursor: pointer;
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

        @if($errors->has('login'))
            <div class="error">{{ $errors->first('login') }}</div>
        @endif

        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <label class="remember">
                <input type="checkbox" name="remember"> Remember Me
            </label>

            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
