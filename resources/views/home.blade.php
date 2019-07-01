@extends('layouts.mainpage') 

@section('title','HelloWorld')

@section('content')
<script src="/assets/lib/jquery-plugins-my/jquery-rollover/jquery.rollover.js"></script>
<div class="py-5 d-none d-sm-block" style="background-color: #628AC7;">
	<div class="container py-5 text-center">
		<h1 class="display-1">S Archive Demo</h1>
		<p class="lead">Source Archive</p>
	</div>
</div>
<div class="d-sm-none" style="background-color: #628AC7;">
	<div class="container py-5 text-center">
		<h1 class="">S Archive Demo</h1>
		<h3>Source Archive</h3>
	</div>
</div>
<div class="py-5" style="background-color: #eee;">
	<div class="container">
	</div>
</div>
@stop
