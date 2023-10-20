{{-- bs4 style --}}
@if (session()->has('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<h4 class="alert-heading">알림</h4>
	{{ session()->get('message') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show"role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<h4 class="alert-heading">오류</h4>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
