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
	<div style="background-color: #34b599; padding: 20px;">
		<div class="container"
			style="padding-top: 2rem; padding-bottom: 1rem;">
			<h1>Calculators</h1>
			<p class="lead">간이 계산기 를 사용하실 수 있습니다.</p>
		</div>
	</div>
	<div class="site-layout-page pb-5 pt-4">
		<div class="container">
			<div class="row">
				<div class="col sh-layout-page-inner">
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