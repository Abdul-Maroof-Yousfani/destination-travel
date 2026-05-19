<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />

  <title>@yield('title') | EDESTINATIONS</title>
  <meta name="description" content="{{ config('variables.metaDescription') ?? '' }}" />
  <meta name="keywords" content="{{ config('variables.metaKeyword') ?? '' }}">

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ url('assets/images/favicon.png') }}" type="image/x-icon">
  {{-- <link rel="apple-touch-icon" href="{{ url('adminAssets/img/captrax_favicon.png') }}"> --}}

  <!-- Include Styles -->
  <link rel="stylesheet" href="{{ url('assets/css/layout.css') }}">
  <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css?family=Mulish:200,300,regular,500,600,700,800,900,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ url('assets/js/jquery.js') }}"></script>
  @yield('style')
</head>

<body>
  {{-- <x-loader :showOnLoad="request()->routeIs(['hotels.search', 'hotels.booking', 'flights', 'hotels.payment'])" /> --}}
  <x-loader :showOnLoad="true" />
  @include('home/layouts/navbar')
  @if (session('message'))
    <script>
      $(document).ready(function() {
        _alert("{{ session('message') }}", "{{ session('status') }}");
      });
    </script>
  @endif
  @if ($errors->any())
    <script>
      $(document).ready(function () {
        _alert("{{ $errors->first() }}", "error");
      });
    </script>
  @endif
  <!-- Layout Content -->
  @yield('content')
  <!--/ Layout Content -->

  <!-- Include Scripts -->
  @include('combine/scripts')
  @include('home/layouts/footer')
  @include('home/partials/scripts')
  @yield('script')

  {{-- WhatsApp Floating Button --}}
  <a href="https://wa.me/{{config('variables.contact.whatsapp')}}" target="_blank" id="whatsapp-float" title="Chat with us on WhatsApp" aria-label="Chat on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>

  <style>
    #whatsapp-float {
      position: fixed;
      bottom: 28px;
      right: 28px;
      z-index: 99999;
      width: 58px;
      height: 58px;
      background: #25d366;
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      box-shadow: 0 6px 24px rgba(37, 211, 102, 0.45);
      text-decoration: none;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    #whatsapp-float:hover {
      transform: scale(1.12);
      box-shadow: 0 10px 32px rgba(37, 211, 102, 0.6);
      color: #fff;
    }

    /* Pulse ring */
    #whatsapp-float::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: rgba(37, 211, 102, 0.4);
      animation: wa-pulse 2s ease-out infinite;
    }

    @keyframes wa-pulse {
      0%   { transform: scale(1);   opacity: 0.8; }
      70%  { transform: scale(1.55); opacity: 0; }
      100% { transform: scale(1.55); opacity: 0; }
    }
  </style>

</body>

</html>