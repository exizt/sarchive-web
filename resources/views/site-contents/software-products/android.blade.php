@extends('layouts.software_single') 
@section('title',"{$item->software_name}")
@section('meta-description',"실수령액 계산기 Android")
@section('content')
<link rel="stylesheet" type="text/css"
	href="/assets/lib/jquery-plugins/jquery.slick/1.6.0/slick.css" />
<link rel="stylesheet" type="text/css"
	href="/assets/lib/jquery-plugins/jquery.slick/1.6.0/slick-theme.css" />
<script type="text/javascript"
	src="/assets/lib/jquery-plugins/jquery.slick/1.6.0/slick.min.js"></script>
<script>
$(document).ready(function(){
	$(".preview-images").slick({
		dots: false,
		infinite: false,
		autoplay: true,
		autoplaySpeed: 5000,
		slidesToShow: 1,
		slidesToScroll: 1,
		adaptiveHeight: false,
		arrows: false
	});
});
</script>
<style>
.header-display{
	padding: 20px;
	background: rgba(95, 135, 196, 1.0); /* For browsers that do not support gradients */
	background: -webkit-linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.8)); /* For Safari 5.1 to 6.0 */
	background: -o-linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.8)); /* For Opera 11.1 to 12.0 */
	background: -moz-linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.8)); /* For Firefox 3.6 to 15 */
	background: linear-gradient(-35deg, rgba(95, 135, 196, 1.0), rgba(94, 177, 198, 0.7)); /* Standard syntax */
}
.preview-images-wrap{
	background-image: url("/assets/images/nexus_5x_bg.png");
	width: 294px;
	height: 598px;
}
.preview-images{
	width: 283px;
	/*margin-left: 13px;*/
	padding-left: 13px;
	padding-top: 60px;
}
@media ( max-width : 576px) {
.preview-images-wrap{
	width: 147px;
	height: 299px;
	background-size: 147px 299px;
}
.preview-images{
	width: 141px;
	padding-left: 6px;
	padding-top: 30px;
}
}

</style>
<div class="header-display">
	<div class="container py-5">
		<h1 class="display-4" style="color: rgba(255, 255, 255, 0.8)">{{ $item->subject }}</h1>
		<p class="lead" style="color: rgba(255, 255, 255, 0.8)">{{ $item->description }}</p>
	</div>
</div>
<div class="py-3" style="background-color:#eee">
	<div class="container">
		<div class="card">
			<div class="card-body media">
				<div class="d-flex mr-3">
					<div class="preview-images-wrap">
						<div class="preview-images" role="listbox">
							@foreach ($item->screenshots as $screenshot)
							<div>
								<div class="image" style="border:0">
									<img src="{{$screenshot}}" class="img-fluid">
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
				<div class="media-body">
					<h2>{{ $item->description }}</h2>
					<p>{!! $item->contents !!}</p>
					<div class="m-5 text-center">
						<a href="{{ $item->store_link }}" target="_blank"><img src="/assets/images/Get_it_on_Google_play.svg" ></a>
					</div>
					<a href="{{ $item->privacy_link }}" class="btn btn-sm btn-secondary" role="button">개인정보 처리 방침</a>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
