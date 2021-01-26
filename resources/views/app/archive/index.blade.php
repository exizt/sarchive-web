@extends('layouts.sarchive_layout')
@section('title',"아카이브 프로필 관리")
@section('content')
<div class="container py-5">
	@include('layouts.modules.messages.messages_and_errors_bs4')
	<div class="row px-0 mx-0">
		<div class="d-flex w-100 justify-content-between">
			<h4 class="">아카이브 목록</h4>
			<small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
		</div>
	</div>
	<hr class="mt-1">
	<div class="list-group">
		@foreach ($masterList as $item)
		<a class="list-group-item list-group-item-action flex-column align-items-start shh-profile-list" href="{{ route($ROUTE_ID.'.edit',$item->id) }}"
			data-archive-id="{{$item->id}}" data-name="{{$item->name}}">
			<div class="d-flex w-100 justify-content-between">
				<h5 class="">{{ $item->name }}@if ($item->is_default) &nbsp;&nbsp;&nbsp;<span class="badge badge-success pull-right">기본</span> @endif </h5>
				<div>
					<span class="shh-listmovemode-off">
						<small>{{ $item->created_at->format('Y-m-d') }}</small>
					</span>
					<span class="shh-listmovemode-on" style="display:none">
						<button type="button" class="btn btn-primary btn-sm shh-btn-mode-up">▲</button>
						<button type="button" class="btn btn-primary btn-sm shh-btn-mode-down">▼</button>
					</span>
				</div>
			</div>
			<p class="mb-1 pl-md-3 sarc-item-comments">
				<small>{{ $item->comments }}</small>
			</p>
		</a>
		@endforeach
	</div>
	<hr>
	<div class="d-flex w-100 justify-content-between">
		<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-outline-success btn-sm">신규</a>
		<span>
			<a href="#" id="changeIndexModeToggle" class="btn btn-outline-success btn-sm shh-listmovemode-off">순서변경</a>
			<a href="#" id="shh-movemode-cancel" class="btn btn-outline-success btn-sm shh-listmovemode-on" style="display:none">순서변경 취소</a>
			<a href="#" id="shh-movemode-save" class="btn btn-outline-success btn-sm shh-listmovemode-on" style="display:none">순서변경 저장</a>
		</span>
	</div>
	<hr>
	<div class="text-xs-center">{{ $masterList->links() }}</div>
</div>
<script>
$(function(){
	$("#changeIndexModeToggle").on("click",function(){
		changeIndexModeOn()
	})
	$("#shh-movemode-cancel").on("click",function(){location.reload();})
	$("#shh-movemode-save").on("click",function(){
		saveArchiveSort()
	})
	$(".shh-btn-mode-up").on("click",onClickMoveUp);
	$(".shh-btn-mode-down").on("click",onClickMoveDown);
})

/**
 * 아카이브 순서 변경사항을 저장
 */
function saveArchiveSort(){
	console.log("save");
	var dataList = [];
	$(".shh-profile-list").each(function(index){
		var data = {
			id : $(this).data("archiveId"),
			name : $(this).data("name"),
			index : index
		};
		dataList.push(data)
	})

	console.log(dataList)

	$.post({
		url: '/archives/updateSort',
		data: {
			'dataList': dataList
		}
	})
	.done(function(data){
		location.reload()
	})
}
function changeIndexModeOn(){
	$(".shh-listmovemode-off").hide()
	$(".shh-listmovemode-on").show()
	$(".shh-profile-list").attr("href","#");
}

function onClickMoveUp(){
	moveUp($(this).closest(".shh-profile-list"),".shh-profile-list")
}

function onClickMoveDown(){
	moveDown($(this).closest(".shh-profile-list"),".shh-profile-list")
}

function moveUp($current,sel){
	var hook = $current.prev(sel)
	if(hook.length){
		var elementToMove = $current.detach();
		hook.before(elementToMove);
	}
}

function moveDown($current,sel){
	var hook = $current.next(sel)
	if(hook.length){
		var elementToMove = $current.detach();
		hook.after(elementToMove);
	}
}

</script>
@endsection