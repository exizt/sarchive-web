@extends('layouts.software_single') 

@section('title','Softwares')

@section('content')
<script src="/assets/lib/jquery-plugins-my/jquery-rollover/jquery.rollover.js"></script>
<style>
.sh-container-cardlist-on{
	box-shadow: 1px 1px 2px #888888;
	/*border: 1px;*/
}
.card-title a{
	text-decoration:none;
	color: inherit;
}
.sh-breadcrumb{
	padding-bottom: 0.25rem;
	padding-left: 0.25rem;
	margin-bottom: 0;
	background-color: inherit;
}
.sh-card-top{
	height:150px;
}
.sh-card-top-noimage{
	background-color:#aaa;color:#eee;
}
</style>
<script>
$(document).ready(function(){
	appearCardItem();
});
$(function() {
	$(".sh-container-cardlist .card").rollOver({
		change : "class",
		over : "sh-container-cardlist-on"
	}).on("click",function(){
		var link = $(this).attr("data-link");
		location.href = link;
	});
});

function appearCardItem(){
	$(".sh-container-cardlist .card").css("opacity",0.0);
	$(".sh-container-cardlist .card").each(function(j){
		$(this).delay(390*j).animate({opacity: 1.0},700);
	});
}
</script>
<div style="background-color: #628AC7;" class="py-3">
	<div class="container py-3 text-center">
		<h1>Softwares</h1>
		<p class="lead mb-0">소프트웨어, 플러그인 등</p>
		<div class="sh-container-cardlist sh-container-softwares mb-5 text-center pt-5">
			<div class="row">
				@foreach ($records as $item)
				<div class="col-sm-6 col-md-4 px-2 pb-3">
					<div class="card text-right" data-link="{{ $item->link }}">
						@if (empty($item->image))
						<div class="card-body sh-card-top sh-card-top-noimage">no image</div>
						@else
						<img class="card-img-top sh-card-top" src="{{ $item->image }}">
						@endif
						<div class="card-body">
							<h4 class="card-title">{{ $item->software_name }}</h4>
							<p class="card-text">
							<small class="text-muted">{{ $item->description }}</small>
							</p>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
@stop
