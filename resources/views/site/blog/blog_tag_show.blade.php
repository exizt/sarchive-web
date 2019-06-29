@extends('layouts.blog') 

@section('title',"블로그")
@section('title-layout',"TAG : $tag_name")
@section('meta-description',"")
@section('layout-description',"")

@section('content')
<div class="container-fluid">
	<div class="row px-0 mx-0">
		<h1 class="col-8 col-md-6">Archives</h1>
	</div>
	
	<div class="text-right px-2">
		<h6><small class="text-mute">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</small></h6>
	</div>
	<hr>
	<div>
	@foreach ($posts as $post)
		<div class="px-2">
			<h5><a href="{{ route($ROUTE_ID.'.show',$post->slug) }}">{{ $post->title }}</a></h5>
			<small class="text-muted"><em>{{ $post->published_at->format('Y-m-d g:ia') }}</em></small>
		</div>
		<hr>
	@endforeach
	</div>
</div>
<hr>
<div class="pl-3">
{{ $posts->links() }}
</div>
@stop