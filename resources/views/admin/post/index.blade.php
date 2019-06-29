@extends('layouts.admin_layout') 
@section('title',"블로그 > 글 목록")
@section('title-layout',"Blog > Articles") 
@section('content')
<style>
.post_is_secret{
	background-color: rgba(100,100,100,0.2);
	opacity: 0.5;
}

</style>
<div class="container-fluid py-4">
	<div class="row">
		<div class="col-md-6">
			<a href="{{ route('admin.post.create') }}" class="btn btn-outline-success btn-sm">신규</a>
		</div>
		<div class="col-md-6 text-right">
			<h5>Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</h5>
		</div>
	</div>
	<hr>
	<div>
		@foreach ($posts as $post)
		<div class="@if ($post->is_secret) post_is_secret @endif">
		<div class="row">
			<div class="col-8">
				<h4>
					<a href="{{ route('admin.post.edit',$post->id) }}" class="text-dark">{{ $post->title }}</a>
				</h4>
				<p>{{ $post->content_summary_alter }}</p>
				<em>발행 : {{ $post->published_at->format('Y-m-d H:i') }} / 작성: {{ $post->created_at->format('Y-m-d H:i') }} / 마지막 변경: {{ $post->updated_at->format('Y-m-d H:i') }}</em><br>
				<em>상태 : @if ($post->is_secret) 비공개 @endif
				@if ($post->is_completed) 완료 @else 미완료 @endif
				</em>
			</div>
			<div class="col-4 text-right">
				<a href="{{ route('admin.post.show',$post->id) }}" class="btn btn-secondary btn-sm">읽기</a>
				@if (! $post->is_secret && $post->is_completed)
					<a href="{{ route('blog.show',$post->slug) }}" class="btn btn-secondary btn-sm" target="_blank">읽기 (to 블로그)&nbsp;<i class="fa fa-external-link"></i></a>
				@endif
			</div>
		</div>
		<hr>
		</div>
		@endforeach
	</div>
	<hr>
	{{ $posts->links() }}
</div>
@stop
