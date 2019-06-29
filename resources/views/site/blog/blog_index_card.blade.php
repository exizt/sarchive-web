@extends('layouts.blog') 

@section('title',"블로그")
@section('meta-description',"")
@section('layout-subheader-title',"블로그")
@section('layout-subheader-description',"")
@section('layout-subheader-background',"")

@section('content')
<style>
.card-title a{
	text-decoration:none;
	color: inherit;
}
</style>
<div class="container">
	<div class="row px-0 mx-0">
		<h1 class="col-8">Archives</h1>
		<div class="text-right col-4 px-2 align-text-bottom"><br>
			<h6><small class="text-mute">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</small></h6>
		</div>
	</div>
	@if (Auth::check())
	<hr class="mt-0">
	<div>
		<div class="btn-group" role="group" aria-label="Basic example">
			<a class="btn btn-secondary btn-sm d-none d-md-block"
				href="{{ route('admin.post.index') }}" target="_blank" role="button">포스팅 관리자</a>
			<a class="btn btn-secondary btn-sm d-none d-md-block"
				href="{{ route('admin.post.create') }}" target="_blank" role="button">신규 포스팅</a>
		</div>
	</div>
	@endif
	<hr class="mt-0">
	<div class="card-deck">
	@foreach ($posts as $post)
		<div class="card mb-2">
			@if (empty($post->image_header))
				<div class="card-body" style="height:200px; background-color:#aaa;color:#eee;">no image</div>
			@else
				<div class="card-body p-0" style="height:200px; color:#eee; 
					background-image:url('{{ $post->image_header }}');background-size: cover;">
					<div style="background-color: rgba(100, 50, 50, 0.3); width:100%; height:100%;">&nbsp;</div>
				</div>
			@endif
			<div class="card-body">
				<h4 class="card-title"><a href="{{ route($ROUTE_ID.'.show',$post->slug) }}">{{ $post->title }}</a></h4>
				<p class="card-text">{{ $post->content_summary_alter }}</p>
				<p class="card-text"><small class="text-muted">{{ $post->published_at->format('F d, Y - g:i a') }}</small></p>
			</div>
		</div>
	@endforeach
	</div>
	<hr>
</div>
<div class="pl-3 container">
{{ $posts->links() }}
</div>
@stop