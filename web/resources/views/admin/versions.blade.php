@extends('layouts.sarchive_layout', ['layoutMode' => 'admin', 'currentMenu'=>'ver'])
@section('title',"글 작성")
@section('content')
<div>
	<h4>버전 정보</h4>
	<ul class="list-group">
		<li class="list-group-item d-flex justify-content-between align-items-center">코드 버전 <small>{{ $source_ver }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">Laravel <small>{{ $laravel_ver }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">PHP <small>{{ $php_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">MySQL <small>{{ $db_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">Apache <small>{{ $wss_ver }}</small></li>
	</ul>
</div>
@endsection
