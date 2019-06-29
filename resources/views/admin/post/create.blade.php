@extends('layouts.admin_layout') 

@section('title',"글 작성 - 블로그")
@section('title-layout',"Blog > Write Article") 
@section('content')
<link rel="stylesheet" href="/assets/lib/simplemde/simplemde.min.css">
<script src="/assets/lib/simplemde/simplemde.min.js"></script> 
<link rel="stylesheet" href="/assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.css">
<script src="/assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script>
/* include libraries */
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
<div class="container-fluid py-4" style="background-color: rgba(233,233,233,0.7)">
    <div class="card">
       <div class="card-body">
       <form class="form-horizontal" role="form" method="POST"
                  action="{{ route('admin.post.store') }}">
       <input type="hidden" name="_token" value="{{ csrf_token() }}">

              @include('admin.post._form')

          <div class="form-group row">
             <div class="col-md-10 col-md-offset-2">
             	<a class="btn btn-secondary" href="{{ route('admin.post.index') }}" role="button">Cancel</a>
                 <button type="submit" class="btn btn-primary">
                      <i class="fa fa-floppy-o"></i>
                      Save New Post
                 </button>
             </div>
          </div>
       </form>
       </div>
    </div>
	@include('admin.post._tools')
</div>

@stop