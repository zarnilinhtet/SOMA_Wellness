<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Soma Wellness studio</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.png') }}" type="image/x-icon" />

  <!-- Fonts and icons -->

  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["assets/css/fonts.min.css"],
      },
      active: function () {
        sessionStorage.fonts = true;
      },
    });
  </script>
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />

  <!-- Select2 JS (Requires jQuery) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
  <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script> -->
  <!-- Replace bootstrap.min.js with bootstrap.bundle.min.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>