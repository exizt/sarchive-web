@extends('layouts.mainpage') 

@section('title','HelloWorld')

@section('content')
<script src="/assets/lib/jquery-plugins-my/jquery-rollover/jquery.rollover.js"></script>
<div class="py-5 d-none d-sm-block" style="background-color: #628AC7;">
	<div class="container py-5 text-center">
		<h1 class="display-1">Dev Site</h1>
		<h3>Softwares, Services</h3>
		<p class="lead">간단한 소프트웨어, 서비스, 프로그래밍 정보 등을 포함하고 있습니다.</p>
	</div>
</div>
<div class="d-sm-none" style="background-color: #628AC7;">
	<div class="container py-5 text-center">
		<h1 class="">Dev Site</h1>
		<h3>Softwares, Services</h3>
	</div>
</div>
<style>
.sh-container-cardlist-on{
	box-shadow: 3px 3px 10px #888888;
}
</style>
<script>
$(document).ready(function(){
	//$(".sh-container-softwares > .row").html("");
	onload_card_item();
});
$(function() {
	$(".sh-container-cardlist .card").rollOver({
		change : "class",
		over : "sh-container-cardlist-on"
	}).on("click",function(){
		var link = $(this).attr("data-link");
		location.href = link;
	});
});
function onload_card_item()
{
	var list = [
		{
			selector:".sh-container-softwares > .row",
			values:[
				{title:"스크린 캡쳐",link:"/softwares/screencapture"
					,descr:"스크린캡쳐를 할 수 있는 Windows 소프트웨어입니다.",image:"/assets/images/shscreencapture_thumb.png"},
				{title:"색상추출기",link:"/softwares/colorpicker"
					,descr:"스크린 색상 추출을 할 수 있는 Windows 어플리케이션입니다.",image:"/assets/images/software_preview_shcolorpickup_thumb.png"},
				{title:"토렌트찌거기제거",link:"/softwares/torrent_file_cleaner"
					,descr:"토렌트 이용시 생기는 불필요한 파일을 제거합니다.",image:"/assets/images/software_preview_shtorrentremove.png"},
				{title:"레이어팝업",link:"/softwares/jqueryplugin/layerpopup"
					,descr:"jquery 플러그인 입니다."},
				{title:"이미지 파일 인풋",link:"/softwares/jqueryplugin/inputimage"
					,descr:"jquery 플러그인 입니다."},
				{title:"롤오버",link:"/softwares/jqueryplugin/rollover"
					,descr:"jquery 플러그인 입니다."}
			]
		},
		{
			selector:".sh-container-calculators > .row",
			values:[
				{title:"대출이자계산기",link:"/services/loan_calculate",descr:"설명"},
				{title:"연봉계산기",link:"/services/salary_calculate",descr:"설명"},
				{title:"부동산 중개수수료 계산기",link:"/services/house_commission",descr:"설명"},
				{title:"퓨니코드 변환기",link:"/services/punycode",descr:"설명"},
				{title:"전기세 계산기",link:"/services/electricity_fee",descr:"설명"},
				{title:"평형 변환기",link:"/services/land_convert_calculator",descr:"설명"}
			]
		},
		{
			selector:".sh-container-services > .row",
			values:[
				{title:"본인 아이피 조회",link:"/services/ipservice",descr:"설명"},
				{title:"Excel-> query 변환",link:"/services/excel_to_query",descr:"설명"},
				{title:"Excel-> Mediawiki 변환 ",link:"/services/excel_to_mediawiki",descr:"설명"},
				{title:"Excel-> dokuwiki 변환",link:"/services/excel_to_dokuwiki",descr:"설명"}
			]
		}		
	];

	for(var i in list)
	{
		var html = "";

		for(var j in list[i].values){
			html += card_item(list[i].values[j]);
			//$(".sh-container-softwares > .row").append(html2).fadeIn("slow");
		}
		$(list[i].selector).html(html);
		var animateSlow = true;
		if(animateSlow){
			$(list[i].selector).find("div").each(function(j){
				$(this).delay(250*j+i*2000).fadeIn("slow");
				//$(this).show();
			});
		} else {
			$(list[i].selector).find("div").show();
		}
	}
}

function card_item(data)
{
	var html = "";
	var title = data.title;
	var descr = data.descr;
	html += '<div class="col-md-6 col-lg-4 col-xl-3 pb-3" style="display:none">';
	html += '<div class="card text-right" data-link="'+data.link+'">';
	if(typeof data.image !== "undefined"){
		html += '<img class="card-img-top p-2" src="'+ data.image +'" style="height:200px;">';
	} else {
		html += '<div class="card-body" style="height:200px; background-color:#aaa;color:#eee;">no image</div>';
	}
	html += 	'<div class="card-body">';
	html += 		'<h4 class="card-title"><a href="'+data.link+'" class="text-dark">'+title+'</a></h4>';
	html += 		'<p class="card-text">';
	html += 			'<small class="text-muted">'+descr+'</small>';
	html += 		'</p>';
	if(typeof data.link != 'undefined'){
		//html += 		'<a href="'+data.link+'" class="btn btn-outline-primary btn-block">바로가기</a>';
	}
	html += 	'</div>';
	html += '</div>';
	html +=  '</div>';
	return html;
}
</script>
<div class="py-5" style="background-color: #eee;">
	<div class="container">
		<div class="sh-container-cardlist sh-container-softwares mb-5 card-body">
			<h2 class="mb-2">Softwares</h2>
			<div class="row"></div>
		</div>

		<div class="sh-container-cardlist sh-container-calculators mb-5 card-body">
			<h2 class="mb-2">Calculators</h2>
			<div class="row"></div>
		</div>

		<div class="sh-container-cardlist sh-container-services mb-5 card-body">
			<h2 class="mb-2">Services</h2>
			<div class="row"></div>
		</div>
	</div>
</div>
@stop
