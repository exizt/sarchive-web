<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
@if (!App::environment('local'))
<title>@yield('title') :: 나의 서비스 - 언제나 초심</title>
@else 
<title>(local) @yield('title') :: 나의 서비스 - 언제나 초심</title>
@endif
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" href="/assets/images/shortcut.ico" />
<!-- ## styles ## -->
<link rel="stylesheet" href="/assets/lib/bootstrap/4.1.1/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/iconfont/material-icons.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/after-material-icons.css">
<link rel="stylesheet" href="/assets/css/bs-callout.css">
<link rel="stylesheet" href="/assets/css/site-base.css">
<link rel="stylesheet" href="/assets/css/site-layouts.css">
<!-- ## scripts ## -->
<script src="/assets/lib/jquery/jquery-3.2.1.min.js"></script>
<!-- popperJS is required for bootstrap dropdown -->
<script src="/assets/lib/bootstrap/required/4.1.1/popper/1.14.3/popper.min.js"></script>
<script src="/assets/lib/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="/assets/js/site-base.js"></script>
<!-- ## semi modules ## -->
<link rel="stylesheet" href="/assets/modules/scrolltop/scrolltop.css">
<script src="/assets/modules/scrolltop/scrolltop.js"></script>
<link rel="stylesheet" href="/assets/modules/sh-sidenav/sh-sidenav.css">
<script src="/assets/modules/sh-sidenav/sh-sidenav.js"></script>
@stack('style-head')
@stack('script-head')
<script src="/assets/js/site-myservice.js"></script>
</head>
<style>
div#layoutSubHeader {
	background-color: #478e99;
	padding: 20px;
	color: white;
	padding-top: 3rem;
	padding-bottom: 3rem;
}

div#layoutBody {
	
}
</style>
<body>
	<header>
		<script>
const SERVICE_URI = "/{{Request::path()}}";
</script>
		<div id="sh-header-nav">
			<nav class="navbar navbar-expand-md navbar-dark bg-dark">
				<a class="navbar-brand" href="/myservice"><i class="fa fa-superpowers fa-spin" aria-hidden="true"></i>&nbsp;MYSERV (내 전용)</a>

				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMyServiceTop" aria-controls="navbarMyServiceTop" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarMyServiceTop">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">연구실</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
								<h6 class="dropdown-header">테스트 중인 기능</h6>
								<a class="dropdown-item" href="{{ route('myservice.todo.index') }}">Todo</a> <a class="dropdown-item" href="{{ route('myservice.furniture.index') }}">가구 배치</a> <a class="dropdown-item" href="{{ route('myservice.resume.index') }}">이력서 관리</a>
							</div></li>

						<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">개발 지원 링크</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
								<h6 class="dropdown-header">자주 가는 곳</h6>
								<a class="dropdown-item" href="https://bitbucket.org" target="_blank">Bitbucket</a> <a class="dropdown-item" href="https://github.com/e2xist" target="_blank">Github</a> <a class="dropdown-item" href="https://stackoverflow.com/" target="_blank">StackOverflow</a>
								<div class="dropdown-divider"></div>
								<h6 class="dropdown-header">서포트</h6>
								<a class="dropdown-item" href="https://play.google.com/apps/publish/?dev_acc=12105670281132573859#AppListPlace" target="_blank">구글 Console</a> <a class="dropdown-item" href="https://itunesconnect.apple.com" target="_blank">iTunes 커넥트</a>
								<div class="dropdown-divider"></div>
								<h6 class="dropdown-header">기술 참고</h6>
								<a class="dropdown-item" href="https://jquery.com" target="_blank">jQuery</a> <a class="dropdown-item" href="https://v4-alpha.getbootstrap.com/" target="_blank">Bootstrap v4</a> <a class="dropdown-item" href="https://laravel.com/docs/5.4" target="_blank">Laravel Doc</a> <a
									class="dropdown-item" href="http://fontawesome.io/" target="_blank">FontAwesome</a> <a class="dropdown-item" href="https://material.io/icons" target="_blank">Material Icons</a> <a class="dropdown-item" href="http://l5.appkr.kr/" target="_blank">라라벨 5 입문 및 실전</a>
								<div class="dropdown-divider"></div>
							</div></li>
					</ul>
					<ul class="nav navbar-nav ml-auto">
						<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink_My">
								<a class="dropdown-item" href="/archives">아카이브</a> <a class="dropdown-item" href="/admin">사이트 관리</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="/">사이트로 이동</a> <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
							</div></li>
					</ul>
				</div>
			</nav>
		</div>
	</header>
	<div id="layoutSubHeader">
		<div class="container">
			<h1>@yield('title-layout','마이 서비스')</h1>
			<p class="lead">My Service</p>
		</div>
	</div>
	<div id="layoutBody">
		<div>@yield('content')</div>
		<!-- .container -->
	</div>
	<!-- .site-layout-page -->
	<footer>
		<div class="site-layout-footer pt-5 pb-3">
			<div class="container mt-5 text-right">
				<p class="text-muted">© 2017 SH Hong. All rights reserved.</p>
			</div>
		</div>
		<div class="scrolltop">
			<div class="scrolltop-arrow"></div>
		</div>
	</footer>
</body>
</html>