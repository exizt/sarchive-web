@extends('layouts.sarchive_layout')
@section('title',"")
@section('content')
{{-- 아카이브 문서 목록 화면 (/archives/x/latest) --}}
<div>
    <div class="mt-4 mb-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="row px-0 mx-0">
                        <div class="d-flex w-100 justify-content-between">
                            <div>
                                @if(isset($parameters['folder']))
                                <h4 class=""><span class="sa-icon sa-icon-folder"></span>{{ $parameters['folder']->name }}<a href="{{ route('folders.edit', [$parameters['folder']->id]) }}"
                                        class="btn btn-sm">편집</a>
                                </h4>
                                @else
                                <h4 class=""><span class="sa-icon sa-icon-archive"></span>{{$layoutParams['archiveName'] ?? '아카이브'}}</h4>
                                @endif
                            </div>
                            <div class="text-right">
                                <a href="/folders/create?archive={{ $layoutParams['archiveId'] }}" class="btn btn-sm btn-outline-primary">새 폴더</a>
                                <a href="/doc/create?archive={{ $layoutParams['archiveId'] }}" class="btn btn-sm btn-outline-primary">새 문서</a>
                            </div>
                        </div>
                        @if (isset($parameters['folder']))
                        <p class="lead">{{ $parameters['folder']->comment }}</p>
                        @endif
                    </div>
                    <hr class="mt-1">
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
                    <div class="list-group mb-3">
                        @foreach ($masterList as $item)
                        <a class="list-group-item list-group-item-action flex-column align-items-start"
                        @if(isset($parameters['folder']))
                            href="{{ "/doc/{$item->id}?lfolder={$parameters['folder']->id}" }}"
                        @else
                            href="{{ "/doc/{$item->id}" }}"
                        @endif
                            >
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="sarc-item-label">{{ $item->title }}</h5>
                                <small>{{ $item->created_at->format('Y-m-d') }}</small>
                            </div>
                            <p class="mb-1 pl-md-3 sarc-item-comments">
                                <small>{{ $item->summary_var }}</small>
                            </p>
                            @if(isset($item->folder))
                            <div class="text-right">
                                <small>폴더 : {{ $item->folder->name }}</small>
                            </div>
                            @endif
                        </a>
                        @endforeach
                    </div>
                    <hr>
                    <div class="text-xs-center">{{ $masterList->appends($paginationParams)->onEachSide(2)->links() }}</div>
                </div>
                <div class="col-md-3 mt-5 mt-md-0">
                    <h5>폴더</h5>

                    @if (isset($parameters['folder']))
                    <div class="list-group my-2">
                        @if ($folder->parent_id == 0)
                            <a href="/archives/{{ $archive->id }}/latest" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" >
                                상위 폴더로
                            </a>
                        @else
                            <a href="/folders/{{ $folder->parent_id }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" >
                                상위 폴더로
                            </a>
                        @endif
                    </div>
                    @endif


                    <div class="list-group my-3">
                        @if (isset($parameters['folder']))
                            <a href="/folders/{{ $folder->id }}?only=1" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" >
                                {{ $folder->name }} (only)
                                <span class="badge badge-secondary badge-pill">{{ $folder->doc_count }}</span>
                            </a>
                        @else
                            <a href="/archives/{{ $archive->id }}/latest?only=1" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" >
                                {{ $archive->name }} (only)
                            </a>
                        @endif
                    </div>

                    <div class="list-group sarc-layout-nav-folder-list" id="shh-nav-board-list"></div>

                    <div class="d-flex w-100 justify-content-between my-2">
                        <div>
                            <a href="#" id="btnIndexEditModeCancel"
                            class="btn btn-sm arch-indexEditMode-show" style="display:none">취소</a>
                        </div>
                        <div>
                            <a href="#" id="btnIndexEditModeToggle" class="btn btn-sm arch-indexEditMode-hide">변경</a>
                            <a href="#" id="btnIndexEditModeSave" class="btn btn-sm btn-outline-success arch-indexEditMode-show" style="display:none">순서변경 저장</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    $(function(){
        doAjaxFolderList(getArchiveId(), getFolderId())
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
                        <span class="badge badge-secondary arch-indexEditMode-up">▲</span>
                        <span class="badge badge-secondary arch-indexEditMode-down">▼</span>
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
