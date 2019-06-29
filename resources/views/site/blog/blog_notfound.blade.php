@extends('layouts.blog') 

@section('title'," - 블로그")
@section('meta-description',"")
@section('layout-subheader-title',"")
@section('layout-subheader-description',"")
@section('layout-subheader-background',"")

@section('content')
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/prism.css">
<script src="/assets/lib/prism/prism.js"></script>

<div class="container mt-1 mb-5">
	<h6><small>Read Post</small></h6>
	<hr>
	잘못된 경로입니다.
	<hr>
	<a href="/blog" class="btn btn-primary">List</a>
	<button class="btn btn-secondary" onclick="history.go(-1)">Back</button>
</div>
@stop