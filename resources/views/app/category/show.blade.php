@extends('layouts.archive_layout') 
@section('title',"") 
@section('content')
<div>
	<div class="mt-4 mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9">
					<div class="row px-0 mx-0">
						<div class="d-flex w-100 justify-content-between">
							<h4 class="">분류 : {{ $category->name }}
								&nbsp;&nbsp;&nbsp;
							<a class="btn btn-outline-info btn-sm site-shortcut-key-e" 
								href="{{ route($ROUTE_ID.'.edit',['archiveId'=>$archive->id,'category'=>$category->id]) }}" 
								role="button">편집</a>
						</h4>
						<small class="text-mute">Page {{ $masterList->currentPage() }}/{{ $masterList->lastPage() }}</small>
						</div>
						<p class="lead">{{ $category->comments }}</p>
					</div>
					<h6>여기에 속하는 문서</h6>
					<div class="list-group">
						@foreach ($masterList as $item) 
						<a class="list-group-item list-group-item-action flex-column align-items-start" 
							href="/doc/{{ $item->id }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $item->title }}</h5>
								<small>{{ $item->created_at->format('Y-m-d') }}</small>
							</div>
							<p class="mb-1 pl-md-3 cz-item-summary">
								<small>{{ $item->summary_var }}</small>
							</p>
							<div class="d-flex justify-content-between">
								<small>게시판 : {{ $item->category_name }}</small>
							</div>
						</a> @endforeach
					</div>
					@if(count($childCategories))
					<hr>
					<h6>여기에 속하는 분류</h6>
					<div class="list-group">
						@foreach ($childCategories as $item)
						<a class="list-group-item list-group-item-action"
							href="{{route($ROUTE_ID.'.show',['archiveId'=>$archive->id,'category'=>urlencode($item)])}}">
							{{$item}}
						</a>
						@endforeach
					</div>
					@endif
					@if(count($category->category_array))
					<hr>
					<div class="card">
						<div class="card-body">
							상위 분류&nbsp;:&nbsp;&nbsp;
							@foreach ($category->category_array as $i=>$item)
								@if($i>0) | @endif
								<a href="{{route($ROUTE_ID.'.show',['archiveId'=>$archive->id,'category'=>urlencode($item)])}}">
									{{$item}}
								</a>&nbsp;
							@endforeach
						</div>
					</div>
					@endif
				</div>
				<div class="col-md-3" id="nav-folders" data-current-folder-id="">
                    <h4 class="px-2">현재 위치</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb" id="shh-nav-board-path"></ol>
                    </nav>
                    <h5>게시판</h5>
                    <div class="list-group" id="shh-nav-board-list"></div>
                    <div class="list-group pt-3" id="js-folderNav-folderOnly" style="display:none"></div>
                </div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $masterList->links() }}</div>
	</div>
</div>
<style>
.shh-navboardlist-depth-1{
	/*padding-left: 1.75rem;*/
}
.shh-navboardlist-depth-2{
	padding-left: 3.5rem;
}
.shh-navboardlist-depth-3{
	padding-left: 6.0em;
}
.shh-navboardlist-depth-4{
	padding-left: 8.5rem;
}
</style>
<script>
    $(function(){
        doAjaxFolderList()
    })
</script>
@endsection
