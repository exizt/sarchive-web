@extends('layouts.admin_layout') 

@section('title',"글 작성 - 블로그")
@section('title-layout',"포스트 관리")

@section('content')
<div class="container-fluid py-4">
	@if (session()->has('message'))
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong>(알림)</strong> {{ session()->get('message') }}
	</div>
	@endif
	
	<div class="row">
		<div class="col-4 col-md-6">
			<h3><small>내용 보기</small></h3>
		</div>
		<div class="col-8 col-md-6 text-right">
			<a class="btn btn-secondary btn-sm" href="{{ url()->previous() }}" role="button">Back</a>
			<a class="btn btn-primary btn-sm" href="{{ route($ROUTE_ID.'.index') }}" role="button">List</a>
			<a class="btn btn-success btn-sm" href="{{ route($ROUTE_ID.'.edit',$item->id) }}" role="button" id="site-shortcut-key-e">Edit</a>
		</div>
	</div>	
	<hr>
	<div class="form-group row">
		<label for="" class="col-md-2">명칭</label>
		<div class="col-md-10">
			<div>{{ $item->name }}</div>
		</div>
	</div>	
	<div class="form-group row">
		<label for="" class="col-md-2">변경 일시</label>
		<div class="col-md-10">
			<p>{{ $item->updated_at }}
				<small>(생성일시 {{ $item->created_at }})</small></p>
		</div>
	</div>	
	<div class="form-group row">
		<label for="" class="col-md-2">간략 설명</label>
		<div class="col-md-10">
			<div style="white-space: pre-wrap">{{ $item->description }}</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="" class="col-md-2">계산 과정</label>
		<div class="col-md-10">
			<div style="white-space: pre-wrap">{{ $item->process }}</div>
		</div>
	</div>	
	<div class="form-group row">
		<label for="" class="col-md-2">세율 기록</label>
		<div class="col-md-10">
			<div style="white-space: pre-wrap">{{ $item->history }}</div>
		</div>
	</div>		
	<hr>
	<a class="btn btn-primary btn-sm" href="{{ route($ROUTE_ID.'.index') }}" role="button">목록</a>
</div>
@stop