@extends('layouts.software_single') 
@section('title',"{$item->software_name}")
@section('content')
<style>
.software-introduce h1,h2,h3,h4,h5{
    margin-top: 1.5em;
}
.software-introduce h1:first-child{
    margin-top: 0;
}
.sh-breadcrumb{
    background-color: transparent;
}
</style>
<script src="/assets/lib/github_get_latest_release_download.js"></script>
<link rel="stylesheet" type="text/css" href="/assets/lib/prism/softwares_menu/prism.css">
<script src="/assets/lib/prism/softwares_menu/prism.js"></script>
<script>
$(function() {
	GitHub_getLatestReleaseDownload($(".downlink-latest-release").attr("data-repo"),function(link){
		$(".downlink-latest-release").attr("href",link);
	});
});
</script>
<div style="background-color: #628AC7;" class="py-3">
	<div class="container py-3">
		<h1>Softwares</h1>
		<p class="lead mb-0">소프트웨어, 플러그인 등</p>
	</div>
</div>
<div class="pb-5" style="background-color: rgba(230, 230, 230, 0.25);">
	<div class="container">
		<ol class="breadcrumb sh-breadcrumb">
		  <li class="breadcrumb-item"><a href="/">Home</a></li>
		  <li class="breadcrumb-item active"><a href="/softwares">Softwares</a></li>
		</ol>
	<h1 class="display-4">{{ $item->software_name }}</h1>
	@if($item->github_enable)
	<a href="https://github.com/{{$item->github_user_id}}/{{$item->github_repo_name}}"
		class="badge badge-dark" target="_blank" >Repo</a>&nbsp; <a
		href="https://github.com/{{$item->github_user_id}}/{{$item->github_repo_name}}/releases"
		class="badge badge-dark" target="_blank">Releases</a>&nbsp; <a
		href="https://{{$item->github_user_id}}.github.io/{{$item->github_repo_name}}/"
		class="badge badge-dark" target="_blank">Examples</a>&nbsp; <a
		href="#" class="downlink-latest-release badge badge-success"
		data-repo="{{$item->github_user_id}}/{{$item->github_repo_name}}">Zip Download</a>
	@endif
	<hr>
	<p class="text-muted">{{ $item->description }}</p>
	<hr>
	
	@if($item->download_link)
	<div class="m-5 text-center">
		<a href="{{ $item->download_link }}"
			class="btn btn-lg btn-outline-primary" role="button"><strong>{{ $item->software_name }}</strong> 다운로드<br> <small>최신버전&nbsp;v{{ $item->version_latest }}</small>
		</a>
	</div>
	<hr>
	@endif
	
	@if($item->preview_link)
	<div class="text-center p-5">
		<img src="{{ $item->preview_link }}" class="img-thumbnail rounded img-fluid">
	</div>
	@endif

	<div class="card mt-2 mb-5">
		<div class="card-body software-introduce">
			{!! $item->contents !!}
		</div>
	</div>
</div>
</div>
@stop
