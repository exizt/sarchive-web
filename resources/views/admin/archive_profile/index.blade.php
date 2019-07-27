@extends('layouts.admin_layout') 
@section('title',"아카이브 프로필 관리") 
@section('content')
<div>
	<div class="row px-0 mx-0">
		<div class="d-flex w-100 justify-content-between">
			<h4 class="">아카이브 프로필 목록</h4>
			<small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
		</div>
	</div>
	<hr class="mt-1">
	<div class="list-group">
		@foreach ($masterList as $item) <a class="list-group-item list-group-item-action flex-column align-items-start" href="{{ route($ROUTE_ID.'.edit',$item->id) }}">
			<div class="d-flex w-100 justify-content-between">
				<h5 class="mb-1">{{ $item->name }}</h5>
				<small>{{ $item->created_at->format('Y-m-d') }}</small>
			</div>
			<p class="mb-1 pl-md-3 cz-item-summary">
				<small>{{ $item->text }}</small>
			</p>
		</a> @endforeach
	</div>
	<div class="d-flex w-100 justify-content-between">
		<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-outline-success btn-sm">신규</a>
	</div>
	<hr>
	<div class="text-xs-center">{{ $masterList->links() }}</div>
</div>
@stop