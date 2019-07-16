@extends('layouts.admin_layout') 
@section('title',"아카이브 카테고리 관리") 
@section('title-layout',"아카이브 카테고리 관리") 
@section('content')
<script>
$(function() {
	$('form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
		    e.preventDefault();
		    return false;
		}
	});
});
</script>
<div class="container-fluid pt-4 pb-5">
	@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li> @endforeach
		</ul>
	</div>
	@endif

	<form class="form-horizontal" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="card">
			<div class="card-body">
				@include($VIEW_PATH.'._form')
				<div>
					<button type="submit" class="btn btn-sm btn-primary" id="site-shortcut-key-s">저장</button>
					<a class="btn btn-sm btn-outline-secondary" href="{{ url()->previous() }}" role="button">취소</a>
				</div>
			</div>
		</div>
	</form>
</div>
@stop