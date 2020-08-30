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
<link rel="shortcut icon" href="/assets/images/shortcut.ico" />
<!-- ## styles ## -->
<link rel="stylesheet" href="/assets/lib/bootstrap/4.3.1-dark-theme/css/bootstrap.css">
<link rel="stylesheet" href="/assets/lib/font-awesome/font-awesome-5.9.0/css/fontawesome.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome/font-awesome-5.9.0/css/brands.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome/font-awesome-5.9.0/css/solid.min.css">
<!--<link rel="stylesheet" href="/assets/lib/font-awesome-4.7.0/css/font-awesome.min.css">-->
<link rel="stylesheet" href="/assets/lib/google-material-icons/iconfont/material-icons.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/after-material-icons.css">
<link rel="stylesheet" href="/assets/css/bs-callout.css">
<link rel="stylesheet" href="/assets/css/site-base.css">
<link rel="stylesheet" href="/assets/css/site-layouts.css">
<!-- ## scripts ## -->
<script src="/assets/lib/jquery/jquery-3.2.1.min.js"></script>
<!-- popper.JS : dropdown of bootstrap 을 위해 필요. (bootstrap 4.0.0 이후로 추가) -->
<script src="/assets/lib/bootstrap/required/4.3.1/popper/1.14.3/popper.min.js"></script>
<script src="/assets/lib/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="/assets/js/site-base.js"></script>
<!-- ## semi modules ## -->
<link rel="stylesheet" href="/assets/modules/scrolltop/scrolltop.css">
<script src="/assets/modules/scrolltop/scrolltop.js"></script>
<link rel="stylesheet" href="/assets/modules/sh-sidenav/sh-sidenav.css">
@stack('style-head') 
@stack('script-head')
<!--<script src="/assets/js/site-shortcut.js"></script>-->
<link rel="stylesheet" href="/assets/site/archive/archive.css">
<script src="/assets/js/core-func.js"></script>
<script src="/assets/js/shortcut-key-event.js"></script>
<script src="/assets/site/archive/archive.js"></script>
<script>
$(function(){
	ajaxHeaderNav()
})
function ajaxHeaderNav(){
	var profileId = wrapData($("body").data("profile"),1)
	$.getJSON("/archives/ajax_headerNav",{
		profile : profileId
	},function(data){
		$.each(data.list, function(i,item){
			var html = '<li class="nav-item"><a class="nav-link" href="/'
			+ profileId + '/archives?board='+item.id+'">'+item.name+'</a></li>'
			$("#shh-header-navbar").append(html)
		})
	})
}
</script>
</head>
<script>
const SERVICE_URI = "/{{Request::path()}}";
document.onkeyup = shortcutKeyEvent;
</script>
<body @isset($parameters) @foreach ($parameters as $k => $v) data-{{$k}}="{{$v}}" @endforeach @endisset >
	<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			@isset($parameters['profile']) 
			<a class="navbar-brand" href="/{{$parameters['profile']}}/archives"><i class="fab fa-superpowers fa-spin" aria-hidden="true"></i>&nbsp;&nbsp;S아카이브</a>
			@else
			<a class="navbar-brand" href="/"><i class="fab fa-superpowers fa-spin" aria-hidden="true"></i>&nbsp;&nbsp;S아카이브</a>
			@endisset
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto" id="shh-header-navbar"></ul>
				<form class="form-inline my-2 my-lg-0" action="{{ route('archives.search')}}">
					@isset($parameters['profile'])
						<input type="hidden" name="profile" value="{{ $parameters['profile']}}">
					@endisset
					<input class="form-control mr-sm-2 site-shortcut-key-f" type="search" placeholder="Search" aria-label="Search" name="q" value="{{ $parameters['q'] or ''}}">
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
				</form>
				<ul class="navbar-nav">
					<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink_My">
							<h6 class="dropdown-header">기능</h6>
							@isset($parameters['profile'])
							<a class="dropdown-item site-shortcut-key-n site-shortcut-key-a" href="{{ route('archives.create',['profile'=>$parameters['profile']]) }}">글쓰기</a>
							@endisset
							@isset($parameters['profile']) 
							<a class="dropdown-item" href="/page/단축키?profile={{$parameters['profile']}}">단축키 일람</a>
							@else
							<a class="dropdown-item" href="/page/단축키">단축키 일람</a>
							@endisset
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">프로필 선택</h6>
							<a class="dropdown-item" href="{{ route('archives.index',['profile'=>1])}}">개발 분류로 이동</a>
							<a class="dropdown-item" href="{{ route('archives.index',['profile'=>2])}}">일반 분류로 이동</a>
							<a class="dropdown-item" href="/">더보기</a>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">관리</h6>
							<a class="dropdown-item" href="/admin">설정</a>
							<a class="dropdown-item" href="{{ config('app.c_site_url','') }}">개인사이트로 이동</a> 
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
								class="fas fa-sign-out-alt" aria-hidden="true"></i>&nbsp;Logout</a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
						</div>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<div id="layoutBody">
		@yield('layout_header')
		@yield('content')
		@yield('layout_footer')
	</div>
	<!-- .site-layout-page -->
	<footer>
		<div class="container-fluid text-right">
        	<p class="text-muted pt-5">© SH Hong. All rights reserved.</p>
        </div>
        <div class="scrolltop">
        	<div class="scrolltop-arrow"></div>
        </div>
	</footer>
</body>
</html>