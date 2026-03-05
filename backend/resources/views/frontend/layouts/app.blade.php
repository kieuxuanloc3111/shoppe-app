<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>@yield('title', 'E-Shopper')</title>

	<link href="{{ asset('frontend//css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/prettyPhoto.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/price-range.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/animate.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/main.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/responsive.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('frontend/css/rate.css') }}">

</head>

<body>

@include('frontend.layouts.header')

@yield('slider')

@yield('content')

@include('frontend.layouts.footer')

<script src="{{ asset('frontend/js/jquery.js') }}"></script>
<script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.scrollUp.min.js') }}"></script>
<script src="{{ asset('frontend/js/price-range.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.prettyPhoto.js') }}"></script>
<script src="{{ asset('frontend/js/main.js') }}"></script>
<!-- <script src="{{ asset('frontend/js/bootstrap-slider.js') }}"></script> -->
@yield('scripts')
</body>
</html>
