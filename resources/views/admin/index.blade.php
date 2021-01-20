@extends('layouts.sarchive_layout', ['layoutMode' => 'admin']) 
@section('title',"글 작성") 
@section('content')
<div>
	<h4>SArchive 설정</h4>
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">관리 페이지</h5>
			관리 페이지 입니다.

		</div>
	</div>
	<hr>
	<h4>버전 정보</h4>
	<ul class="list-group">
		<li class="list-group-item d-flex justify-content-between align-items-center">코드 버전 <small>{{ $source_ver }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">Laravel <small>{{ App::VERSION() }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">PHP <small>{{ $php_ver }}</small></li>
	</ul>
</div>
@endsection