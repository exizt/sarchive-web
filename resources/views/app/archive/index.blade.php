@extends('layouts.archive_layout') 
@section('title',"$archiveBoard->name") 
@section('content')
<div id="curBoardId" data-current-board="{{ $parameters['board']}}">
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9">
					<div class="row px-0 mx-0">
						<div class="d-flex w-100 justify-content-between">
							<h4 class="">게시글 목록 (선택된 게시판 : {{ $archiveBoard->name }})</h4>
							<small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
						</div>
						<p class="lead">{{ $archiveBoard->comment }}</p>
					</div>
					<hr class="mt-1">
					<div class="list-group">
						@foreach ($masterList as $item) <a class="list-group-item list-group-item-action flex-column align-items-start" href="{{ route('archives.show',['profile'=>$parameters['profile'],'archive'=>$item->id]) }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $item->title }}</h5>
								<small>{{ $item->created_at->format('Y-m-d') }}</small>
							</div>
							<p class="mb-1 pl-md-3 cz-item-summary">
								<small>{{ $item->summary_var }}</small>
							</p>
							<div class="d-flex justify-content-between">
								<small>게시판 : {{ $item->board }}</small>
								<small>분류 : {{ $item->category }}</small>
							</div>
						</a> @endforeach
					</div>
				</div>
				<div class="col-md-3">
					<h4 class="px-2">현재 위치</h4>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb" id="shh-nav-board-path"></ol>
					</nav>
					<h5>게시판</h5>
					<div class="list-group" id="shh-nav-board-list"></div>
				</div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $masterList->appends(['board' => $parameters['board']])->links() }}</div>
	</div>
</div>
<script>
	$(function(){
		ajaxBoardList()
	})
	
	function ajaxBoardList(){
		var boardId = $("#curBoardId").data("currentBoard")
		var profileId = $("body").data("profile")

		//console.log(boardId)
		$.getJSON("/archives/ajax_boards",{
			board_id : boardId
		},function(data){
			//console.log(data)
			
			var current = data.current
			var curPath = JSON.parse(current.path)

			$.each(curPath,function(i,item){
				//console.log(item)
				var html = '<li class="breadcrumb-item"><a href="/'
				+ profileId + '/archives?board='
				+ item.id
				+ '">'
				+ item.text
				+ '</a></li> '
				$("#shh-nav-board-path").append(html)
			})
			$.each(data.list, function(i,item){
				var depth = item.depth - current.depth
				if(depth >3) return
				var html = '<a href="/'
				+ profileId + '/archives?board='
				+ item.id
				+ '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">'
				+ ">".repeat(depth)+"&nbsp;"+item.name
				+ ' <span class="badge badge-secondary badge-pill">'+item.count+'</span></a>'

				$("#shh-nav-board-list").append(html)
			})
		})
	}
	</script>
@stop
