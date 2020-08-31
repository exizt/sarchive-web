@extends('layouts.page_layout') 
@section('title',"") 
@section('content') 
<div class="container-fluid mt-4 mb-5">
	<div class="card">
		<div class="card-body">{!! $page->content !!}</div>
	</div>
</div>
@endsection
