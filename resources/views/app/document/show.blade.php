@extends('layouts.sarchive_layout') 
@section('title',"$article->title") 
@section('content') 
{{-- prism : 코드 syntaxhighlighter 종류 중 하나 --}}
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/prism.css">
<script src="/assets/lib/prism/prism.js"></script>

<div class="container-fluid mt-4 mb-5">
	@include('layouts.modules.messages.messages_and_errors_bs4')
	<h5>위치</h5>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/archives/{{$archive->id}}/latest">{{ $archive->name }}</a></li> 
			@isset ($folder->paths)
			@foreach ($folder->paths as $item)
			<li class="breadcrumb-item"><a href="/folders/{{$item->id}}">{{ $item->name }}</a></li> 
			@endforeach
			@endisset
		</ol>
	</nav>
	<div class="d-flex justify-content-between">
		<h4>본문</h4>
		<div>
			<a class="btn btn-sm shh-btn-bookmark active {{($bookmark->is_favorite)? 'btn-info':'btn-primary'}}" href="#" role="button" 
				data-mode="favorite" data-archive="{{$article->id}}" data-value="{{$bookmark->is_favorite}}"><i class="fas fa-star"></i>&nbsp;즐겨찾기</a>
			<a class="btn btn-sm shh-btn-bookmark active {{($bookmark->is_bookmark)? 'btn-info':'btn-primary'}}" href="#" role="button" 
				data-mode="bookmark" data-archive="{{$article->id}}" data-value="{{$bookmark->is_bookmark}}"><i class="fas fa-bookmark"></i>&nbsp;북마크</a>
		</div>
	</div>
	<div class="card sa-content-text">
		<div class="card-body">
			<h5 class="card-title">{{ $article->title }}</h5>
			<p class="text-right">
				<small class="text-muted">최근 변경 {{ $article->updated_at->format('Y-m-d g:ia') }} (생성 {{ $article->created_at->format('Y-m-d g:ia') }})</small>
			</p>
			<hr>
			<p class="card-text">{!! $article->content !!}</p>
		</div>
	</div>
	<h4 class="my-3">문서 정보</h4>
	<div class="card">
		<div class="card-body">
			<div>작성 : {{ $article->created_at->format('Y-m-d g:ia') }}</div>
			<div>수정 : {{ $article->updated_at->format('Y-m-d g:ia') }}</div>
			<div>
				분류 : <ul class="list-inline shh-cat-list" style="display:inline">
					@foreach ($article->category_array as $i => $item)
						<li class="list-inline-item px-1 mx-0"><a href="/archives/{{$archive->id}}/category/{{urlencode($item)}}">{{$item}}</a></li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
	<hr>
	<div class="form-group row">
		<div class="col-md-10 col-md-offset-2">
			<a class="btn btn-primary btn-sm site-shortcut-key-c" href="{{ $previousList }}" role="button">목록</a>
			<a class="btn btn-outline-info btn-sm site-shortcut-key-e" 
				href="{{ route($ROUTE_ID.'.edit',['doc'=>$article->id]) }}" role="button">편집</a>
			
		</div>
	</div>

</div>
<style>
.sa-content-text {
	font-family: 나눔고딕,'맑은 고딕','NanumGothic','Malgun Gothic';
	font-size: 1.375rem;
	font-weight: normal;
}
.shh-cat-list{

}
.shh-cat-list li:first-child{
	border-left:none;
}
.shh-cat-list li:not(:first-child)::before{
	content: "|";
	padding-right: 0.5rem;
}
.shh-cat-list li:not(:first-child){
	
}
</style>
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
	var conf = {
		true_class : "btn-info",
		false_class : "btn-primary"
	}
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
					$(this).addClass(conf.true_class)
					$(this).removeClass(conf.false_class)
				} else {
					$(this).addClass(conf.false_class)
					$(this).removeClass(conf.true_class)
				}
			} else if($(this).data("mode") == "bookmark"){
				$(this).attr("data-value",json.data.is_bookmark)
				if(json.data.is_bookmark=='1'){
					$(this).addClass(conf.true_class)
					$(this).removeClass(conf.false_class)
				} else {
					$(this).addClass(conf.false_class)
					$(this).removeClass(conf.true_class)
				}
			}
			//console.log("dd"+index+$(this).data("mode"));
		})
	})
}
</script>
@endsection
