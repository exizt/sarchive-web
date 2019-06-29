@extends('layouts.software_single')

@section('title',"개인정보처리방침 - {$item->software_name}")

@section('content')

<style>
pre {
	white-space: pre-wrap;
	word-break: normal;
}
</style>
<style>
.header-display{
	padding: 20px;
	background: rgba(95, 135, 196, 1.0); /* For browsers that do not support gradients */
	background: -webkit-linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.8)); /* For Safari 5.1 to 6.0 */
	background: -o-linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.8)); /* For Opera 11.1 to 12.0 */
	background: -moz-linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.8)); /* For Firefox 3.6 to 15 */
	background: linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.7)); /* Standard syntax */
}
</style>
<div class="header-display">
	<div class="container py-5">
		<h1 class="display-4" style="color: rgba(255, 255, 255, 0.8)">{{ $item->subject }}</h1>
		<p class="lead" style="color: rgba(255, 255, 255, 0.8)">{{ $item->description }}</p>
	</div>
</div>

<div class="" style="background-color: rgba(68, 138, 199, 0.1)">
	<div class="container py-5">
		<div class="card">
			<div class="card-body">
				<h4 class="display-4" style="color: rgba(0, 0, 0, 0.95)">개인정보 취급 방침</h4>
				<a href="{{ $item->link }}" class="btn btn-sm btn-secondary" role="button">앱 정보보기</a>
				<p class="pt-5">{!! $item->privacy_statement !!}</p>
			</div>
		</div>
	</div>
</div>

@stop
