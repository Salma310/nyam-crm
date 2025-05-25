<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Nyam CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: url('{{ asset('bg-nyam.png') }}') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            z-index: 0;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card img {
            width: 90px;
            margin-bottom: 20px;
        }

        .login-card h2 {
            font-weight: 600;
            color: #e67e22;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .btn-login {
            background-color: #e67e22;
            color: #fff;
            border-radius: 12px;
            padding: 10px;
            width: 100%;
            font-weight: bold;
        }

        .btn-login:hover {
            background-color: #d35400;
        }

        .form-check-label {
            font-size: 0.9em;
        }

        @media screen and (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="overlay"></div>
    <div class="login-card">
        <img src="{{ asset('logo.png') }}" alt="Nyam Logo">
        <h2>Login ke Nyam CRM</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
            <input type="password" name="password" class="form-control" placeholder="Password" required>

            <div class="form-check text-start mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat Saya</label>
            </div>

            <button type="submit" class="btn btn-login">Masuk</button>
        </form>
    </div>
</body>

</html>
