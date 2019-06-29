@extends('layouts.admin_layout') 
@section('title',"블로그") 
@section('title-layout',"Blog > Tags") 
@section('content')
<div class="container-fluid">
	<div class="text-right px-2">
		<h6>
			<small class="text-mute">Page {{ $tags->currentPage() }} of {{ $tags->lastPage() }}</small>
		</h6>
	</div>
	<hr>
	<div>
		@foreach ($tags as $item)
		<div class="px-2">
			<h5>
				<a href="{{ route('blog.tags.show',$item->tag) }}" class="text-dark">{{ $item->tag }}</a>
			</h5>
			<h6>
				<small class="text-muted">{{ $item->created_at->format('Y-m-d g:ia') }} ({{ $item->updated_at->format('Y-m-d g:ia') }})</small>
			</h6>
		</div>
		<hr>
		@endforeach
	</div>
	<div class="pl-3">{{ $tags->links() }}</div>
	<hr>
</div>
@stop
