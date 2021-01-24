@extends('layouts.sarchive_layout') 
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
								href="{{ route('category.edit',['archive'=>$archive->id,'category'=>$category->id]) }}" 
								role="button">편집</a>
						</h4>
						<small class="text-mute">Page {{ $masterList->currentPage() }}/{{ $masterList->lastPage() }}</small>
						</div>
						<p class="lead">{{ $category->comments }}</p>
					</div>
					@if(count($childCategories))
					<h5>하위 분류</h5>
					<div class="row mb-5 mx-0">
						@foreach ($childCategories as $item)
						<div class="list-group col mx-2">
							<a class="list-group-item list-group-item-action"
								href="{{route('explorer.category',['archive'=>$archive->id,'category'=>urlencode($item)])}}">
								{{$item}}
							</a>
						</div>
						@endforeach
					</div>
					
					@endif					
					<h5>여기에 속하는 문서</h5>
					<div class="list-group">
						@foreach ($masterList as $item) 
						<a class="list-group-item list-group-item-action flex-column align-items-start" 
							href="/doc/{{ $item->id }}?lcategory={{ urlencode($category->name) }}">
							<div class="d-flex w-100 justify-content-between">
								<h5 class="mb-1">{{ $item->title }}</h5>
								<small>{{ $item->created_at->format('Y-m-d') }}</small>
							</div>
							<p class="mb-1 pl-md-3 cz-item-summary">
								<small>{{ $item->summary_var }}</small>
							</p>
						</a> @endforeach
					</div>

					@if(count($category->category_array))
					<hr>
					<div class="card">
						<div class="card-body">
							상위 분류&nbsp;:&nbsp;
							<ul class="sarc-cat-list">
							@foreach ($category->category_array as $i=>$item)
							<li><a href="{{route('explorer.category',['archive'=>$archive->id,'category'=>urlencode($item)])}}">
									{{$item}}
								</a></li>
							@endforeach
							</ul>
						</div>
					</div>
					@endif
				</div>
				<div class="col-md-3">
                    <h4 class="px-2">현재 위치</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb" id="shh-nav-board-path"></ol>
                    </nav>
                    <h5>폴더</h5>
                    <div class="list-group sarc-layout-nav-folder-list" id="shh-nav-board-list"></div>
                    <div class="list-group pt-3" id="js-folderNav-folderOnly" style="display:none"></div>
                </div>
			</div>
		</div>
		<hr>
		<div class="text-xs-center">{{ $masterList->links() }}</div>
	</div>
</div>
<script>
    $(function(){
        doAjaxFolderList()
    })
</script>
@endsection
