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
			<br>
			<a class="btn btn-outline-primary btn-sm shh-btn-bookmark {{($bookmark->is_favorite)? 'active':''}}" href="#" role="button" 
			data-mode="favorite" data-archive="{{$article->id}}" data-value="{{$bookmark->is_favorite}}"><i class="fas fa-star"></i>&nbsp;즐겨찾기</a>
			<a class="btn btn-outline-primary btn-sm shh-btn-bookmark {{($bookmark->is_bookmark)? 'active':''}}" href="#" role="button" 
				data-mode="bookmark" data-archive="{{$article->id}}" data-value="{{$bookmark->is_bookmark}}"><i class="fas fa-bookmark"></i>&nbsp;북마크</a>
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
				if(json.data.is_favorite=='1'){
					$(this).addClass("active")
				} else {
					$(this).removeClass("active")
				}
			} else if($(this).data("mode") == "bookmark"){
				$(this).attr("data-value",json.data.is_bookmark)
				if(json.data.is_bookmark=='1'){
					$(this).addClass("active")
				} else {
					$(this).removeClass("active")
				}
			}
			console.log("dd"+index+$(this).data("mode"));
		})
	})
}
</script>
@stop
