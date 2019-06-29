@extends('layouts.admin_layout') 
@section('title',"SKU 생성")
@section('title-layout',"SKU Serial Management")
@section('content')
<script>
$(function() {
	$(document).on("keypress", ":input:not(textarea)", function(event) {
	    if (event.keyCode == 13) {
	        event.preventDefault();
	    }
	});
});
</script>
<div class="container-fluid pt-4 pb-5">
	@if (session()->has('message'))
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>Message</strong> {{ session()->get('message') }}
	</div>
	@endif
	
	@if ($errors->any())
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<form class="form-horizontal" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<div class="row px-0 mx-0">
		<h3>
			<small>신규 생성</small>
		</h3>
	</div>
	<div class="card">
		<div class="card-body">
			@include($VIEW_PATH.'._form')
			<div class="">
				<button type="submit" class="btn btn-sm btn-primary" id="site-shortcut-key-s">저장</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ url()->previous() }}" role="button">취소</a>
			</div>
		</div>
	</div>
	</form>
</div>

@stop