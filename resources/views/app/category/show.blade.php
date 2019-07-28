@extends('layouts.archive_layout') 
@section('title',"") 
@section('content')
<div>
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9">
					<div class="row px-0 mx-0">
						<div class="d-flex w-100 justify-content-between">
							<h4 class="">분류 : {{ $parameters['category']}}
								&nbsp;&nbsp;&nbsp;
							<a class="btn btn-outline-info btn-sm site-shortcut-key-e" 
								href="{{ route($ROUTE_ID.'.edit',['profile'=>$parameters['profile'],'category'=>urlencode($parameters['category'])]) }}" role="button">편집</a>
						</h4>
						<small class="text-mute">Page {{ $archives->currentPage() }}/{{ $archives->lastPage() }}</small>
						</div>
						<p class="lead">{{ $ArchiveCategory->text }}</p>
					</div>
					<h6>여기에 속하는 문서</h6>
					<div class="list-group">
						@foreach ($archives as $item) 
						<a class="list-group-item list-group-item-action flex-column align-items-start" 
							href="{{ route('archives.show',['profile'=>$parameters['profile'],'category'=>$item->id]) }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $item->title }}</h5>
								<small>{{ $item->created_at->format('Y-m-d') }}</small>
							</div>
							<p class="mb-1 pl-md-3 cz-item-summary">
								<small>{{ $item->summary_var }}</small>
							</p>
							<div class="d-flex justify-content-between">
								<small>게시판 : {{ $item->category_name }}</small>
							</div>
						</a> @endforeach
					</div>
					@if(count($childCategories))
					<hr>
					<h6>여기에 속하는 분류</h6>
					<div class="list-group">
						@foreach ($childCategories as $item)
						<a href="/{{$parameters['profile']}}/category/{{urlencode($item)}}" class="list-group-item list-group-item-action">{{$item}}</a>
						@endforeach
					</div>
					@endif
					@if(count($ArchiveCategory->parent_array))
					<hr>
					<div class="card">
						<div class="card-body">
							상위 분류&nbsp;:&nbsp;&nbsp;
							@foreach ($ArchiveCategory->parent_array as $i=>$item)
								@if($i>0) | @endif <a href="/{{$parameters['profile']}}/category/{{urlencode($item)}}">{{$item}}</a>&nbsp;
							@endforeach
						</div>
					</div>
					@endif
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
		<div class="text-xs-center">{{ $archives->appends(['profile'=>$parameters['profile'],'category' => $parameters['category']])->links() }}</div>
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
@stop
