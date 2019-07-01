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
				@if (Route::has('login'))
				<ul class="nav navbar-nav ml-auto">
					@if (Auth::check())
					<li class="nav-item dropdown"><a class="nav-link dropdown-toggle"
						href="#" id="navbarDropdownMenuLink_My" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">{{
							Auth::user()->name }}&nbsp;</a>
						<div class="dropdown-menu dropdown-menu-right"
							aria-labelledby="navbarDropdownMenuLink_My">
							<a class="dropdown-item" href="/archives">아카이브</a>
							<a class="dropdown-item" href="https://chosim.asv.kr/admin">관리 페이지</a>
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