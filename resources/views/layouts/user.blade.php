<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - ArethaBeauty</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f6fa;
    }

    header {
      background-color: #fff;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e0e0e0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .brand {
      font-size: 26px;
      font-weight: bold;
      color: #e91e63;
      letter-spacing: 1px;
    }

    nav a {
      margin: 0 12px;
      text-decoration: none;
      color: #555;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: #e91e63;
    }

    .container {
      display: flex;
      height: calc(100vh - 80px);
    }

    .sidebar {
      width: 230px;
      background-color: #fff;
      padding: 30px 20px;
      border-right: 1px solid #e0e0e0;
      box-shadow: 2px 0 5px rgba(0,0,0,0.03);
    }

    .sidebar a {
      display: block;
      padding: 12px 18px;
      margin-bottom: 12px;
      border-radius: 8px;
      text-decoration: none;
      color: #444;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .sidebar a:hover {
      background-color: #ffe3ed;
      color: #e91e63;
    }

    .content {
      flex: 1;
      padding: 40px;
      overflow-y: auto;
    }

    .content h2 {
      color: #333;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #e0e0e0;
        box-shadow: none;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <header>
    <div class="brand">ArethaBeauty</div>
    <nav>
      <a href="{{ route('layananpelanggan') }}">Layanan</a>
      <a href="{{ route('kontakpelanggan') }}">Kontak</a>
      <a href="{{ route('caripelanggan') }}">Cari</a>
      <a href="{{ route('profilpelanggan') }}">Profil</a>
    </nav>
  </header>

  <div class="container">
    <div class="sidebar">
        <a href="{{ route('dashboardpelanggan') }}">Dashboard</a>
        <a href="{{ route('layananpelanggan') }}">Metode Layanan</a>
        <a href="{{ route('layananpelanggan') }}">Layanan</a>
    </div>

    <div class="content">
      @yield('content')
    </div>
  </div>

</body>
</html>
