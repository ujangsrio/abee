<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - ArethaBeauty</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f6f3f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-box {
      background: white;
      padding: 40px;
      width: 400px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border-radius: 0;
    }

    .login-box h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #4d4d4d;
      font-size: 20px;
    }

    label {
      display: block;
      font-size: 13px;
      margin-bottom: 5px;
      color: #444;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 6px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 0;
      font-size: 13px;
      background-color: #f9f0f5 !important;
      transition: border 0.3s;
      color: #000;
    }

    input:focus {
      border-color: #b47bb3;
      outline: none;
    }

    input:-webkit-autofill {
      box-shadow: 0 0 0px 1000px #f9f0f5 inset !important;
      -webkit-text-fill-color: #000 !important;
    }

    button {
      width: 100%;
      background-color: #b47bb3;
      border: none;
      color: white;
      padding: 10px;
      border-radius: 0;
      font-weight: bold;
      cursor: pointer;
      font-size: 14px;
      margin-top: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      transition: background 0.3s;
    }

    button:hover {
      background-color: #a066a5;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
      text-align: center;
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
    }

    .register-link a {
      color: #6e6e6e;
      text-decoration: underline;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
      @csrf
      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>

      <div class="register-link">
        <a href="{{ route('customer.register') }}">Belum punya akun? Daftar</a>
      </div>
    </form>
  </div>
</body>
</html>
