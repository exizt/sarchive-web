@extends('layouts.admin_layout') 

@section('title',"글 작성 - 블로그")
@section('title-layout',"포스트 관리")

@section('content')
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/prism.css">
<script src="/assets/lib/prism/prism.js"></script>

<div class="container-fluid py-4">
	<h6><small>Read Post</small></h6>
	<div class="row">
		<div class="col-4 col-md-6">
			<h3><small>Archive</small></h3>
		</div>
		<div class="col-8 col-md-6 text-right">
			<a class="btn btn-secondary btn-sm" href="{{ url()->previous() }}" role="button">Back</a>
			<a class="btn btn-primary btn-sm" href="{{ route($ROUTE_ID.'.index') }}" role="button">List</a>
			<a class="btn btn-success btn-sm" href="{{ route($ROUTE_ID.'.edit',$post->id) }}" role="button" id="site-shortcut-key-e">Edit</a>
			<button type="button" class="btn btn-danger btn-sm " data-toggle="modal" data-target="#modal-delete">
				<i class="fa fa-times-circle"></i>&nbsp;Delete
			</button>
		</div>
	</div>	
	<hr>
	<h1>{{ $post->title }}</h1>
	<h5>Published at {{ $post->published_at->format('F j, Y') }}
	<small>({{ $post->published_at->format('g:i a') }})</small></h5>
	@foreach ($post->tags as $item)
	<a href="{{ route('blog.tags.show',$item->tag) }}" class="badge badge-dark">{{ $item->tag }}</a>
	@endforeach	
	<hr>
	{!! $post->content_html !!}
	<hr>
	<button class="btn btn-primary" onclick="history.go(-1)">Back</button>
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
			<form method="POST" action="{{ route($ROUTE_ID.'.destroy',$post->id) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_method" value="DELETE">
				<button type="button" class="btn btn-default"
					data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger">
				<i class="fa fa-times-circle"></i> Yes
				</button>
			</form>
			</div>
		</div>
	</div>
</div>
@stop