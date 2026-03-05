<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets/images/favicon.png') }}">
    <title>@yield('title', 'Admin')</title>

    <!-- CSS -->
    <link href="{{ asset('admin/assets/libs/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/dist/css/style.min.css') }}" rel="stylesheet">
</head>

<body>
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<div id="main-wrapper"
     data-navbarbg="skin6"
     data-theme="light"
     data-layout="vertical"
     data-sidebartype="full"
     data-boxed-layout="full">

    @include('admin.layouts.header')

    @include('admin.layouts.menu-left')

    <div class="page-wrapper">

        @yield('content')

        @include('admin.layouts.footer')
    </div>
</div>

<!-- JS -->
<script src="{{ asset('admin/assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/assets/extra-libs/sparkline/sparkline.js') }}"></script>
<script src="{{ asset('admin/dist/js/waves.js') }}"></script>
<script src="{{ asset('admin/dist/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('admin/dist/js/custom.min.js') }}"></script>

@stack('scripts')
</body>
</html>
