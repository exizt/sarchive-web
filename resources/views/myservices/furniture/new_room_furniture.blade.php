<?php
/**
 * 캔버스 방식
 * 
 * 
 * 구성 방법
 * 게임처럼 구성을 하고, 배열 또는 오브젝트 로 구현 해야 할 듯 하다.
 * 아이템 을 추가/제거/변경 이 가능하게 해야 한다.
 * 아이템
 * - 색상 : bgcolor
 * - 이미지 선택 시 : bgimage
 * 
 * 
 */
?>
@extends('mypage.layouts.new')
@section('content')
<link rel="stylesheet"
	href="/assets/lib/jquery-ui/jquery-ui-1.12.1.custom/jquery-ui.min.css">
<link rel="stylesheet"
	href="/assets/lib/bootstrap-plugins/bootstrap-slider/css/bootstrap-slider.min.css">
<script
	src="/assets/lib/jquery-ui/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script
	src="/assets/lib/bootstrap-plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
<script>
var itemList = [
	{name:"침대",width:150,height:200,top:19,left:171,rotate:0}
	,{name:"TV대",width:150,height:47,top:323,left:202,rotate:0}
	,{name:"옷장",width:121,height:62,top:18,left:331,rotate:0}
	,{name:"소파",width:180,height:73,top:297,left:475,rotate:0}
	,{name:"책상-석훈",width:140,height:60,top:256,left:21,rotate:0}
	,{name:"책상-민진",width:60,height:180,top:19,left:111,rotate:0}
	,{name:"화장대",width:80,height:40,top:330,left:353,rotate:0}
	,{name:"식탁",width:65,height:85,top:75,left:455,rotate:0}
	,{name:"렌지대",width:40,height:56,top:18,left:453,rotate:0}
];
var selectedIndex = 0;
var settings = {width:620,height:595};
$(document).ready(function(){
	drawFurniture(itemList);
	listFurniture(itemList);
	initDraggable();
	initSliderBar();
	eventListner_ItemListTable();
	eventListner_ChangeItemval();
	$("#item_changewidthheight").on("click",function(){
		var before_width = itemList[selectedIndex].width;
		var before_height = itemList[selectedIndex].height;
		itemList[selectedIndex].width = before_height;
		itemList[selectedIndex].height = before_width;
		redrawFunritureArchitect();
	});
	initCanvas();
	$("#canvas").mousedown(function(e){isDragging=true;});
    $("#canvas").mousemove(function(e){
        if(isDragging){
        	var ctx = document.getElementById('canvas').getContext('2d');
	        var canMouseX=parseInt(e.clientX);
	    	var canMouseY=parseInt(e.clientY);
	        ctx.clearRect(0,0,600,600);
	        ctx.drawImage(img,canMouseX-128/2,canMouseY-120/2,128,120);
	        drawCanvas();
    	}
	});
    $("#canvas").mouseup(function(e){isDragging=false;});
    $("#canvas").mouseout(function(e){
    	isDragging=false;
    });
});
var imageRoot = new Image();
var isDragging=false;
function initCanvas()
{
	imageRoot.src = "/assets/images/uploads/room-background_18p.jpg";
	window.requestAnimationFrame(drawCanvas);
}
function drawCanvas() 
{
	var ctx = document.getElementById('canvas').getContext('2d');
	ctx.clearRect(0,0,600,600);
	ctx.drawImage(imageRoot, 0, 0, 600, 600);
	ctx.rect(0, 0, 150, 150);
	ctx.fillStyle = "#ffffff";
	ctx.fill();
	ctx.lineWidth = 1;
	ctx.stroke();
}
/* 화면에 그림 */
function drawFurniture(list)
{
	var html = "";
	$("#containment-wrapper").html("");
	for(var i in list)
	{
		var item = list[i];
		//html += '<div class="ui-widget-content furniture-item" style="width:'+item.width+'px;height:'+item.height+'px;top:'+item.top+'px;left:'+item.left+'px;" data-index="'+i+'">'+item.name+'</div>';

		var test = $('<div class="ui-widget-content furniture-item" style="width:'+item.width+'px;height:'+item.height+'px;top:'+item.top+'px;left:'+item.left+'px;" data-index="'+i+'">'+item.name+'</div>');
		assignRotateCSS(test,item.rotate);
		test.appendTo("#containment-wrapper");
	}
	//$("#containment-wrapper").html(html);
}
/* 선택 목록에 생성 */
function listFurniture(list)
{
	var html = "";
	for(var i in list)
	{
		var item = list[i];
		var num = parseInt(i) + 1;
		html += '<tr><th scope="row">'+num+'</th><td><a href="#" data-index="'+i+'">'+item.name+'</a>&nbsp;&nbsp;<small class="text-muted">&#40;'+item.width+' x '+item.height+'&#41;</small></td></tr>';
	}
	$("#itemlistTable > tbody").html(html);
}
/* 체인지 이벤트 훅 */
function eventListner_ChangeItemval()
{
	$("#item_name").on("change",changeItemval);
	$("#item_width").on("change",changeItemval);
	$("#item_height").on("change",changeItemval);
	$("#item_rotate").on("change",changeItemval);
}
/* 변경이벤트 처리*/
function changeItemval()
{
	itemList[selectedIndex].name = $("#item_name").val();
	itemList[selectedIndex].width = $("#item_width").val();
	itemList[selectedIndex].height = $("#item_height").val();
	itemList[selectedIndex].rotate = $("#item_rotate").val();

	redrawFunritureArchitect();
}
/* 변경된 값을 프로그램 에 적용 */ 
function redrawFunritureArchitect()
{
	drawFurniture(itemList);
	listFurniture(itemList);
	initDraggable();
	
	viewItemDetail();
}
/* 가구 목록 에서 선택 이벤트 */
function eventListner_ItemListTable(){
	$("#itemlistTable").on("click","a",function(e){
		e.preventDefault();
		selectedIndex = $(this).attr("data-index");
		viewItemDetail();
	});
}
/* 상세 옵션을 표현 */
function viewItemDetail()
{
	if($("#item_fieldset").attr("disabled")=="disabled"){
		$("#item_fieldset").attr("disabled",false);
	}
	var item = itemList[selectedIndex];
	$("#item_name").val(item.name);
	$("#item_width").val(item.width);
	$("#item_height").val(item.height);
	$("#item_rotate").slider('setValue',item.rotate);
	$("#currentSliderValLabel").text(item.rotate);
}
/* draggable 이벤트 셋팅 */
function initDraggable()
{
	$(".furniture-item").draggable({
		containment : "#containment-wrapper",
		scroll : false,
		drag : function(){
			var top = $(this).css("top");
			var left = $(this).css("left");
			itemList[$(this).attr("data-index")].top = parseInt(top,10);
			itemList[$(this).attr("data-index")].left = parseInt(left,10);
			selectedIndex = $(this).attr("data-index");
			viewItemDetail();
		}
	});
}
function initSliderBar()
{
	$("#item_rotate").slider();
	$("#item_rotate").on("slide",function(e){
		$("#currentSliderValLabel").text(e.value);
	});
}
function reverseWidthHeight(target)
{
	var current_width = target.css("width");
	var current_height = target.css("height");
	target.css("width",current_height);
	target.css("height",current_width);
}
function assignRotateCSS(target,degree)
{
	target.css('-moz-transform', 'rotate(' + degree + 'deg)');
	target.css('-moz-transform-origin', '50% 50%');
	target.css('-webkit-transform', 'rotate(' + degree + 'deg)');
	target.css('-webkit-transform-origin', '50% 50%');
	target.css('-o-transform', 'rotate(' + degree + 'deg)');
	target.css('-o-transform-origin', '50% 50%');
	target.css('-ms-transform', 'rotate(' + degree + 'deg)');
	target.css('-ms-transform-origin', '50% 50%');
}
</script>
<style>
#containment-wrapper {
	width: 620px;
	height: 595px;
	border: 2px solid #ccc;
	padding: 0px;
	margin: 0 auto;
	float: left;
	background-image: url("/assets/images/uploads/room-background_18p.jpg");
	position: relative;
}

