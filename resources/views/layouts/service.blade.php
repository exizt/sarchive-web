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
	<div style="background-color: #478e99; padding: 20px;">
		<div class="container"
			style="padding-top: 2rem; padding-bottom: 1rem;color:white;">
			<h1>Services</h1>
			<p class="lead">간단한 서비스들</p>
		</div>
	</div>
	<div class="site-layout-page pb-5 pt-4">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 d-none d-lg-block sh-sidenav">
					<div class="d-flex justify-content-start">
						<div class="p-2">
							<h5 class="text-muted">Services</h5>
						</div>
						<div class="p-2 ml-auto">
							<i class="fa fa-angle-double-left sh-event-sidemenu-toggle"
								aria-hidden="true" style="display: none;"></i>
						</div>
					</div>
					<nav class="nav flex-column">
						<div class="sh-divider"></div>
						<h6 class="sh-nav-header">Services</h6>
						<a class="nav-link" href="/services/ipservice">아이피조회</a> 
						<a class="nav-link" href="/services/excel_to_query">excel->query 변환</a>
						<a class="nav-link" href="/services/excel_to_mediawiki">excel->mediawiki 변환</a>
						<a class="nav-link" href="/services/excel_to_dokuwiki">excel->dokuwiki 변환</a> 
					</nav>
				</div>
				<div class="col-lg-9 sh-layout-page-inner">
					<div style="display: none;" class="sh-event-sidemenu-right">
						<i class="fa fa-angle-double-right sh-event-sidemenu-toggle"
							aria-hidden="true"></i>
					</div>
					<div>@yield('content')</div>
					<div class="pt-3">@include('layouts.modules.adsense')</div>
				</div>
			</div>
			<!-- .site-layout-page-inner -->
		</div>
		<!-- .container -->
	</div>
	<!-- .site-layout-page -->
	<footer> @include('layouts.partials.footer') </footer>
</body>
@include('layouts.modules.analytics')
</html>