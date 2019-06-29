<?php /*
admin 페이지 이므로, resource 는 최대한 직접적으로 이용한다. CDN 에서 긁어오지 말 것.
*/?><!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
@if (!App::environment('local'))
<title>@yield('title') :: 사이트 관리</title>
@else 
<title>(local) @yield('title') :: 사이트 관리</title>
@endif
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" href="/assets/images/shortcut.ico" />
<!-- ###styles### -->
<link rel="stylesheet" href="/assets/lib/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/iconfont/material-icons.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/after-material-icons.css">
<link rel="stylesheet" href="/assets/css/bs-callout.css">
<link rel="stylesheet" href="/assets/css/site-base.css">
<link rel="stylesheet" href="/assets/css/site-layouts.css">
<!-- ###scripts### -->
<script src="/assets/lib/jquery/jquery-3.2.1.min.js"></script>
<!-- popper.JS : dropdown of bootstrap 을 위해 필요. (bootstrap 4.0.0 이후로 추가) -->
<script src="/assets/lib/bootstrap/required/4.3.1/popper/1.14.3/popper.min.js"></script>
<script src="/assets/lib/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="/assets/js/site-base.js"></script>
<!-- modules -->
<link rel="stylesheet" href="/assets/modules/scrolltop/scrolltop.css">
<script src="/assets/modules/scrolltop/scrolltop.js"></script>
<link rel="stylesheet" href="/assets/modules/sh-sidenav/sh-sidenav.css">
@stack('style-head')
@stack('script-head')
</head>
<body style="background-color: rgba(52,58,64,1.0);">
	<header style="background-color: rgba(52,58,64,1.0);">@include('layouts.partials.admin.header')</header>
	<!-- background-color: #478e99;color:white; -->
	<div class="container-fluid pt-2 pb-4" style="background-color: rgba(52,58,64,1.0); color: rgba(255,255,255,0.9);">
		<div class="">
			<h2>@yield('title-layout','DashBoard')</h2>
			<p class="lead"><em>SH.H Management System</em></p>
		</div>
	</div>
	<div style="background-color: white">
		<div>@yield('content')</div>
		<!-- .container -->
	</div>
	<!-- .site-layout-page -->
	<footer> @include('layouts.partials.admin.footer') </footer>
</body>
</html>