.furniture-item {
	padding: 0;
	/*float: left;*/
	margin: 0;
	position: absolute;
	font-size: 0.5rem;
	color: rgba(0,0,0,0.8);
}
</style>
<div>
	<h2>가구 방 배치</h2>

	<div class="row">
		<div class="col-lg-6">
			<canvas id="canvas" width="600" height="600" style="border:1px solid black;"></canvas>
			<div id="containment-wrapper"></div>
		</div>

		<div class="col-lg-6">
			<div class="card">
				<div class="card-header">
					<ul class="nav nav-tabs card-header-tabs" role="tablist">
						<li class="nav-item"><a class="nav-link active" data-toggle="tab"
							href="#furni-settings" role="tab">가구 셋팅</a></li>
						<li class="nav-item"><a class="nav-link" data-toggle="tab"
							href="#default-settings" role="tab">기본 설정</a></li>
					</ul>
				</div>
				<div class="card-block">
					<div class="tab-content">
						<div class="tab-pane fade show active" id="furni-settings"
							role="tabpanel">
							<div class="row">
								<div class="col-6">
									<table class="table table-sm" id="itemlistTable">
										<thead>
											<tr>
												<th>#</th>
												<th>가구 목록</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
								<div class="col-6">
									<div class="card">
										<div class="card-header">설정 값</div>
										<div class="card-block">
											<fieldset disabled id="item_fieldset">
												<div class="form-group">
													<label for="name_han" class="control-label">명칭</label> <input
														type="text" name="details[name]" id="item_name"
														class="form-control" placeholder="" title="" value="">
												</div>
												<div class="form-group">
													<label for="name_han" class="control-label">가로 (cm)</label>
													<input type="text" name="details[name]" id="item_width"
														class="form-control" placeholder="" title="" value="390">
												</div>
												<div class="form-group">
													<label for="name_eng" class="control-label">세로 (cm)</label>
													<input type="text" name="details[name_english]"
														id="item_height" class="form-control" placeholder=""
														title="" value="920">
												</div>
												<div class="form-group">
													<label for="name_eng" class="control-label">가로세로 전환</label>
													<button type="button" class="btn btn-secondary btn-sm"
														id="item_changewidthheight">
														<i class="fa fa-retweet" aria-hidden="true"></i>
													</button>
												</div>
												<div class="form-group">
													<label for="name_eng" class="control-label">회전 값</label> <input
														type="text" name="details[name_english]" id="item_rotate"
														class="form-control" placeholder="" title="" value="0"
														data-provide="slider" data-slider-min="0"
														data-slider-max="360" data-slider-step="1"
														data-slider-value="3"> <span id="currentSliderValLabel"></span>
												</div>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="default-settings" role="tabpanel">
							<div class="form-group">
								<label for="name_han" class="control-label">가로 (cm)</label> <input
									type="text" name="details[name]" id="width"
									class="form-control" placeholder="" title="" value="620">
							</div>
							<div class="form-group">
								<label for="name_eng" class="control-label">세로 (cm)</label> <input
									type="text" name="details[name_english]" id="height"
									class="form-control" placeholder="" title="" value="595">
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
@stop
