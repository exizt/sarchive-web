@extends('layouts.admin_layout') 
@section('title',"글 작성") 
@section('content')
<div>
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="card mb-3">
				<div class="row no-gutters">
					<div class="col-md-4">
						<ul class="list-group">
							<li class="list-group-item">게시판 설정</li>
							<li class="list-group-item">페이지 설정</li>
							<li class="list-group-item">아카이브 프로필 설정</li>
							<li class="list-group-item">&nbsp;</li>
							<li class="list-group-item">&nbsp;</li>
						</ul>
					</div>
					<div class="col-md-8">
						<div class="card-body">
						<h5 class="card-title">게시판 분류 설정</h5>
						<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
						<p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop