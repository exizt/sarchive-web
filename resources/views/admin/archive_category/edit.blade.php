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
	@include('layouts.modules.messages_and_errors')
	<form class="form-horizontal" role="form" method="POST" action="{{ route($ROUTE_ID.'.update', $item->id) }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}"> <input type="hidden" name="_method" value="PUT">
		<div class="row px-0 mx-0">
			<h3>
				<small>글 수정하기</small>
			</h3>
		</div>
		<div class="card">
			<div class="card-body">
				@include($VIEW_PATH.'._form') {{-- Buttons --}}
				<div class="row">
					<div class="col-8">
						<button type="submit" class="btn btn-sm btn-primary" name="action" value="finished">저장</button>
						<a class="btn btn-sm btn-outline-secondary" href="{{ route($ROUTE_ID.'.index') }}" role="button">List</a>
					</div>
					<div class="col-4 text-right">
						<button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modal-delete">삭제</button>
					</div>
				</div>
				{{-- END of Buttons --}}
			</div>
		</div>
		<!-- //Card -->
	</form>
</div>
<!-- //.container-fluid -->
{{-- Confirm Delete --}}
<div class="modal fade" id="modal-delete" tabIndex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">확인</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="lead">정말 삭제하시겠습니까?</p>
			</div>
			<div class="modal-footer">
				<form method="POST" action="{{ route($ROUTE_ID.'.destroy',$item->id) }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}"> <input type="hidden" name="_method" value="DELETE">
					<button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">닫기</button>
					<button type="submit" class="btn btn-sm btn-danger">예</button>
				</form>
			</div>
		</div>
	</div>
</div>
@stop
