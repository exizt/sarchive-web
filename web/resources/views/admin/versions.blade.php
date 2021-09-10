@extends('layouts.sarchive_layout', ['layoutMode' => 'admin', 'currentMenu'=>'ver'])
@section('title',"글 작성")
@section('content')
<div>
	<h4>버전 정보</h4>
	<ul class="list-group">
		<li class="list-group-item d-flex justify-content-between align-items-center">코드 버전 <small>{{ $source_ver }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">Laravel <small>{{ App::VERSION() }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">PHP <small>{{ $php_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">MySQL <small>{{ $mysql_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">Apache <small>{{ $apache_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">임의 패스워드 생성 (26) <small>{{ $new_password_26 }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">임의 패스워드 생성 (31) <small>{{ $new_password_31 }}</small></li>
	</ul>
</div>
@endsection
