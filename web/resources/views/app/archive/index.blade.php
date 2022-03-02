@extends('layouts.sarchive_layout')
@section('content')
<div class="container py-5">
	@if(count($masterList) == 0)
	<div class="text-center">
		<a class="btn btn-primary" href="{{ route($ROUTE_ID.'.create') }}">새 아카이브 생성하기</a>
	</div>
	@endif
	<div class="list-group">
		@foreach ($masterList as $item)
		<a class="list-group-item list-group-item-action flex-column align-items-start"
			href="{{ route('archive.first',$item->id) }}">
			<div class="d-flex w-100 justify-content-between">
				<h5 class="mb-1">{{ $item->name }}</h5>
				<small>{{ $item->created_at->format('Y-m-d') }}</small>
			</div>
			<p class="mb-1 pl-md-3 sarc-item-comments">
				<small>{{ $item->comments }}</small>
			</p>
		</a>
		@endforeach
	</div>
	<div class="text-right mt-2">
		<a href="{{ route($ROUTE_ID.'.editableIndex') }}" class="btn btn-sm">아카이브 편집</a>
	</div>
</div>
@endsection
