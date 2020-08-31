@extends('layouts.admin_layout')
@section('title',"신규") 
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

<div>
	<form class="form-horizontal" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<div class="card mt-3">
			<h5 class="card-header">아카이브 신규</h5>
			<div class="card-body px-1 px-md-3">
				@include($VIEW_PATH.'._form')
				<div class="d-flex w-100 justify-content-between">
					<button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s">신규 생성</button>
					<a class="btn btn-secondary btn-sm site-shortcut-key-c" href="{{ route($ROUTE_ID.'.index') }}" role="button">취소</a>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
