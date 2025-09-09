<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aretha Beauty - Landing Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #ffffff;
      color: #333;
      scroll-behavior: smooth;
    }

    .navbar {
      background-color: rgba(255, 255, 255, 0.9) !important;
      backdrop-filter: blur(8px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
      z-index: 1051;
    }

    .navbar-brand,
    .btn-outline-light {
        color: #b47bb3 !important;
        border-color: #b47bb3 !important;
    }

    .btn-outline-light:hover {
        background-color: #b47bb3 !important;
        color: white !important;
        border-color: #b47bb3 !important;
    }

    .nav-link, .dropdown-toggle {
      color: #2c2c2c !important;
      font-weight: 500;
    }

    .nav-link:hover, .dropdown-toggle:hover {
      color: #b47bb3 !important;
    }

    .carousel-item img {
      height: 500px;
      object-fit: cover;
      width: 100%;
      filter: brightness(0.85);
    }

    .intro-section {
      background: #ffffff;
      text-align: center;
      padding: 30px 20px;
    }

    .intro-section h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #000;
    }

    .service-section {
      background: #ffffff;
      padding: 30px 20px;
    }

    .service-card {
      transition: transform 0.3s;
      border-radius: 15px;
      overflow: hidden;
      background-color: #ffffff;
      border: none;
    }

    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .service-card img {
      height: 200px;
      object-fit: cover;
    }

    .card-body {
      background-color: #fafafa;
      padding: 20px;
    }

    .map-container iframe {
      width: 100%;
      height: 400px;
      border-radius: 10px;
      border: 1px;
    }

    footer {
      background-color: #f0f0f0;
      color: #2c2c2c;
      padding: 40px 0;
    }

    .social-icons a {
      font-size: 1.4rem;
      margin-left: 15px;
      color: #000;
      transition: color 0.3s;
    }

    .social-icons a:hover {
      color: #b47bb3;
    }

    .daftar-btn,
    .masuk-btn {
      background-color: #b47bb3;
      color: #fff;
      font-weight: 500;
      border: none;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .daftar-btn:hover,
    .masuk-btn:hover {
      box-shadow: 0 0 0 0.2rem rgba(180, 123, 179, 0.5);
      color: #fff;
      text-decoration: none;
    }

    .section-title {
      font-size: 2rem;
      font-weight: 700;
      color: #000;
      text-shadow: 3px 3px 10px rgba(125, 119, 119, 0.3);
    }

    /* Fade-in effect */
    .fade-in-section {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
      will-change: opacity, transform;
    }

    .fade-in-section.is-visible {
      opacity: 1;
      transform: none;
    }
  </style>
</head>
<body>

  <!-- Navbar Sticky -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">Aretha Beauty</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#services">Layanan Kami</a></li>
          <li class="nav-item"><a class="nav-link" href="#lokasi">Lokasi Kami</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Kontak Kami</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Carousel -->
  <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="{{ asset('images/landingpage/landingpage.jpg') }}" class="d-block w-100" alt="Aretha Beauty Front Image">
      </div>
    </div>
  </div>

  <!-- Overlay -->
  <div style="position:absolute; top:30%; left:5%; max-width:40%; padding:20px; border-radius:10px; color:#fff;">
    <h2 class="fw-bold">Temukan Kecantikan Anda Dengan Layanan Terbaik Kami</h2>
    <p>Kami menyediakan layanan kecantikan dan perawatan dengan kualitas terbaik!</p>
    <a href="/customer/register" class="btn daftar-btn px-4 py-2">Daftar</a>
    <a href="/login" class="btn daftar-btn px-4 py-2">Masuk</a>
  </div>

  <!-- Intro -->
  <div class="intro-section">
    <h1>Selamat Datang di Aretha Beauty</h1>
    <p>Tempat terbaik untuk perawatan kecantikan Anda. Kami menawarkan layanan Nail Art, Make Up, Henna, dan Lash Lift dengan profesional berpengalaman dan produk berkualitas tinggi.</p>
  </div>

  <!-- Services -->
  <div class="service-section text-center fade-in-section" id="services">
    <div class="container">
      <h2 class="mb-5">Layanan Kami</h2>
      <div class="row g-4">
        @foreach (['nail', 'makeup', 'henna', 'lashlift'] as $layanan)
        <div class="col-md-3">
          <div class="card service-card h-100 shadow-sm">
            <img src="{{ asset("images/landingpage/$layanan.jpg") }}" class="card-img-top" alt="{{ ucfirst($layanan) }}">
            <div class="card-body">
              <h5 class="card-title">{{ ucfirst($layanan === 'nail' ? 'Nail Art' : ($layanan === 'lashlift' ? 'Lash Lift' : $layanan)) }}</h5>
              <p class="card-text">
                @switch($layanan)
                  @case('nail') Desain kuku yang indah dan kreatif. @break
                  @case('makeup') Riasan profesional untuk acara spesial. @break
                  @case('henna') Lukisan henna elegan untuk acara adat. @break
                  @case('lashlift') Bulu mata lentik alami dan tahan lama. @break
                @endswitch
              </p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Lokasi -->
  <div class="container my-5 fade-in-section" id="lokasi">
    <h3 class="text-center mb-4">Lokasi Kami</h3>
    <div class="map-container">
      <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=114.155%2C-8.212%2C114.161%2C-8.208&layer=mapnik&marker=-8.210%2C114.158" allowfullscreen loading="lazy"></iframe>
    </div>
  </div>

  <!-- Footer -->
  <footer class="pt-5 fade-in-section" id="contact">
    <div class="container">
      <div class="row">
        <div class="col-md-6 mb-4">
          <h5>Aretha Beauty</h5>
          <p class="text-muted">Nail Art, Henna, Lash Lift, Make Up</p>
        </div>
        <div class="col-md-6 mb-4">
          <h5>Kontak Kami</h5>
          <ul class="list-unstyled text-muted">
            <li><i class="bi bi-envelope-fill me-2"></i>arethabeauty@gmail.com</li>
            <li><i class="bi bi-telephone-fill me-2"></i>085668077845</li>
            <li><i class="bi bi-geo-alt-fill me-2"></i>Jl. Ahmad Yani, Sumberasri, Songgon, Banyuwangi</li>
          </ul>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <p class="mb-0 text-muted">&copy; 2025 Aretha Beauty. All rights reserved.</p>
        <div class="social-icons">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Fade-in Scroll Effect -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const sections = document.querySelectorAll(".fade-in-section");

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
          }
        });
      }, { threshold: 0.1 });

      sections.forEach(section => {
        observer.observe(section);
      });
    });
  </script>
</body>
</html>
