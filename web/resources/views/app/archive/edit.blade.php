@extends('layouts.sarchive_layout')
@section('title',"아카이브 편집")
@section('content')
<div class="container py-5">
	@include('modules.message.messages_and_errors.default')
	<form class="form-horizontal prevent" role="form" method="POST" action="{{ route($ROUTE_ID.'.update',$item->id) }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_method" value="PUT">

		<div class="card mt-3">
			<h5 class="card-header">편집</h5>
			<div class="card-body px-1 px-md-3">
				@include($VIEW_PATH.'._form')
				<div class="d-flex w-100 justify-content-between">
					<div>
						<button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s" name="action" value="finished">저장</button>
						<button type="submit" class="btn btn-outline-success btn-sm" name="action" value="continue">중간 저장</button>
						<a class="btn btn-outline-secondary btn-sm site-shortcut-key-z" href="{{ route($ROUTE_ID.'.editableIndex') }}" role="button">목록으로</a>
					</div>
					<button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modal-delete">삭제</button>
				</div>
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
			<form method="POST" action="{{ route($ROUTE_ID.'.destroy',$item->id) }}">
			<div class="modal-header">
				<h4 class="modal-title">확인</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="lead">정말 삭제하시겠습니까?</p>
				해당되는 게시물을 이동하게 될 아카이브 선택하기.
				<select name="will_move" class="form-control" title="아카이브 프로필 선택">
				@foreach ($archiveList as $item)
				<option value="{{ $item->id }}">{{ $item->name }}</option>
				@endforeach
				</select>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="_token" value="{{ csrf_token() }}"> <input type="hidden" name="_method" value="DELETE">
				<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
				<button type="submit" class="btn btn-danger">예</button>
			</div>
			</form>
		</div>
	</div>
</div>
@endsection
