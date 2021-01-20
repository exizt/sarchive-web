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
                            @if(isset($archiveBoard))
                            <h4 class="">게시글 목록 (선택된 게시판 : {{ $archiveBoard->name }})</h4>
                            @else
                            <h4 class="">아카이브</h4>
                            @endif
                            <small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
                        </div>
                        @if(isset($archiveBoard))
                        <p class="lead">{{ $archiveBoard->comment }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <a href="" class="btn btn-outline-primary">새 폴더</a>
                        <a href="" class="btn btn-outline-primary">새 문서</a>
                    </div>
                    <hr class="mt-1">
                    <div class="list-group">
                        @foreach ($masterList as $item)
                        <a class="list-group-item list-group-item-action flex-column align-items-start" 
                            href="{{ "/doc/{$item->id}" }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $item->title }}</h5>
                                <small>{{ $item->created_at->format('Y-m-d') }}</small>
                            </div>
                            <p class="mb-1 pl-md-3 cz-item-summary">
                                <small>{{ $item->summary_var }}</small>
                            </p>
                            <div class="text-right">
                                <small>@if(isset($item->folder)) 폴더 : {{ $item->folder->name }} @endif</small>
                                <!--<small>분류 : {{ $item->category }}</small>-->
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3">
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
        <div class="text-xs-center">{{ $masterList->appends($paginationParams)->links() }}</div>
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