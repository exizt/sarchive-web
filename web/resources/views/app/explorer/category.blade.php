@extends('layouts.sarchive_layout')
@section('title','분류 > '.$category->name)
@section('content')
{{-- 카테고리 페이지 (/archives/x/category/x) --}}
<div>
    <div class="mt-4 mb-5">
        <div class="container">
            <div class="row px-0 mx-0">
                <div class="d-flex w-100 justify-content-between">
                    <h4 class="">분류 : {{ $category->name }}
                        &nbsp;&nbsp;&nbsp;
                    <a class="btn btn-outline-info btn-sm"
                        href="{{ route('category.edit',['archive'=>$archive->id,'category'=>$category->id]) }}"
                        role="button">편집</a>
                </h4>
                <small class="text-mute">Page {{ $masterList->currentPage() }}/{{ $masterList->lastPage() }}</small>
                </div>
                <p class="lead font-italic small">{{ $category->comments }}</p>
            </div>
            @if(count($childCategories))
            <h5>하위 분류</h5>
            <div class="row mb-5 mx-0">
                @foreach ($childCategories as $categoryName)
                <div class="list-group col mx-2">
                    <a class="list-group-item list-group-item-action"
                        href="{{route('explorer.category',['archive'=>$archive->id,'category'=>$categoryName])}}">
                        {{$categoryName}}
                    </a>
                </div>
                @endforeach
            </div>

            @endif
            <h5>여기에 속하는 문서</h5>
            <div class="list-group">
                @foreach ($masterList as $item)
                <a class="list-group-item list-group-item-action flex-column align-items-start"
                    href="/doc/{{ $item->id }}?lcategory={{ $category->id }}">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $item->title }}</h5>
                        <small>{{ $item->created_at->format('Y-m-d') }}</small>
                    </div>
                    <p class="mb-1 pl-md-3 sa-list-item-summary">
                        <small>{{ $item->summary_var }}</small>
                    </p>
                </a> @endforeach
            </div>

            @if(count($category->category_array))
            <hr>
            <div class="card">
                <div class="card-body">
                    상위 분류&nbsp;:&nbsp;
                    <ul class="sa-category-breadcrumbs">
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
        <hr>
        <div class="text-xs-center">{{ $masterList->onEachSide(2)->links() }}</div>
    </div>
</div>
@endsection
