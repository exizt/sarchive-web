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
					<div class="list-group pt-3" id="shh-nav-board-only"></div>
				</div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $masterList->appends($mPaginationParams)->links() }}</div>
	</div>
</div>
<style>
.shh-navboardlist-depth-1{
	padding-left: 1.75rem;
}
.shh-navboardlist-depth-2{
	padding-left: 3.5rem;
}
.shh-navboardlist-depth-3{
	padding-left: 6.0em;
}
.shh-navboardlist-depth-4{
	padding-left: 8.5rem;
}
</style>
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
				var t_depth = (depth - 1 < 0) ? 0 : depth - 1
				if(depth >3) return
				var html = '<a href="/'
				+ profileId + '/archives?board='
				+ item.id
				+ '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center shh-navboardlist-depth-'+depth+'">'
				+ "❯".repeat(depth) + "&nbsp;&nbsp;" + item.name
				+ ' <span class="badge badge-secondary badge-pill">'+item.count+'</span></a>'
				//🢒 
				$("#shh-nav-board-list").append(html)
			})

			var html = '<a href="/'
				+ profileId + '/archives?board='
				+ data.current.id
				+ '&only=1" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">'
				+ data.current.name
				+ ' (only)</a>'

				$("#shh-nav-board-only").append(html)
		})
	}
	</script>
@endsection
