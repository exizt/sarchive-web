@extends('layouts.archive_layout') 
@section('title',"$categoryName") 
@section('content')
<div>
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9">
					<div class="row px-0 mx-0">
						<div class="d-flex w-100 justify-content-between">
							<h4 class="">Archives ({{ $categoryName }})</h4>
							<small class="text-mute">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</small>
						</div>
						<p class="lead">{{ $archiveCategory->comment }}</p>
					</div>
					<hr class="mt-1">
					<div class="list-group">
						@foreach ($posts as $post) <a class="list-group-item list-group-item-action flex-column align-items-start" href="{{ route($ROUTE_ID.'.show',$post->id) }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $post->title }}</h5>
								<small>{{ $post->created_at->format('Y-m-d') }}</small>
							</div>
							<p class="mb-1 pl-md-3 cz-item-summary">
								<small><em>{{ $post->summary_var }}</em></small>
							</p>
							<div class="d-flex justify-content-between">
								<small>[{{ $post->category_name }}] {{ $post->created_at->format('Y-m-d') }}</small>
								<small>@if (!empty($post->reference)) <em>출처 : {{ $post->reference }}</em> @endif</small>
							</div>
						</a> @endforeach
					</div>
				</div>
				<div class="col-md-3">
					<h4 class="px-2">Categories</h4>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">	@foreach ($categoryPath as $item)	<li class="breadcrumb-item"><a href="{{ route($ROUTE_ID.'.index')}}?category={{$item->id}}">{{ $item->name }}</a></li> @endforeach
						</ol>
					</nav>
					<div class="list-group list-group-flush">
						@foreach ($subcategories as $item) <a href="{{ route($ROUTE_ID.'.index')}}?category={{ $item->id }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">{{ $item->name }} <span class="badge badge-secondary badge-pill">{{ $item->count }}</span></a> @endforeach
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $posts->appends(['category' => $parameters['categoryId']])->links() }}</div>
	</div>
</div>
@stop
