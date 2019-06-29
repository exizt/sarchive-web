<!DOCTYPE html>
<html lang="ko">
<head>
@include('layouts.partials.head-metaclip')
@include('layouts.partials.head-resourceclip')
@stack('style-head')
@stack('script-head')
</head>
<body>
	<header>@include('layouts.partials.header')</header>
	@hasSection('layout-subheader-background')
	<div class="layout-subheader p-0" style="background-color: #478e99; padding: 20px; background-size: cover; 
		background-image: url('@yield('layout-subheader-background','')');background-position:center center">
		<div style="background-color: rgba(100, 100, 50, 0.2); width:100%; height:100%;">
			<div class="container py-5" style="color:white;">
				<h1 class="py-xl-5">@yield('layout-subheader-title','BLOG')</h1>
				<p class="lead">@yield('layout-subheader-description','Blog')</p>
			</div>
		</div>
	</div>
	@else 
	<div class="layout-subheader p-0" style="background-color: #478e99; padding: 20px;">
		<div class="container py-5" style="color:white;">
			<h1>@yield('layout-subheader-title','BLOG')</h1>
			<p class="lead">@yield('layout-subheader-description','Blog')</p>
		</div>
	</div>
	@endif
	<div class="mt-3 mb-5">
		<div>@yield('content')</div>
		<div class="container pt-3">@include('layouts.modules.adsense')</div>
	</div>
	<footer> @include('layouts.partials.footer') </footer>
</body>
@include('layouts.modules.analytics')
</html>