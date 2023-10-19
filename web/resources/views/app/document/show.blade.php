@extends('layouts.sarchive_layout')
@section('title',"$article->title")
@section('content')
<div class="container-fluid px-1 px-md-3 mt-4 mb-5">
    @include('layouts.modules.messages.messages_and_errors_bs4')
    <h5>위치</h5>
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
    <div class="d-flex justify-content-between">
        <h4>본문</h4>
        <div>
            <a class="btn btn-sm btn-outline-info site-shortcut-key-e"
                href="{{ $actionLinks->edit }}" role="button">편집</a>
            <a class="btn btn-sm shh-btn-bookmark active {{($bookmark->is_favorite)? 'btn-info':'btn-primary'}}" href="#" role="button"
                data-mode="favorite" data-archive="{{$article->id}}" data-value="{{$bookmark->is_favorite}}"><i class="fas fa-star"></i>&nbsp;즐겨찾기</a>
            <a class="btn btn-sm shh-btn-bookmark active {{($bookmark->is_bookmark)? 'btn-info':'btn-primary'}}" href="#" role="button"
                data-mode="bookmark" data-archive="{{$article->id}}" data-value="{{$bookmark->is_bookmark}}"><i class="fas fa-bookmark"></i>&nbsp;북마크</a>
        </div>
    </div>
    <div class="card sa-article">
        <div class="card-body px-2 px-sm-3">
            <h5 class="card-title">{{ $article->title }}</h5>
            <p class="text-right">
                <small class="text-muted">최근 변경 {{ $article->updated_at->format('Y-m-d g:ia') }} (생성 {{ $article->created_at->format('Y-m-d g:ia') }})</small>
            </p>
            <hr>
            <div class="sa-article-output">
                {!! $article->content !!}
            </div>
        </div>
    </div>
    <h4 class="my-3">문서 정보</h4>
    <div class="card">
        <div class="card-body">
            <div>작성 : {{ $article->created_at->format('Y-m-d g:ia') }}</div>
            <div>수정 : {{ $article->updated_at->format('Y-m-d g:ia') }}</div>
            <div>
                분류 : <ul class="sarc-cat-list">
                    @foreach ($article->category_array as $i => $item)
                        <li><a href="/archives/{{$archive->id}}/category/{{urlencode($item)}}">{{$item}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group row">
        <div class="col-md-10 col-md-offset-2">
            <a class="btn btn-primary btn-sm site-shortcut-key-z"
                href="{{ $actionLinks->list }}" role="button">목록</a>
            <a class="btn btn-outline-info btn-sm site-shortcut-key-e"
                href="{{ $actionLinks->edit }}" role="button">편집</a>
            <a class="btn btn-outline-secondary btn-sm"
                href="javascript:history.back()" role="button">뒤로</a>
        </div>
    </div>

</div>
<script>
$(function(){
    $(".shh-btn-bookmark").on("click",doAjax_Bookmarking_event)
})

function doAjax_Bookmarking_event(e){
    e.preventDefault()
    var id = $(this).data("archive")
    var mode = $(this).data("mode")
    doAjax_Bookmarking(mode,id)
}

function doAjax_Bookmarking(mode,id){
    var conf = {
        true_class : "btn-info",
        false_class : "btn-primary"
    }
    $.post({
        url: '/archives/ajax_mark',
        dataType: 'json',
        data: {
            mode: mode,
            archive: id
        }
    }).done(function(json){
        $(".shh-btn-bookmark").each(function(index){
            if($(this).data("mode") == "favorite"){
                $(this).attr("data-value",json.data.is_favorite)
                if(json.data.is_favorite=='1'){
                    $(this).addClass(conf.true_class)
                    $(this).removeClass(conf.false_class)
                } else {
                    $(this).addClass(conf.false_class)
                    $(this).removeClass(conf.true_class)
                }
            } else if($(this).data("mode") == "bookmark"){
                $(this).attr("data-value",json.data.is_bookmark)
                if(json.data.is_bookmark=='1'){
                    $(this).addClass(conf.true_class)
                    $(this).removeClass(conf.false_class)
                } else {
                    $(this).addClass(conf.false_class)
                    $(this).removeClass(conf.true_class)
                }
            }
            //console.log("dd"+index+$(this).data("mode"));
        })
    })
}
</script>
{{-- prism : 코드 syntaxhighlighter 종류 중 하나 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css" integrity="sha512-mIs9kKbaw6JZFfSuo+MovjU+Ntggfoj8RwAmJbVXQ5mkAX5LlgETQEweFPI18humSPHymTb5iikEOKWF7I8ncQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js" integrity="sha512-9khQRAUBYEJDCDVP2yw3LRUQvjJ0Pjx0EShmaQjcHa6AXiOv6qHQu9lCAIR8O+/D8FtaCoJ2c0Tf9Xo7hYH01Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" integrity="sha512-SkmBfuA2hqjzEVpmnMt/LINrjop3GKWqsuLSSB3e7iBmYK7JuWw4ldmmxwD9mdm2IRTTi0OxSAfEGvgEi0i2Kw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
