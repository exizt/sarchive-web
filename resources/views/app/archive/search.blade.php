@extends('layouts.archive_layout') 
@section('title',"검색 결과") 
@section('content')
<div id="curBoardId" data-current-board="{{ $parameters['board']}}">
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9">
					<div class="row px-0 mx-0">
						<div class="d-flex w-100 justify-content-between">
							<h4 class="">검색 결과</h4>
							<small class="text-mute">Page {{ $articles->currentPage() }} of {{ $articles->lastPage() }}</small>
						</div>
					</div>
					<hr>
					<div class="list-group">
						@foreach ($articles as $article)
						<a class="list-group-item list-group-item-action flex-column align-items-start" 
							href="{{ route('archives.show',['profile_id'=>$article->profile_id,'archive_id'=>$article->id]) }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $article->title }}</h5>
								<small>{{ $article->created_at->format('Y-m-d') }}<br>카테고리 : {{ $article->category_name }}
								</small>
							</div>
							<p class="mb-1 pl-3">
								<small><em>{{ $article->summary_var }}</em></small>
							</p>
						</a> @endforeach
					</div>
				</div>
				<div class="col-md-3">
					<h4 class="px-2">게시판</h4>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb" id="shh-nav-board-path"></ol>
					</nav>
					<div class="list-group" id="shh-nav-board-list"></div>
				</div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $articles->appends($pagParameters)->links() }}</div>
	</div>
</div>
<script>
	$(function(){
		ajaxBoardList()
	})
	
	function ajaxBoardList(){
		var boardId = $("#curBoardId").data("currentBoard")
		//console.log(boardId)
		$.getJSON("/archives/ajax_boards",{
			board_id : boardId
		},function(data){
			//console.log(data)
			
			var current = data.current
			var curPath = JSON.parse(current.path)

			$.each(curPath,function(i,item){
				//console.log(item)
				var html = '<li class="breadcrumb-item"><a href="archives?board='
				+item.id
				+'">'
				+item.text
				+'</a></li> '
				$("#shh-nav-board-path").append(html)
			})
			$.each(data.list, function(i,item){
				var depth = item.depth - current.depth
				if(depth >3) return
				var html = '<a href="archives?board='
				+item.id
				+'" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">'
				+">".repeat(depth)+"&nbsp;"+item.name
				+' <span class="badge badge-secondary badge-pill">'+item.count+'</span></a>'

				$("#shh-nav-board-list").append(html)
			})
		})
	}
	</script>
@endsection
