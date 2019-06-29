@extends('layouts.software_single')

@section('title',"실수령액 세금 계산기 - for iOS")

@section('content')

<style>
pre {
	white-space: pre-wrap;
	word-break: normal;
}
</style>
<style>
.sec-sw-subject {
	background-color: rgba(68, 138, 199, 0.5);
	background-image: url("/resources/softwares/images/ios-salary-income/background-mockup-low.jpg");
	background-size: 1500px;
	opacity: 0.6;
	filter: alpha(opacity = 60); /* For IE8 and earlier */
}
</style>
<div class="sec-sw-subject py-2 py-sm-5">
	<div class="container py-2 py-sm-5">
		<h1 class="display-3" style="color: rgba(255, 255, 255, 1.0)">실수령액 세금
			계산기</h1>
		<p class="lead" style="color: rgba(255, 255, 255, 0.8)">Korea salary
			income tax calculator</p>
	</div>
</div>
<div class="" style="background-color: rgb(107, 108, 119)">
	<div class="container py-1">
		
	</div>
</div>
<div class="" style="background-color: rgba(68, 138, 199, 0.1)">
	<div class="container py-5">
		<div class="card">
			<div class="card-body">
				<h4 class="display-4" style="color: rgba(0, 0, 0, 0.95)">개인정보 취급 방침</h4>
				<a href="{{$link_apppage}}" class="btn btn-sm btn-secondary" role="button">앱 정보보기</a>
				<p class="pt-5">{!! $contents !!}</p>
			</div>
		</div>
	</div>
</div>

@stop
