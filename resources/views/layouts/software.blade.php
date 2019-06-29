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
	<div style="background-color: #628AC7; padding: 20px;">
		<div class="container"
			style="padding-top: 2rem; padding-bottom: 1rem;">
			<h1>Softwares</h1>
			<p class="lead">소프트웨어, 플러그인 등</p>
		</div>
	</div>
	<div class="site-layout-page pb-5 pt-4">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 d-none d-lg-block sh-sidenav">
					<div class="d-flex justify-content-start">
						<div class="p-2">
							<h5 class="text-muted">Softwares</h5>
						</div>
						<div class="p-2 ml-auto">
							<i class="fa fa-angle-double-left sh-event-sidemenu-toggle"
								aria-hidden="true" style="display: none;"></i>
						</div>
					</div>
					<nav class="nav flex-column">
						<div class="sh-divider"></div>
						<h6 class="sh-nav-header">PC Software for Windows</h6>
						<a class="nav-link" href="/softwares/screencapture">화면 캡쳐</a> <a
							class="nav-link" href="/softwares/colorpicker">색상 코드 추출</a> <a
							class="nav-link" href="/softwares/torrent_file_cleaner">.Torrent
							파일 삭제</a>
						<div class="sh-divider"></div>
						<h6 class="sh-nav-header">jQuery Plugins</h6>
						<a class="nav-link" href="/softwares/jqueryplugin/layerpopup">레이어팝업</a>
						<a class="nav-link" href="/softwares/jqueryplugin/rollover">이미지 롤오버</a>
						<a class="nav-link" href="/softwares/jqueryplugin/inputimage">FileInput
							Image</a>
						<div class="sh-divider"></div>
						<h6 class="sh-nav-header">Android App</h6>
						<a class="nav-link" href="/android/loan-calculator-for-android">대출이자계산기</a>
						<a class="nav-link" href="/softwares/android/salary_calculator">연봉계산기</a>
						<a class="nav-link" href="/softwares/android/deviceinfo">장치정보보기</a>
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