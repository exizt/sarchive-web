@extends('layouts.sarchive_layout', ['layoutMode' => 'admin', 'currentMenu'=>'version'])
@section('title',"버전 정보")
@section('content')
<div>
	<h4>버전 정보</h4>
	<ul class="list-group">
		<li class="list-group-item d-flex justify-content-between align-items-center">소스 코드 <small>{{ $source_ver }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">라라벨 <small>{{ $laravel_ver }}</small></li>
		<li class="list-group-item d-flex justify-content-between align-items-center">PHP <small>{{ $php_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">{{ $db_label }} <small>{{ $db_ver }}</small></li>
        <li class="list-group-item d-flex justify-content-between align-items-center">{{ $wss_label }} <small>{{ $wss_ver }}</small></li>
	</ul>
</div>
@endsection
