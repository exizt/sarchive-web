@extends('layouts.admin_layout') 
@section('title',"버전 정보") 
@section('title-layout',"운영 > 버전 정보") 
@section('content')
<div class="container-fluid py-4" style="background-color: rgb(240, 240, 240)">
	<h4>정보</h4>
	<ul class="list-group">
		<li class="list-group-item d-flex justify-content-between align-items-center">Laravel <small>{{ App::VERSION() }}</small></li>		
		<li class="list-group-item d-flex justify-content-between align-items-center">PHP <small>{{ $PHP_VERSION }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">MySQL <small>{{ $MYSQL_VERSION }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">Apache <small>{{ $APACHE_VERSION }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">임의 패스워드 생성 <small>{{ $newPassword }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">최근 업데이트 <small>{{ $latestModified }}</small></li>
	</ul>
</div>
@stop
