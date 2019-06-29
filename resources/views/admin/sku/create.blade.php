@extends('layouts.admin_layout') 
@section('title',"SKU 생성")
@section('title-layout',"SKU Serial Management")
@section('content')
<script src="/assets/js/site-myservice.js"></script>
<script>

$(function() {
	$('form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
		    e.preventDefault();
		    return false;
		}
	});
});
</script>
<script>
$(document).ready(function(){
	selectChained($("#depth4"));

	$("#depth1").change();

	$("#depth4").on("change",function(){
		var sku = renderingSKU();
		$("#SKU-").text(sku);
	});
});
function renderingSKU(){
	return "SHN"+ $("#depth1").val() + "-" + $("#depth2").val() + $("#depth3").val() + "-"  + $("#depth4").val();
}
function selectChained($child){
	// 핵심 코드
	function parent_change_event(){
		var value_parent = $(this).val();
		
		$child.find("option").remove();

		var child_option_length = 0;
		$child_clone.each(function(){
			if($(this).data("parent")== value_parent){
				$child.append($(this));
				child_option_length++;
			}
		});

		/*
		if(child_option_length==0){
			$child.change();
		}
		*/
		$child.change();
	}
	// 구문
	if($child.data("parent")===undefined || $child.data("parent")===""){
		//console.log("data-parent is undefined ("+$child.attr("id") + ")");
		return;
	}

	var $parent = $($child.data("parent"));
	var $child_clone = $child.find("option").clone();
	$parent.on("change",parent_change_event);
	selectChained($parent);
}
</script>
<div class="container-fluid pt-4 pb-5">
    <div class="row px-0 mx-0">
       	<div class="col-6">
			<h3>
				<small>SKU 키 생성</small>
			</h3>
		</div>
    </div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

	<div class="card">
		<div class="card-body">
			<form class="form-horizontal" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div>
				<h4>카테고리 선택</h4>
			</div>
			<hr>
			<div class="row">
				<div class="form-group col-12 col-sm-6 col-md-3">
				    <label for="depth1">Step 1</label>
				    <select class="form-control" id="depth1" name="depth1" size="3">
				      <option value="SWR" selected="selected">SWR: 소프트웨어</option>
				      <option value="HDR">HDR: 하드웨어</option>
				    </select>
				</div>
				
				<div class="form-group col-12 col-sm-6 col-md-3">
				    <label for="depth2">Step 2</label>
				    <select class="form-control" id="depth2" name="depth2" size="3" data-parent="#depth1">
				      <option data-parent="SWR" value="AP">AP: 애플리케이션 (프로그램, 앱)</option>
				      <option data-parent="SWR" value="SV">SV: 서버</option>
				      <option data-parent="SWR" value="WB">WB: 웹</option>
				    </select>
				</div>
				
				<div class="form-group col-12 col-sm-6 col-md-3">
				    <label for="depth3">Step 3</label>
				    <select class="form-control" id="depth3" name="depth3" size="3" data-parent="#depth2">
				      <option data-parent="AP" value="ADR">ADR: 안드로이드</option>
				      <option data-parent="AP" value="IPH">IPH: iOS</option>
				      <option data-parent="AP" value="WND">WND: 윈도우</option>
				      <option data-parent="AP" value="MCS">MCS: 맥 OS</option>
				      <option data-parent="WB" value="FRT">FRT: HTML 일반 웹 프론트 엔드</option>
				      <option data-parent="WB" value="PHP">PHP: php</option>
				      <option data-parent="WB" value="JSP">JSP: jsp</option>
				    </select>
				</div>		
				
				<div class="form-group col-12 col-sm-6 col-md-3">
				    <label for="depth4">Step 4</label>
				    <select class="form-control" id="depth4" name="depth4" size="3" data-parent="#depth3">
				      <option data-parent="IPH" value="AL">AL: 모든 All or Every</option>
				      <option data-parent="IPH" value="GA">GA: 게임류 Games</option>
				      <option data-parent="IPH" value="UT">UT: 유틸류 Utilites</option>
				      <option data-parent="IPH" value="ED">ED: 교육용 Education</option>
				      <option data-parent="ADR" value="AL">AL: 모든 All or Every</option>
				      <option data-parent="MCS" value="AL">AL: 모든 All or Every</option>
				      <option data-parent="WND" value="AL">AL: 모든 All or Every</option>
				      <option data-parent="FRT" value="JQ">JQ: jQuery 라이브러리</option>
				      <option data-parent="FRT" value="JS">JS: 일반 Javascript 라이브러리</option>
				    </select>
				</div>
				
				<div class="col-12">
					<span>선택된 키</span>
					<span id="SKU-">SHN-</span>
				</div>
			</div>
			
			<div class="pt-5">
				<h4>제품 정보</h4>
				<hr>
			</div>
			@include($VIEW_PATH.'._form')
			<div class="">
				<button type="submit" class="btn btn-sm btn-primary" id="site-shortcut-key-s">저장</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ url()->previous() }}" role="button">Cancel</a>
			</div>
			</form>
		</div>
	</div>
</div>

@stop