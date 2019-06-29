@extends('layouts.admin_layout') 
@section('title',"소프트웨어 제품 관리")
@section('title-layout',"소프트웨어 제품 관리")
@section('content')
<div class="container-fluid pt-4 pb-5">
	@include('layouts.modules.messages_and_errors')
	<div class="">
		<div class="row">
			<div class="col-6">
			<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-sm btn-outline-success">신규</a>
			</div>
    		<h6 class="col-6 text-right px-2">
    			<small class="text-mute">Page {{ $records->currentPage() }} of {{ $records->lastPage() }}</small>
    		</h6>
		</div>
    	<div class="text-right px-2">
    	</div>
    	<hr>
    	<div>
    		@foreach ($records as $item)
    		<div class="px-2">
    			<h5>
    				<a href="{{ route($ROUTE_ID.'.edit',$item->id) }}" class="text-dark">{{ $item->software_name }}</a>
    			</h5>
    			<h6>
    				<small class="text-muted">최근 수정 {{ $item->updated_at }}&nbsp;&nbsp;&nbsp;(생성 {{ $item->created_at }})</small>
    			</h6>
    		</div>
    		<hr>
    		@endforeach
    	</div>
	</div>
</div>
@stop