<script>
const SERVICE_URI = "/{{Request::path()}}";
$(document).ready(function(){
	activeMenu("#navbarNav","{{request()->segment(1)}}");
});
/**
 * 현재 선택된 상태의 메뉴 를 active 처리
 */
function activeMenu(sel,value)
{
	$(sel).find(".item-choice").each(function(){
		if($(this).is("[data-item]"))
		{
			var item = $(this).attr("data-item");
			var check_result = false; 
			if(item.indexOf("|") > -1){
				var items = item.split("|");
				for (var k in items)
				{
					if(items[k]==value){
						check_result = true;
					}
				}
			} else {
				check_result = (item==value) ? true: false;
			}
			if(check_result){
				$(this).addClass("active");
			}
		}
	});
}

</script>
<div id="sh-header-nav">
	<nav
		class="navbar navbar-expand-md navbar-dark bg-dark sh-bg-nav">
		<div class="container">
			<a class="navbar-brand" href="/">언제나 초심</a>
			<button class="navbar-toggler navbar-toggler-right" type="button"
				data-toggle="collapse" data-target="#navbarNav"
				aria-controls="navbarNav" aria-expanded="false"
				aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item dropdown item-choice" data-item="software"><a
						class="nav-link dropdown-toggle" href="#"
						id="navbarDropdownMenuLink" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">Products</a>
						<div class="dropdown-menu"
							aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="/softwares">전체 보기</a>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">PC Software</h6>
							<a class="dropdown-item" href="/softwares/screencapture">스크린캡쳐
								프로그램</a> <a class="dropdown-item" href="/softwares/colorpicker">색상
								추출기</a> <a class="dropdown-item"
								href="/softwares/torrent_file_cleaner">Torrent 찌꺼기 제거</a>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">JQuery Plugins</h6>
							<a class="dropdown-item" href="/softwares/simpleone-popup-js">Simpleone popup.js</a>
							<a class="dropdown-item" href="/softwares/mouse-hover-js">Mouse hover.js</a>
							<a class="dropdown-item" href="/softwares/fileupload-imagebutton-js">FileUpload Image button.js</a>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">iOS App</h6>
							<a class="dropdown-item" href="/ios/korea-salary-income-calculator-for-ios"><i class="fa fa-mobile" aria-hidden="true"></i>실수령액 계산기</a>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">Android App</h6>
							<a class="dropdown-item"
								href="/softwares/salary-calculator-for-android"><i class="fa fa-mobile" aria-hidden="true"></i>실수령액 계산기</a>
							<a class="dropdown-item"
								href="/softwares/loan-calculator-for-android"><i class="fa fa-mobile" aria-hidden="true"></i>대출이자 계산기</a>
							<a class="dropdown-item" href="/softwares/device-information-for-android"><i class="fa fa-mobile" aria-hidden="true"></i>장치정보보기</a>
						</div></li>
					<li class="nav-item dropdown item-choice"
						data-item="service|information"><a
						class="nav-link dropdown-toggle" href="#"
						id="navbarDropdownMenuLink2" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">Services</a>
						<div class="dropdown-menu"
							aria-labelledby="navbarDropdownMenuLink2">
							<h6 class="dropdown-header">Calculators</h6>
							<a class="dropdown-item" href="/services/income-salary-calculator">실수령액 계산기</a>
							<a class="dropdown-item" href="/services/loan-calculator">대출이자계산기</a>
							<a class="dropdown-item" href="/services/brokerage-calculator-korea">부동산 중개수수료 계산기</a>
							<a class="dropdown-item" href="/services/punycode-converter">퓨니코드 변환기</a>
							<a class="dropdown-item" href="/services/electric-bill-calculator-korea">전기사용량 -> 전기세 계산</a>
							<a class="dropdown-item" href="/services/area-unit-converter">평형 변환기</a>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">Services</h6>
							<a class="dropdown-item" href="/services/ipservice">아이피 조회</a>
							<a class="dropdown-item" href="/services/excel_to_mediawiki">excel->mediawiki 변환기</a>
							<a class="dropdown-item" href="/services/excel_to_dokuwiki">excel->dokuwiki 변환기</a>
							<a class="dropdown-item" href="/services/excel_to_query">excel->query 변환기</a>
						</div></li>
					<li class="nav-item"><a class="nav-link" href="http://e2xist.tistory.com" target="_blank">Blog</a></li>						
				</ul>
				@if (Route::has('login'))
				<ul class="nav navbar-nav ml-auto">
					@if (Auth::check())
					<li class="nav-item dropdown"><a class="nav-link dropdown-toggle"
						href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">{{
							Auth::user()->name }}&nbsp;</a>
						<div class="dropdown-menu dropdown-menu-right"
							aria-labelledby="navbarDropdownMenuLink_My">
							<a class="dropdown-item" href="/myservice">내 서비스</a>
							<a class="dropdown-item" href="/archives">아카이브</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="/admin">관리 페이지</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="{{ route('logout') }}"
								onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
								class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
							<form id="logout-form" action="{{ route('logout') }}"
								method="POST" style="display: none;">{{ csrf_field() }}</form>
						</div></li> 
					@else
					<li class="nav-item"><a class="nav-link" href="{{ url('/login') }}"><i
							class="fa fa-sign-in fa-spin" aria-hidden="true"></i> Sign in</a></li>
					@endif
				</ul>
				@endif
			</div>
		</div>
	</nav>
</div>