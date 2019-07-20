@extends('layouts.archive_layout') 
@section('title',"검색 결과") 
@section('content')
<div>
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9">
					<div class="row px-0 mx-0">
						<div class="d-flex w-100 justify-content-between">
							<h4 class="">Archives</h4>
							<small class="text-mute">Page {{ $articles->currentPage() }} of {{ $articles->lastPage() }}</small>
						</div>
					</div>
					<hr>
					<div class="list-group">
						@foreach ($articles as $article) <a class="list-group-item list-group-item-action flex-column align-items-start" href="{{ route($ROUTE_ID.'.show',$article->id) }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $article->title }}</h5>
								<small>{{ $article->created_at->format('Y-m-d') }}<br>카테고리 : {{ $article->category_name }}
								</small>
							</div>
							<p class="mb-1 pl-3">
								<small><em>{{ $article->summary_var }}</em></small>
							</p>
						</a> @endforeach
					</div>
				</div>
				<div class="col-md-3">
					<h4 class="px-2">Categories</h4>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							@foreach ($categoryPath as $item)
							<li class="breadcrumb-item"><a href="{{ route($ROUTE_ID.'.index')}}?category={{$item->id}}">{{ $item->name }}</a></li> @endforeach
						</ol>
					</nav>
					<div class="list-group list-group-flush">
						@foreach ($subcategories as $item) <a href="{{ route($ROUTE_ID.'.index')}}?category={{ $item->id }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">{{ $item->name }} <span class="badge badge-secondary badge-pill">{{ $item->count }}</span></a> @endforeach
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $articles->appends(['q' => $parameters['q'],'profile'=> $parameters['profile']])->links() }}</div>
	</div>
</div>
@stop
