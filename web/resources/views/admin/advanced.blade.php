@extends('layouts.sarchive_layout', ['layoutMode' => 'admin', 'currentMenu'=>'advanced']) 
@section('title',"글 작성") 
@section('content')
<div>
	<h3>고급 기능</h3>
	<ul class="list-group">
		<li class="list-group-item">게시판 분류 트리 갱신 (프로시저 실행)</li>
		<li class="list-group-item">게시판이 지정되지 않은 게시물 조회</li>
		<li class="list-group-item">분류가 없는 게시물 조회</li>
	</ul>
	<h4>버그 체크</h4>
	<ul class="list-group">
		<li class="list-group-item">게시물ID 와 프로필ID 의 오차가 생긴 게시물 조회</li>
	</ul>
</div>
@endsection