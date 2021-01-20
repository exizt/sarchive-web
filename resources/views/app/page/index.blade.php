@extends('layouts.sarchive_layout') 
@section('title',"") 
@section('content')
<div>
	<div class="row px-0 mx-0">
		<div class="d-flex w-100 justify-content-between">
			<h4 class="">페이지 목록</h4>
			<small class="text-mute">Page {{ $masterListSet->currentPage() }} of {{ $masterListSet->lastPage() }}</small>
		</div>
	</div>
	<hr class="mt-1">
	<div class="list-group">
		@foreach ($masterListSet as $item) <a class="list-group-item list-group-item-action flex-column align-items-start" href="{{ route($ROUTE_ID.'.show',$item->title) }}">
			<div class="d-flex w-100 justify-content-between">
				<h5 class="mb-1">{{ $item->title }}</h5>
				<small>{{ $item->created_at->format('Y-m-d') }}</small>
			</div>
		</a> @endforeach
	</div>
	<hr>
	<div class="text-xs-center">{{ $masterListSet->links() }}</div>
</div>
@endsection
