@extends('layouts.admin_layout') 
@section('title',"소프트웨어 제품 관리")
@section('title-layout',"소프트웨어 제품 관리")
@section('content')
<div class="container-fluid pt-4 pb-5">
	@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li> @endforeach
		</ul>
	</div>
	@endif

	<form class="form-horizontal" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}" enctype="multipart/form-data">
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