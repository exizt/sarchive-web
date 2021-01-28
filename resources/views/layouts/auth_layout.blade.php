<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
@production
<title>S아카이브 :: @yield('title')</title>
@else 
<title>(local) S아카이브 :: @yield('title')</title>
@endproduction
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="S아카이브">
<link rel="shortcut icon" href="/assets/favicon/favicon-2021.ico" />
<!-- ## styles ## -->
<!--<link rel="stylesheet" href="/assets/lib/bootstrap/4.3.1-dark-theme/css/bootstrap.css">-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/darkly/bootstrap.min.css" integrity="sha512-A/WvCV75maYUI3F3yjeSqYg0dUIepPRx14Qw8EZjJ/udG5/s3uDWLHnm1FSbYzrJg4RLdAdEm/f6+1V6AxCBJQ==" crossorigin="anonymous" />
<!--<link rel="stylesheet" href="/assets/lib/bootstrap/4.3.1/css/bootstrap.min.css">-->
<link rel="stylesheet" href="/assets/css/archive.css">
</head>
<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-2">
			<a id="logo" class="navbar-brand mr-2" href="/"></a>
			<a class="navbar-brand" href="/">S아카이브</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		</nav>
	</header>
	<div id="layoutBody" class="py-5">
		@yield('content')
	</div>
	<footer>
		<div class="container-fluid text-right">
        	<p class="text-muted pt-5">© SH Hong. All rights reserved.</p>
        </div>
	</footer>
</body>
</html>