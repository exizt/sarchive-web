@extends('layouts.sarchive_layout')
@section('title',"폴더 신규")
@section('content')
<div class="container py-5">
	@include('layouts.modules.messages.messages_and_errors_bs4')
	<form class="form-horizontal prevent" role="form" method="POST"
		action="{{ route($ROUTE_ID.'.store') }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="archive_id" value="{{ $parameters['archive_id'] }}">

		<div class="card mt-3">
			<h5 class="card-header">폴더 추가</h5>
			<div class="card-body px-1 px-md-3">
				@include($VIEW_PATH.'._form')
				<div class="d-flex w-100 justify-content-between">
					<button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s">추가</button>
					<a class="btn btn-secondary btn-sm site-shortcut-key-z"
					href="{{ url()->previous() }}" role="button">취소</a>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection
