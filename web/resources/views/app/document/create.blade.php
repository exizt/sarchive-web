@extends('layouts.sarchive_layout')
@section('title',"글 작성")
@section('content')
<div class="container-fluid mt-4 mb-5">
	@include('messages.messages_and_errors_bs4')
	<form class="form-horizontal prevent" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="archive_id" value="{{ $parameters['archive_id'] }}">

		<div class="card mt-3">
			<h5 class="card-header">신규 글 작성</h5>
			<div class="card-body px-1 px-md-3">
				@include($VIEW_PATH.'._form')
				<div>
					<button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s">저장</button>
					<button type="submit" class="btn btn-outline-success btn-sm" name="action" value="continue">저장 후 계속 편집</button>
					<a class="btn btn-outline-secondary btn-sm site-shortcut-key-z" href="javascript:history.back()" role="button">취소</a>
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
