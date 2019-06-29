<script>
const SERVICE_URI = "/{{Request::path()}}";
@isset($ROUTE_ID) const ROUTE_ID = "{{$ROUTE_ID}}"; @endisset
</script>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
	<a class="navbar-brand" href="/admin"><i class="fa fa-gears" aria-hidden="true"></i>&nbsp;사이트 관리</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto" id="navSiteManager">
			<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 블로그 관리 </a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="{{ route('admin.post.index') }}">글 관리</a>
					<a class="dropdown-item" href="{{ route('admin.post.tags') }}">태그 목록</a>
				</div></li>
			<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 제품 관리</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="{{ route('admin.sku.index') }}">SKU 관리</a>
					<a class="dropdown-item" href="{{ route('admin.softwareManager.index') }}">소프트웨어 페이지 관리</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{ route('admin.archiveCategory.index') }}">S아카이브 카테고리 관리</a>
				</div></li>
			<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown3" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 서비스 관리</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown3">
					<a class="dropdown-item" href="{{ route('admin.isc_termMgmt.index') }}">실수령액 계산기</a>
				</div></li>
			<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 운영 관리 </a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="{{ route('admin.vers') }}">버전 정보</a>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header">운영 관련 링크</h6>
					<a class="dropdown-item" href="https://analytics.google.com/analytics/web/?hl=ko" target="_blank">구글 애널리틱스</a>
					<a class="dropdown-item" href="https://www.google.com/adsense/" target="_blank">구글 애드센스</a>
					<a
						class="dropdown-item" href="https://www.cloudflare.com" target="_blank">CloudFlare</a> 
						<a class="dropdown-item" href="https://www.google.com/webmasters/tools/home?hl=ko" target="_blank">Google 서치 콘솔</a>
					<div class="dropdown-divider"></div>
				</div></li>
		</ul>
		<ul class="nav navbar-nav ml-auto">
			<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}&nbsp;&nbsp;</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink_My">
					<a class="dropdown-item" href="{{ route('admin.post.create') }}">블로그 글 작성</a>
					<div class="dropdown-divider"></div> 				
					<a class="dropdown-item" href="/archives">아카이브</a>
					<a class="dropdown-item" href="/myservice">내 서비스</a> 
					<div class="dropdown-divider"></div> 
					<a class="dropdown-item" href="/">사이트로 이동</a> 
					<a class="dropdown-item"
						href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
				</div></li>
		</ul>
	</div>
</nav>
