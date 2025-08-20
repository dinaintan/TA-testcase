<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Upload File</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
  <style>
    .sign-up-form {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    .upload-form input[type="file"] {
      width: 100%;
    }
    .upload-form button {
      width: 100%;
    }
    #uploadForm .d-flex {
        align-items: center; 
    }
    
    #uploadForm .form-control {
        max-width: 600px !important; 
    }

    #uploadForm .btn {
      white-space: nowrap;
      width: auto;
      background-color: #3578a8;
      color: white;
      border: none;
      height: auto; 
      padding: .375rem .75rem; 
    }
  </style>
  </head>

<body class="index-page">
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center me-auto me-xl-0">
        </a>
      <nav id="navmenu" class="navmenu">
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>
  <main class="main">
    <section id="hero" class="hero section dark-background">
      <img src="{{ asset('assets/img/testcase.jpg') }}" alt="">
      <div class="container d-flex justify-content-center align-items-center" style="min-height: 50vh;">
        <div class="row w-100">
          <div class="col-lg-10 offset-lg-1 text-center">
            <h2 data-aos="fade-up" data-aos-delay="100">
              Aplikasi Penghasil Kasus Uji Otomatis dari Activity Diagram
            </h2>
            <p style="margin-bottom:20px;">
              Sebuah aplikasi web yang membantu menghasilkan kasus uji secara otomatis berdasarkan diagram aktivitas yang ditulis menggunakan sintaks PlantUML.
            </p>
            <form id="uploadForm" action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="d-flex justify-content-center align-items-center gap-2">
                <input type="file" name="puml_file" required class="form-control" style="max-width: 600px; flex-grow: 1;">
                <button type="submit" class="btn" style="white-space: nowrap; width: auto; background-color: #3578a8; color: white; border: none;">Proses</button>
              </div>
            </form>
            @if (session('success'))
              <div class="alert alert-success mt-3">
                {{ session('success') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>