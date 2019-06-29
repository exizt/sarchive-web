@extends('layouts.blog') 

@section('title',"{$post->title} - 블로그")
@section('meta-description',"{$post->content_summary_alter}")
@section('meta-title',"{$post->title} - 언제나 초심 블로그")
@section('layout-subheader-title',"{$post->title}")
@section('layout-subheader-description',"{$post->published_at->format('F d, Y - g:i a')}")
@section('layout-subheader-background',"{$post->image_header}")

@section('content')
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/prism.css">
<script src="/assets/lib/prism/prism.js"></script>

<style>
.content-of-post{
	padding-bottom: 3rem;
}
.content-of-post p{
	margin-bottom: 2rem;
}
.content-of-post h1{
	margin-top: 3rem;
}

</style>
<div class="container mt-1 mb-5">
	<h6><small>Read Post</small></h6>
	<hr>
	<div>@include('layouts.modules.adsense')</div>
	<hr>
	<h1>{{ $post->title }}</h1>
	<h5>Published at {{ $post->published_at->format('F j, Y') }}
	<small>({{ $post->published_at->format('g:i a') }})</small></h5>
	@foreach ($post->tags as $item)
	<a href="{{ route($ROUTE_ID.'.tags.show',$item->tag) }}" class="badge badge-dark">{{ $item->tag }}</a>
	@endforeach	
	<hr>
	<div class="content-of-post">
	{!! $post->content_html !!}
	</div>
	<hr>
	<a href="/blog" class="btn btn-primary">List</a>
	<button class="btn btn-secondary" onclick="history.go(-1)">Back</button>
</div>
<div class="container mt-1 mb-5">@include('layouts.modules.disqus',['disqus_page_identifier'=>$disqus_pid])</div>
@stop