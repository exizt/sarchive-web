@extends('layouts.archive_layout') 
@section('title',"$article->title") 
@section('content') 
{{-- prism : 코드 syntaxhighlighter 종류 중 하나 --}}
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/prism.css">
<script src="/assets/lib/prism/prism.js"></script>

<div class="container-fluid mt-4 mb-5">
	게시판 경로
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			@isset ($boardPath)
			@foreach ($boardPath as $item)
			<li class="breadcrumb-item"><a href="{{ route($ROUTE_ID.'.index',['profile'=>$parameters['profile']])}}?board={{$item->id}}">{{ $item->text }}</a></li> 
			@endforeach
			@endisset
		</ol>
	</nav>
	정보
	<div class="card">
		<div class="card-body">
			* 최근 변경일시 : {{ $article->updated_at->format('Y-m-d g:ia') }}<br>
			* 생성일시 : {{ $article->created_at->format('Y-m-d g:ia') }}<br>
			* 분류 :&nbsp;&nbsp;
			@foreach ($article->category_array as $i => $item)
				@if($i > 0) | @endif
				<a href="/{{$parameters['profile']}}/category/{{urlencode($item)}}">{{$item}}</a>
			@endforeach
		</div>
	</div>
	본문
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">{{ $article->title }}</h5>
			<p class="text-right">
				<small class="text-muted">최근 변경 {{ $article->updated_at->format('Y-m-d g:ia') }} (생성 {{ $article->created_at->format('Y-m-d g:ia') }})</small>
			</p>
			<hr>
			<p class="card-text">{!! $article->content !!}</p>
		</div>
	</div>
	<hr>
	<div class="form-group row">
		<div class="col-md-10 col-md-offset-2">
			<a class="btn btn-primary btn-sm site-shortcut-key-c" href="{{ $previousList }}" role="button">목록</a>
			<a class="btn btn-outline-info btn-sm site-shortcut-key-e" href="{{ route($ROUTE_ID.'.edit',['profile'=>$parameters['profile'],'archive'=>$article->id]) }}" role="button">편집</a>
			<a class="btn btn-outline-primary btn-sm shh-btn-bookmark" href="#" role="button" 
				data-mode="bookmark" data-archive="{{$article->id}}" data-value="{{$bookmark->is_bookmark}}">북마크</a>
			<a class="btn btn-outline-primary btn-sm shh-btn-bookmark" href="#" role="button" 
				data-mode="favorite" data-archive="{{$article->id}}" data-value="{{$bookmark->is_favorite}}">즐겨찾기</a>
		</div>
	</div>
</div>

{{-- Confirm Delete --}}
<div class="modal fade" id="modal-delete" tabIndex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Please Confirm</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="lead">
					<i class="fa fa-question-circle fa-lg"></i>&nbsp;&nbsp;Are you sure you want to delete this post?
				</p>
			</div>
			<div class="modal-footer">
				<form method="POST" action="{{ route($ROUTE_ID.'.destroy',['profile'=>$parameters['profile'],'archive'=>$article->id]) }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}"> <input type="hidden" name="_method" value="DELETE">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger">
						<i class="fa fa-times-circle"></i> Yes
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	$(".shh-btn-bookmark").on("click",doAjax_Bookmarking_event)
})

function doAjax_Bookmarking_event(e){
	e.preventDefault()
	var id = $(this).data("archive")
	var mode = $(this).data("mode")
	doAjax_Bookmarking(mode,id)
}

function doAjax_Bookmarking(mode,id){
	$.post({
		url: '/archives/ajax_mark',
		dataType: 'json',
		data: {
			mode: mode,
			archive: id
		}
	}).done(function(json){
		$(".shh-btn-bookmark").each(function(index){
			if($(this).data("mode") == "favorite"){
				$(this).attr("data-value",json.data.is_favorite)
			} else if($(this).data("mode") == "bookmark"){
				$(this).attr("data-value",json.data.is_bookmark)
			}
			console.log("dd"+index+$(this).data("mode"));
		})
	})
}
</script>
@stop
