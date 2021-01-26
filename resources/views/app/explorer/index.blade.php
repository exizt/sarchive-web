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
                            @if(isset($parameters['folder']))
                            <div>
                                <h4 class="">글 목록 (선택된 폴더 : {{ $parameters['folder']->name }})
                                    <a href="{{ route('folders.edit', [$parameters['folder']->id]) }}" class="btn btn-sm btn-outline-primary">편집</a>
                                </h4>
                            </div>
                            @else
                            <h4 class="">아카이브</h4>
                            @endif
                            <small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
                        </div>
                        @if(isset($parameters['folder']))
                        <p class="lead">{{ $parameters['folder']->comment }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <a href="/folders/create?archive={{ $layoutParams['archiveId'] }}" class="btn btn-outline-primary">새 폴더</a>
                        <a href="/doc/create?archive={{ $layoutParams['archiveId'] }}" class="btn btn-outline-primary">새 문서</a>
                    </div>
                    <hr class="mt-1">
                    <div class="list-group">
                        @foreach ($masterList as $item)
                        <a class="list-group-item list-group-item-action flex-column align-items-start" 
                        @if(isset($parameters['folder']))
                            href="{{ "/doc/{$item->id}?lfolder={$parameters['folder']->id}" }}"
                        @else 
                            href="{{ "/doc/{$item->id}" }}"
                        @endif
                            >
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $item->title }}</h5>
                                <small>{{ $item->created_at->format('Y-m-d') }}</small>
                            </div>
                            <p class="mb-1 pl-md-3 sarc-item-comments">
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
                    <h5>폴더</h5>
                    <div class="list-group sarc-layout-nav-folder-list" id="shh-nav-board-list"></div>
                    <div class="list-group pt-3" id="js-folderNav-folderOnly" style="display:none"></div>
                    <div class="d-flex w-100 justify-content-between">
                        <span>
                            <a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-outline-success btn-sm arch-indexEditMode-show" style="display:none">신규</a>
                        </span>
                        <span>
                            <a href="#" id="btnIndexEditModeToggle" class="btn btn-outline-success btn-sm arch-indexEditMode-hide">변경</a>
                            <a href="#" id="btnIndexEditModeCancel" class="btn btn-outline-success btn-sm arch-indexEditMode-show" style="display:none">순서변경 취소</a>
                            <a href="#" id="btnIndexEditModeSave" class="btn btn-outline-success btn-sm arch-indexEditMode-show" style="display:none">순서변경 저장</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-xs-center">{{ $masterList->appends($paginationParams)->links() }}</div>
    </div>
</div>
<script>
    $(function(){
        doAjaxFolderList()
        bindIndexMode()
    })
    function bindIndexMode(){
        var listItemClassName = "arch-indexEditMode-listitem";
        var listItemSelector = "."+listItemClassName;
        
        $("#btnIndexEditModeToggle").on("click",changeIndexEditModeOn)
        $("#btnIndexEditModeCancel").on("click",function(){location.reload();})
        $("#btnIndexEditModeSave").on("click",saveArchiveSort)
        $(document).on("click",".arch-indexEditMode-up",onClickMoveUp);
        $(document).on("click",".arch-indexEditMode-down",onClickMoveDown);

        /**
         * 인덱스 변경 모드로 전환
         */
        function changeIndexEditModeOn(){
            // folderNav에 맞춘 작업
            $("#shh-nav-board-list").find("span").remove();
            $(".sarc-depth-1").addClass(listItemClassName);
            $(".sarc-depth-1").append(`<span>
						<button type="button" class="btn btn-primary btn-sm arch-indexEditMode-up">▲</button>
						<button type="button" class="btn btn-primary btn-sm arch-indexEditMode-down">▼</button>
                    </span>`);
            $(".sarc-depth-2").remove();
            $(".sarc-depth-3").remove();
            $(".sarc-depth-4").remove();
            $(".sarc-depth-5").remove();
            $(".sarc-depth-5").remove();

            // 일반적인 보여지는 부분 처리
            $(".arch-indexEditMode-hide").hide()
            $(".arch-indexEditMode-show").show()
            $(listItemSelector).attr("href","#");
        }

        /**
         * 아카이브 순서 변경사항을 저장
         */
        function saveArchiveSort(){
            // 작업 해야함.
            var dataList = [];
            $(".sarc-depth-1").each(function(index){
                var data = {
                    id : $(this).data("id"),
                    name : $(this).data("label"),
                    index : index+1
                };
                dataList.push(data)
            })
            console.log(dataList)
    
            ajaxSave(dataList)
        }

        function ajaxSave(dataList){
            $.post({
                url: '/folders/updateSort',
                data: {
                    'dataList': dataList
                }
            })
            .done(function(data){
                location.reload()
            })
        }

        function onClickMoveUp(){
            moveUp($(this).closest(".sarc-depth-1"),".sarc-depth-1")
        }

        function onClickMoveDown(){
            moveDown($(this).closest(".sarc-depth-1"),".sarc-depth-1")
        }

        function moveUp($current,sel){
            var hook = $current.prev(sel)
            if(hook.length){
                var elementToMove = $current.detach();
                hook.before(elementToMove);
            }
        }

        function moveDown($current,sel){
            var hook = $current.next(sel)
            if(hook.length){
                var elementToMove = $current.detach();
                hook.after(elementToMove);
            }
        }
    }
</script>
@endsection