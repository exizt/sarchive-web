@extends('layouts.admin_layout') 
@section('title',"관리자 모드")
@section('content')
<script>
$(function(){
	ajaxBlog();
	//ajaxSKU();
});
function ajaxBlog(){
	/*
	* Ajax 통신
	*/
	$.ajax({
		url : "/admin/post",
		method : "get",
		data : {
			ajax:"true",
			num : 15
		},
		dataType : "json",
		success : successEvent,
		error : function(xhr, status, error) {alert(error);}
	});
	/*
	* 성공시
	*/
	function successEvent(data)
	{
		var html;
		var selector_table = "#devscrap tbody";
		$(selector_table).html("");
		for(var i in data){
			var item = data[i];
			//var html = "<li>" + item.title + "</li>";
			var html = "<tr>"
			+ "<td><a href='/admin/post/"+item.id+"'>"+item.title+"</a></td>"
			+ "<td>"+item.created_at+"</td>"
			+ "<td>"+item.published_at+"</td>"
			+"</tr>";
			$(selector_table).append(html);
		}
	}
}
</script>
<style>
a:hover{
	/*text-decoration: none;*/
	/*color: rgba();*/
}
a{
	color: rgb(50,50,50);
}
.dashboard-container{
	padding-top: 2rem;
	padding-bottom: 3rem;
	background-color: rgb(240, 240, 240);
}
</style>
<div class="container-fluid dashboard-container">
	<div class="card">
		<div class="card-body">
			<h4>블로그 게시글 <small><a href="/admin/post">more..</a></small></h4>
			<table class="table table-sm table-hover" id="devscrap">
				<thead class="thead-inverse">
					<tr>
						<th>제목</th>
						<th>작성일</th>
						<th>발행일</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="3">로딩중...</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop
