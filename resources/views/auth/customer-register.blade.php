<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register Pelanggan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #faf5fc;
      font-family: 'Poppins', sans-serif;
    }

    .container {
      background: white;
      padding: 25px 30px;
      width: 600px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      border-radius: 0;
    }

    h2 {
      text-align: center;
      color: #4d4d4d;
      margin-bottom: 20px;
      font-size: 20px;
    }

    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px 20px;
    }

    label {
      display: block;
      font-size: 13px;
      margin-bottom: 5px;
      color: #444;
    }

    input {
      width: 100%;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 0;
      font-size: 13px;
      background-color: #f9f0f5;
      transition: border 0.3s;
    }

    input:focus {
      border-color: #b47bb3;
      outline: none;
    }

    .full-width {
      grid-column: 1 / 3;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #b47bb3;
      color: white;
      border: none;
      font-weight: bold;
      border-radius: 0;
      font-size: 14px;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: background 0.3s;
      margin-top: 10px;
    }

    button:hover {
      background-color: #a066a5;
    }

    .back-login {
      grid-column: 1 / 3;
      text-align: center;
      margin-top: 10px;
    }

    .back-login a {
      color: #6e6e6e;
      text-decoration: underline;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Daftar</h2>
    <form method="POST" action="{{ route('customer.register.submit') }}" autocomplete="off">
      @csrf
      <div>
        <label>Nama:</label>
        <input type="text" name="name" required autocomplete="off">
      </div>
      <div>
        <label>Email:</label>
        <input type="email" name="email" required autocomplete="off">
      </div>
      <div>
        <label>WhatsApp:</label>
        <input type="text" name="whatsapp" required autocomplete="off">
      </div>
      <div>
        <label>Password:</label>
        <input type="password" name="password" required autocomplete="new-password">
      </div>
      <div>
        <label>Konfirmasi Password:</label>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
      </div>
      <div></div> <!-- Spacer -->

      <div class="full-width">
        <button type="submit">Daftar</button>
      </div>
      <div class="back-login">
  <a href="{{ route('login') }}">Sudah punya akun? Login</a>
</div>
    </form>
  </div>
</body>
</html>
