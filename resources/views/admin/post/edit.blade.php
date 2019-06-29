@extends('layouts.admin_layout') 
@section('title',"글 수정 - 블로그")
@section('title-layout',"Blog > Edit Article") 
@section('content')

@if($editor =='simplemde')
<link rel="stylesheet" href="/assets/lib/simplemde/simplemde.min.css">
<script src="/assets/lib/simplemde/simplemde.min.js"></script>
<script>
$(function(){
	/*
	* simpleMDE (https://simplemde.com)
	*/
	var simplemde = new SimpleMDE({ 
		element: document.getElementById("content"),
		spellChecker: false,
		autoDownloadFontAwesome : false
	});
});
</script>
@endif
<link rel="stylesheet" href="/assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.css">
<script src="/assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script>
/* include libraries */

/* custom script */
$(function() {
	// prevent enter key event to submit
	$('form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
		    e.preventDefault();
		    return false;
		}
	});
});
</script>
<style>
@media screen and (max-width:500px) {
	.CodeMirror{
		height: 600px;
	}
}
</style>
<div class="container-fluid py-4" style="background-color: rgba(233,233,233,0.7)">
	<form class="form-horizontal" role="form" method="POST" action="{{ route('admin.post.update',$post->id) }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="_method" value="PUT">	

    <div class="card mt-2">
       <div class="card-body">
			@include('admin.post._form')
			<div class="form-group row">
				<div class="col-md-10 col-md-offset-2">
                 	<a class="btn btn-secondary" href="{{ route('admin.post.index') }}" role="button">Cancel</a>
                    <button type="submit" class="btn btn-primary" name="action" value="finished">Save</button>
                    <button type="submit" class="btn btn-success" name="action" value="continue">Save(C)</button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete">
                      <span class="d-none d-sm-block">Delete</span>
                      <span class="d-sm-none">Del</span>
                    </button>
                 </div>
             </div>
		</div>
    </div>
	</form>
	@include('admin.post._tools')
</div>

{{-- Confirm Delete --}}
<div class="modal fade" id="modal-delete" tabIndex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Please Confirm</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p class="lead">
					<i class="fa fa-question-circle fa-lg"></i>&nbsp;&nbsp;Are you sure you want to delete this post?
				</p>
			</div>
			<div class="modal-footer">
			<form method="POST" action="{{ route($ROUTE_ID.'.destroy',$post->id) }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_method" value="DELETE">
				<button type="button" class="btn btn-default"
					data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-danger">
				<i class="fa fa-times-circle"></i> Yes
				</button>
			</form>
			</div>
		</div>
	</div>
</div>
@stop