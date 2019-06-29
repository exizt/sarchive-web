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
<link rel="stylesheet" href="/assets/lib/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/lib/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/iconfont/material-icons.css">
<link rel="stylesheet" href="/assets/lib/google-material-icons/after-material-icons.css">
<link rel="stylesheet" href="/assets/css/bs-callout.css">
<link rel="stylesheet" href="/assets/css/site-base.css">
<link rel="stylesheet" href="/assets/css/site-layouts.css">
<!-- ## scripts ## -->
<script src="/assets/lib/jquery/jquery-3.2.1.min.js"></script>
<!-- popper.JS : dropdown of bootstrap 을 위해 필요. (bootstrap 4.0.0 이후로 추가) -->
<script src="/assets/lib/bootstrap/required/4.0.0/popper/1.12.9/popper.min.js"></script>
<script src="/assets/lib/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="/assets/js/site-base.js"></script>
<!-- ## semi modules ## -->
<link rel="stylesheet" href="/assets/modules/scrolltop/scrolltop.css">
<script src="/assets/modules/scrolltop/scrolltop.js"></script>
<link rel="stylesheet" href="/assets/modules/sh-sidenav/sh-sidenav.css">
@stack('style-head') 
@stack('script-head')
<!--<script src="/assets/js/site-shortcut.js"></script>-->
<link rel="stylesheet" href="/assets/site/archive/archive.css">
<script src="/assets/js/shortcut-key-event.js"></script>
<script src="/assets/site/archive/archive.js"></script>
</head>
<script>
const SERVICE_URI = "/{{Request::path()}}";

$(document).ready(function(){
	activeNavMenuItem("#navbarNav","{{request()->segment(1)}}");
});
$(document).ready(function(){
    if (("standalone" in window.navigator) && window.navigator.standalone) {
      // For iOS Apps
      $('a').on('click', function(e){
        e.preventDefault();
        var new_location = $(this).attr('href');
        if (new_location != undefined && new_location.substr(0, 1) != '#' && $(this).attr('data-method') == undefined){
          window.location = new_location;
        }
      });
	}
});

document.onkeyup = shortcutKeyEvent;
</script>
<body>
	<header>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="{{ route($ROUTE_ID.'.index')}}"><i class="fa fa-superpowers fa-spin" aria-hidden="true"></i>&nbsp;&nbsp;S아카이브</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					@foreach ($nav as $item)
    					@if ($item->id == $parameters['categoryId'])
    					<li class="nav-item active"><a class="nav-link" href="{{ route($ROUTE_ID.'.index')}}?category={{ $item->id }}">{{ $item->name }}</a></li>
    					@else
    					<li class="nav-item"><a class="nav-link" href="{{ route($ROUTE_ID.'.index')}}?category={{ $item->id }}">{{ $item->name }}</a></li> 
    					@endif
					@endforeach
				</ul>
				<form class="form-inline my-2 my-lg-0" action="{{ route($ROUTE_ID.'.search')}}">
					<input class="form-control mr-sm-2 site-shortcut-key-f" type="search" placeholder="Search" aria-label="Search" name="q" value="{{ $parameters['q'] or ''}}">
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
				</form>
				<ul class="navbar-nav">
					<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink_My">
							<a class="dropdown-item site-shortcut-key-n site-shortcut-key-a" href="{{ route($ROUTE_ID.'.create') }}">글쓰기</a>
							@if( $ROUTE_ID == 'NormalArchives')
							<a class="dropdown-item" href="{{ route('archives.index')}}">개발 분류로 이동</a>
							@else
							<a class="dropdown-item" href="{{ route('NormalArchives.index')}}">일반 분류로 이동</a>
							@endif
							<a class="dropdown-item" href="/">도움말 (작업중)</a>
							<a class="dropdown-item" href="/admin/archiveCategory">카테고리 관리</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="/admin">사이트 관리</a>
							<a class="dropdown-item" href="/myservice">마이서비스</a> 
							<a class="dropdown-item" href="/">사이트로 이동</a> 
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
								class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;Logout</a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
						</div>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<div id="layoutBody">
		<div>@yield('content')</div>
		<!-- .container -->
	</div>
	<!-- .site-layout-page -->
	<footer>
		<div class="container-fluid text-right">
        	<p class="text-muted pt-5">© 2018 SH Hong. All rights reserved.</p>
        </div>
        <div class="scrolltop">
        	<div class="scrolltop-arrow"></div>
        </div>
	</footer>
</body>
</html>