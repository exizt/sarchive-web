@extends('layouts.page_layout') 
@section('title',"") 
@section('content') 
<div class="container-fluid mt-4 mb-5">
	<div class="card">
		<div class="card-body"><h3>단축키</h3>
			<br> <kbd>Alt + Shift</kbd> 다음에 아래의 키를 누른다.<br><br><br>
			<h4>예약된 키</h4>
			<ol>
			<li>글쓰기 : <span class="badge badge-success">N</span> or <span class="badge badge-success">A</span></li>
			<li>글수정 : <span class="badge badge-success">E</span></li>
			<li>검색 포커스 : <span class="badge badge-success">F</span></li>
			<li>저장 : <span class="badge badge-success">S</span></li>
			</ol>
		</div>
	</div>
</div>
@endsection
