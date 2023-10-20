@extends('layouts.sarchive_layout')
@section('title',"$article->title")
@push('scripts')
<script src="/assets/js/document.js"></script>
@endpush
@section('content')
<div class="container-fluid px-1 px-md-3 mt-4 mb-5">
    @include('modules.message.messages_and_errors.default')
    <nav aria-label="breadcrumb" id="locationNav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/archives/{{$archive->id}}/latest">{{ $archive->name }}</a></li>
            @isset ($folder->paths)
            @foreach ($folder->paths as $item)
            <li class="breadcrumb-item"><a href="/folders/{{$item->id}}">{{ $item->name }}</a></li>
            @endforeach
            @endisset
        </ol>
    </nav>
    <div class="sa-article">
        <h3 class="">{{ $article->title }}</h3>
        <div class="d-flex justify-content-between">
            <small class="text-muted font-italic font-weight-light">
                {{ $article->created_at->format('Y-m-d') }}
                (updated {{ $article->updated_at->format('Y-m-d') }})
            </small>
            <div class="text-right">
                <a class="btn btn-sm btn-outline-info site-shortcut-key-e"
                    href="{{ $actionLinks->edit }}" role="button">편집</a>
                <a id="saArticleLikeBtn" class="btn btn-sm btn-outline-secondary" href="#" role="button"
                    data-document="{{$article->id}}">Like</a>
            </div>
        </div>
        <hr>
        <div class="sa-article-output">
            {!! $article->content !!}
        </div>
    </div>
    <hr>
    <h5 class="mt-5">문서 정보</h5>
    <div class="card">
        <div class="card-body">
            <div>생성 일시 : {{ $article->created_at->format('Y-m-d g:ia') }}</div>
            <div>최근 변경 : {{ $article->updated_at->format('Y-m-d g:ia') }}</div>
            <div>
                분류 : <ul class="sa-category-breadcrumbs">
                    @foreach ($article->category_array as $i => $item)
                        <li><a href="/archives/{{$archive->id}}/category/{{urlencode($item)}}">{{$item}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
            @if (session()->has('status_after_editing'))
            <a class="btn btn-secondary btn-sm site-shortcut-key-z"
                href="{{ $actionLinks->list }}" role="button">목록</a>
            @else
                <a class="btn btn-secondary btn-sm site-shortcut-key-z"
                    href="javascript:history.back()" role="button">뒤로</a>
            @endif
        </div>
        <div>
            <a class="btn btn-outline-info btn-sm site-shortcut-key-e"
                href="{{ $actionLinks->edit }}" role="button">편집</a>
        </div>
    </div>

</div>
{{-- prism : 코드 syntaxhighlighter 종류 중 하나 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css" integrity="sha512-mIs9kKbaw6JZFfSuo+MovjU+Ntggfoj8RwAmJbVXQ5mkAX5LlgETQEweFPI18humSPHymTb5iikEOKWF7I8ncQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js" integrity="sha512-9khQRAUBYEJDCDVP2yw3LRUQvjJ0Pjx0EShmaQjcHa6AXiOv6qHQu9lCAIR8O+/D8FtaCoJ2c0Tf9Xo7hYH01Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" integrity="sha512-SkmBfuA2hqjzEVpmnMt/LINrjop3GKWqsuLSSB3e7iBmYK7JuWw4ldmmxwD9mdm2IRTTi0OxSAfEGvgEi0i2Kw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
