@extends('layouts.blog') 

@section('title',"블로그 Tags")
@section('meta-description',"블로그 태그 목록")
@section('meta-title',"블로그 태그 목록 - 언제나 초심 블로그")
@section('layout-subheader-title',"블로그 태그 목록")
@section('layout-subheader-description',"")

@section('content')
<script src="/assets/js/site-myservice.js"></script>
<div>
	<div class="container-fluid">
		<div class="row px-0 mx-0">
			<h1 class="col-8 col-md-6"><i class="fa fa-tags"></i>&nbsp;태그 목록</h1>
		</div>
		
	    <div class="text-right px-2">
	    	<h6><small class="text-mute">Page {{ $tags->currentPage() }} of {{ $tags->lastPage() }}</small></h6>
	    </div>
	    <hr>
	    <div>
	      @foreach ($tags as $item)
	        <div class="px-2">
	          <h5><a href="{{ route($ROUTE_ID.'.tags.show',$item->tag) }}">{{ $item->tag }}</a></h5>
	          <h6><small class="text-muted">{{ $item->created_at->format('Y-m-d g:ia') }} ({{ $item->updated_at->format('Y-m-d g:ia') }})</small></h6>
	        </div>
	        <hr>
	      @endforeach
	    </div>
	</div>
    <hr>
    <div class="pl-3">
    {{ $tags->links() }}
    </div>
 </div>
@stop