@extends('layouts.admin_layout') 
@section('title',"아카이브 카테고리 관리")
@section('title-layout',"아카이브 카테고리 관리")
@section('content')
<div class="container-fluid pt-4 pb-5">
	@include('layouts.modules.messages_and_errors')
	<div class="">
		<div class="my-3">
			<h3>카테고리 목록</h3>
			<a href="{{ route($ROUTE_ID.'.create') }}?unit={{ $parameters['unit'] }}" class="btn btn-sm btn-outline-success">카테고리 추가</a>
		</div>
		<ul class="nav nav-tabs">
			@if ($parameters['unit'] == 'G')
				<li class="nav-item"><a class="nav-link" href="{{ route($ROUTE_ID.'.index') }}?unit=D">개발 아카이브</a></li>
				<li class="nav-item"><a class="nav-link active" href="{{ route($ROUTE_ID.'.index') }}?unit=G">일반 아카이브</a></li>					
			@else
				<li class="nav-item"><a class="nav-link active" href="{{ route($ROUTE_ID.'.index') }}?unit=D">개발 아카이브</a></li>
				<li class="nav-item"><a class="nav-link" href="{{ route($ROUTE_ID.'.index') }}?unit=G">일반 아카이브</a></li>
			@endif
		</ul>
		<div class="list-group list-group-flush">
			@foreach ($categories as $item) <a href="{{ route($ROUTE_ID.'.edit',$item->id ) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">{{ $item->name }} <span class="badge badge-secondary badge-pill">{{ $item->count }}</span></a> @endforeach
		</div>
	</div>
</div>
@stop