<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
@if (!App::environment('local'))
<title>@yield('title') :: S아카이브</title>
@else 
<title>(local) @yield('title') :: S아카이브</title>
@endif
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="S아카이브">
<link rel="shortcut icon" href="/assets/brand/favicon/favicon-2021.ico" />
<!-- ## styles ## -->
<link rel="stylesheet" href="/assets/lib/bootstrap/4.3.1-dark-theme/css/bootstrap.css">
<link rel="stylesheet" href="/assets/lib/font-awesome/font-awesome-5.9.0/css/fontawesome.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome/font-awesome-5.9.0/css/brands.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome/font-awesome-5.9.0/css/solid.min.css">
</head>
<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<a class="navbar-brand" href="/"><i class="fab fa-superpowers fa-spin" aria-hidden="true"></i>&nbsp;&nbsp;S아카이브</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		</nav>
	</header>
	<div id="layoutBody" class="py-5">
		@yield('content')
	</div>
	<!-- .site-layout-page -->
	<footer>
		<div class="container-fluid text-right">
        	<p class="text-muted pt-5">© SH Hong. All rights reserved.</p>
        </div>
	</footer>
</body>
</html>