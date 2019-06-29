@extends('layouts.service')
@section('content')
<h1>아이피 주소 확인</h1>
<br>
<div class="card">
	<div class="card-body">
		Your IP : {{$ip}} <br><br>
	</div>
</div>
<div class="bs-callout bs-callout-primary">
	<h4>About 아이피 조회</h4>
	<p>본인의 접속 아이피 를 확인합니다.</p>
</div>
@stop