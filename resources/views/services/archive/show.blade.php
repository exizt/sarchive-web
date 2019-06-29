@extends('layouts.archive_layout') 
@section('title',"$article->title") 
@section('content') 
{{-- prism : 코드 syntaxhighlighter 종류 중 하나 --}}
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/prism.css">
<script src="/assets/lib/prism/prism.js"></script>

<div class="container-fluid mt-4 mb-5">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			@foreach ($categoryPath as $item)
			<li class="breadcrumb-item"><a href="{{ route($ROUTE_ID.'.index')}}?category={{$item->id}}">{{ $item->name }}</a></li> 
			@endforeach
		</ol>
	</nav>

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
			<a class="btn btn-outline-info btn-sm site-shortcut-key-e" href="{{ route($ROUTE_ID.'.edit',$article->id) }}" role="button">편집</a>
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
				<form method="POST" action="{{ route($ROUTE_ID.'.destroy',$article->id) }}">
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
@stop
