@extends('layouts.admin_layout') 
@section('title',"SKU 목록")
@section('title-layout',"SKU Serial Management")
@section('content')
<script>
$(document).ready(function(){
	selectChained($("#depth4"));

	for(i=1;i<5;i++){
		if($("#current_depth_"+i).val().length > 1){
			$("#depth"+i).val($("#current_depth_"+i).val());
			$("#depth"+i).change();
		}
	}
	$("#depth1").change();
});

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
<style>
.font-monospace{
	font-family: "Courier New", Courier, monospace;
}
</style>
<div class="container-fluid pt-4 pb-5">
	<form class="form-horizontal" role="form" method="GET"
	action="{{ route($ROUTE_ID.'.index') }}">
	<input type="hidden" id="current_depth_1" value="{{ $search['depth_1']}}">
	<input type="hidden" id="current_depth_2" value="{{ $search['depth_2']}}">
	<input type="hidden" id="current_depth_3" value="{{ $search['depth_3']}}">
	<input type="hidden" id="current_depth_4" value="{{ $search['depth_4']}}">
	<h3>카테고리 선택</h3>
	<div class="row">
		<div class="form-group col-12 col-sm-6 col-md-3">
		    <label for="depth1">Step 1</label>
		    <select class="form-control" id="depth1" name="depth1" size="5">
		      <option value="SWR" selected="selected">SWR: 소프트웨어</option>
		      <option value="HDR">HDR: 하드웨어</option>
		    </select>
		</div>
		
		<div class="form-group col-12 col-sm-6 col-md-3">
		    <label for="depth2">Step 2 </label>
		    <select class="form-control" id="depth2" name="depth2" size="5" data-parent="#depth1">
		      <option data-parent="SWR" value="AP">AP: 애플리케이션 (프로그램, 앱)</option>
		      <option data-parent="SWR" value="SV">SV: 서버</option>
		      <option data-parent="SWR" value="WB">WB: 웹</option>
		    </select>
		</div>
		
		<div class="form-group col-12 col-sm-6 col-md-3">
		    <label for="depth3">Step 3</label>
		    <select class="form-control" id="depth3" name="depth3" size="5" data-parent="#depth2">
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
		    <select class="form-control" id="depth4" name="depth4" size="5" data-parent="#depth3">
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
	</div>
	<div class="">
		<button class="btn btn-info btn-sm" type="submit">검색</button>
		<a href="{{ route($ROUTE_ID.'.index') }}" class="btn btn-sm">선택 초기화</a>
	</div>
	</form>
	
	
	<hr>
	<h2 class="pt-3 mt-5">SKU 목록</h2>
	<table class="table table-striped table-hover">
		<thead class="thead-inverse">
			<tr>
				<th>#</th>
				<th>SKU 시리얼</th>
				<th>제품명</th>
				<th>분류 1</th>
				<th>분류 2</th>
				<th>분류 3</th>
				<th>분류 4</th>
			</tr>
		</thead>
		<tbody>
		@foreach ($records as $item)
			<tr>
				<th scope="row">{{ $item->id }}</th>
				<td class="font-monospace"><a href="{{ route($ROUTE_ID.'.edit',$item->id) }}" class="text-dark">{{ $item->product_sku }}</a></td>
				<td>{{ $item->product_name }}</td>
				<td>{{ $item->depth_1 }}</td>
				<td>{{ $item->depth_2 }}</td>
				<td>{{ $item->depth_3 }}</td>
				<td>{{ $item->depth_4 }}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<div class="row px-0 mx-0">
		<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-success btn-sm">신규</a>
	</div>
    <hr>
    {{ $records->links() }}
</div>
@stop

