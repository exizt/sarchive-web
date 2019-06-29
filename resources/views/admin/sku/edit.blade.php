@extends('layouts.admin_layout') 
@section('title',"SKU 정보 수정")
@section('title-layout',"SKU Serial Management") 
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

	<form class="form-horizontal" role="form" method="POST"
		action="{{ route($ROUTE_ID.'.update',$item->id) }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="_method" value="PUT">		
	<div class="row px-0 mx-0">
		<h3>
			<small>글 수정하기</small>
		</h3>
	</div>

	<div class="card">
		<div class="card-body">
			<div class="form-group row">
				<label for="title" class="col-md-2 control-label">SKU</label>
				<div class="col-md-10">
					{{ $item->product_sku }}
				</div>
			</div>		
			@include($VIEW_PATH.'._form')
			{{-- Buttons --}}
			<div class="row">
				<div class="col-8">
					<button type="submit" class="btn btn-sm btn-primary" name="action"
						value="finished">저장</button>
					<a class="btn btn-sm btn-outline-secondary"
						href="{{ route($ROUTE_ID.'.index') }}" role="button">List</a>
				</div>
				<div class="col-4 text-right">
					<button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal"
						data-target="#modal-delete">삭제</button>
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
				<h4 class="modal-title">재질문</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="lead">
					<i class="fa fa-question-circle fa-lg"></i>&nbsp;&nbsp;정말 삭제하시겠습니까?
				</p>
			</div>
			<div class="modal-footer">
				<form method="POST"
					action="{{ route($ROUTE_ID.'.destroy',$item->id) }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}"> <input
						type="hidden" name="_method" value="DELETE">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger">
						<i class="fa fa-times-circle"></i> 네
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
@stop
