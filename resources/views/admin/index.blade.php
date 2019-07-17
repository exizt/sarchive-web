@extends('layouts.admin_layout') 
@section('title',"글 작성") 
@section('content')
<div class="container-fluid mt-4 mb-5">
	<div class="row">
		<div class="col-md-2">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">게시판 설정</li>
				<li class="list-group-item">페이지 설정</li>
				<li class="list-group-item">아카이브 프로필 설정</li>
				<li class="list-group-item">&nbsp;</li>
				<li class="list-group-item">&nbsp;</li>
			</ul>
		</div>
		<div class="col-md-10">
			<h4>게시판 목록</h4>
			<div class="card">
				<div class="card-body">
				<h5 class="card-title">게시판 목록</h5>
				<ul class="nav nav-tabs" id="navTab">
					<li class="nav-item"><a class="nav-link active" href="#" data-profile="1">개발 아카이브</a></li>
					<li class="nav-item"><a class="nav-link" href="#" data-profile="2">일반 아카이브</a></li>
				</ul>
				</div>
			</div>
		</div>
		<div class="container-fluid pt-4 pb-5">
				<div class="">
					<div class="my-3">
						<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-sm btn-outline-success">카테고리 추가</a>
					</div>
					<div id="jstree"></div>
				</div>
			</div>					
	</div>
</div>
@stop