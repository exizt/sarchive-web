{{-- Dashboard in MyService 
Todo
* 간략 스터디 항목
* 오늘 일정
--}}
@extends('layouts.myservice_layout') 
@section('title',"대시보드 - MYSERV")
@section('title-layout',"대시보드") 
@section('content')
<script>
$(function(){
	//appendAjax();
});
function appendAjax(){
	//ajaxSKU();
}
</script>
<style>
a:hover{
	/*text-decoration: none;*/
	/*color: rgba();*/
}
a{
	color: rgba(50,50,50,1.0);
}
.dashboard-container{
	padding-top: 2rem;
	padding-bottom: 3rem;
	background-color: rgba(200,200,200,0.25);
}
</style>
{{$today}}
<div class="dashboard-container">
	<div class="container">
		<div class="row mb-4">
			<div class="col-12">
    			<div class="card">
    				<h4 class="card-header">간략 스터디</h4>
    				<div class="card-body">
    				<h5>오늘의 명언</h5>
    				<p>열정을 잃지 않고 실패에서 실패로 걸어가는 것이 성공이다. – 윈스턴 처칠</p>
    				<h5>사자성어</h5>
    				<p>溫故知新(온고지신) - 옛것을 익혀 새것을 앎.《論語》</p>
    				<h5>English 명언</h5>
    				<p>Practice makes perfect. 연습은 완벽을 만든다.</p>
    				<h5>English 단어</h5>
    				<p>보통 몇 시에 일어나요? What time do you get up? </p>
    				</div>
    			</div>			
			</div>
		</div>
		<div class="row">
			<div class="col-12 mb-4">
    			<div class="card">
    				<h4 class="card-header">오늘</h4>
    				<div class="card-body">
    					:여기에 오늘 일정이 들어감.
    					지인 생일, 예정된 일정, 특수한 일.
    				</div>
    			</div>			
			</div>
		</div>
	</div>	
</div>
@stop
