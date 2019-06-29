@extends('layouts.software_single') 

@section('title',"실수령액 세금 계산기 - for iOS")
@section('meta-description',"iOS 실수령액 계산기 앱. 실수령액 세금 계산기. korea-salary-income-tax-calculator")

@section('content')
<style>
.sec-sw-subject{
	background-color: rgba(68, 138, 199, 0.5);
	background-image: url("/resources/softwares/images/ios-salary-income/background-mockup-low.jpg");
	background-size: 1500px;
	opacity: 0.6;
    filter: alpha(opacity=60); /* For IE8 and earlier */
}

</style>
<div class="sec-sw-subject py-2 py-sm-5">
	<div class="container py-2 py-sm-5">
		<h1 class="display-3" style="color: rgba(255, 255, 255, 1.0)">실수령액 세금 계산기</h1>
		<p class="lead" style="color: rgba(255, 255, 255, 0.8)">Korea salary income tax calculator</p>
		<img class="d-md-none mx-auto d-block" src="/resources/softwares/images/ios-salary-income/iphone-scr-001.png" width="250" />
	</div>
</div>
<div class="" style="background-color: rgba(68, 138, 199, 0.2)">
	<div class="container py-5">
		<div class="media">
			<img class="mr-3 d-none d-md-block" src="/resources/softwares/images/ios-salary-income/iphone-scr-001.png" width="250" />
			<div class="media-body">
				<h2 class="mt-0">실제 수령액을 계산해보세요.</h2>
				<p>내가 받게 되는 금액은 얼마일까?<br>세금은 얼마 나가고, 보험액은 어떻게 될까?</p>
				<h3>장점</h3>
				<ul>
				<li>이 앱은 2017년 2월 기준으로 세율을 갱신했습니다.</li>
				<li>최소한의 터치를 고민했습니다.</li>
				<li>금액을 입력하면 바로 반응해서 실수령액을 계산합니다.</li>
				<li>매번 입력하는 부분을 직접 설정할 수 있게 하였습니다. </li>
				<li>직접 세율을 변경하고, 실수령액이 어떻게 변동되는지 확인할 수 있습니다.</li>
				</ul>
				<div class="m-5 text-center">
					<a href="{{ $link_store }}" target="_blank"><img src="/assets/images/Download_on_the_App_Store_Badge_US-UK_135x40.svg" ></a>
				</div>
				<a href="{{ $link_privacy }}"
			class="btn btn-sm btn-secondary" role="button">개인정보 처리 방침</a>
			</div>
		</div>
	</div>
</div>

@stop
