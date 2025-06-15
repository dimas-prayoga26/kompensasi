<!DOCTYPE html>
<base href="{{ asset('admin') }}/">
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Dashboard - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
    <meta name="description" content="" />


    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />


    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />


    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />


    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    {{-- <link rel="stylesheet" href="assets/vendor/css/core.css" /> --}}
    <link rel="stylesheet" href="assets/css/demo.css" />


    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />




    <script src="assets/vendor/js/helpers.js"></script>



    <script src="assets/js/config.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>

  <body>

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin.layout.sidebar')


            <div class="layout-page">

                @include('admin.layout.navbar')


            <div class="content-wrapper">

            @yield('content')

            @include('admin.layout.footer')

            <div class="content-backdrop fade"></div>
            </div>

        </div>

      </div>


      <div class="layout-overlay layout-menu-toggle"></div>
    </div>




    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>



    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>


    <script src="assets/js/main.js"></script>


    <script src="assets/js/dashboards-analytics.js"></script>


    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
          @if (session('success'))
              Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: '{{ session('success') }}',
                  position: 'center',
                  showConfirmButton: false,
                  timer: 2000,
                  timerProgressBar: true,
              });
          @elseif (session('error'))
              Swal.fire({
                  icon: 'error',
                  title: 'Gagal!',
                  text: '{{ session('error') }}',
                  position: 'center',
                  showConfirmButton: false,
                  timer: 2000,
                  timerProgressBar: true,
              });
          @endif
      });

        document.addEventListener("DOMContentLoaded", function () {
          $('#btn-logout').on('click', function () {
              Swal.fire({
                  title: 'Logout',
                  text: 'Apakah Anda yakin ingin logout?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Ya, logout!',
                  cancelButtonText: 'Batal',
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
              }).then((result) => {
                  if (result.isConfirmed) {
                      $.ajax({
                          url: "{{ route('auth.logout') }}", // pastikan route ini terdaftar
                          type: 'POST',
                          headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          },
                          success: function (response) {
                              window.location.href = response.redirect_url;
                          },
                          error: function () {
                              Swal.fire('Gagal', 'Logout gagal dilakukan.', 'error');
                          }
                      });
                  }
              });
          });
      });

    </script>
    @yield('js')
  </body>
</html>
