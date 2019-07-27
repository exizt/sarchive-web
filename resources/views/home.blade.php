@extends('layouts.archive_layout') 
@section('title','S아카이브')
@section('content')
<div class="py-5">
	<div class="container">
		<div class="list-group">
			@foreach ($masterList as $item)
			<a class="list-group-item list-group-item-action flex-column align-items-start" href="{{ route('archives.index',$item->id) }}">
				<div class="d-flex w-100 justify-content-between">
					<h5 class="mb-1">{{ $item->name }}</h5>
					<small>{{ $item->created_at->format('Y-m-d') }}</small>
				</div>
				<p class="mb-1 pl-md-3 cz-item-summary">
					<small>{{ $item->text }}</small>
				</p>
			</a>
			@endforeach
		</div>
	</div>
</div>
@stop
