@extends('layouts.sarchive_layout')
@section('title',"")
@section('content')
{{-- 아카이브 문서 목록 화면 (/archives/x/latest) --}}
<div class="container-fluid mt-4 mb-5">
    @include('modules.message.messages_and_errors.default')
    <div class="row">
        <div class="col-md-9">
            <header>
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
                <div class="d-flex w-100 justify-content-between">
                    <header>
                        @if(isset($parameters['folder']))
                        <h4>
                            <span class="sa-icon sa-icon-folder"></span>{{ $parameters['folder']->name }}
                            <a href="{{ route('folders.edit', [$parameters['folder']->id]) }}" class="btn btn-sm">편집</a>
                        </h4>
                        @else
                        <h4>
                            <span class="sa-icon sa-icon-archive"></span>{{$layoutParams['archiveName'] ?? '아카이브'}}
                        </h4>
                        @endif
                    </header>
                    <nav>
                        <a href="{{ $actionLinks->new_folder }}" class="btn btn-sm btn-outline-primary">새 폴더</a>
                        <a href="{{ $actionLinks->new_doc }}" class="btn btn-sm btn-outline-primary">새 문서</a>
                    </nav>
                </div>
                @if ( isset($parameters['folder']) && !empty($parameters['folder']->comment) )
                <p class="lead">{{ $parameters['folder']->comment }}</p>
                @endif
                <hr class="mt-0">
            </header>
            <div class="list-group mb-3">
                @foreach ($masterList as $item)
                <a class="list-group-item list-group-item-action flex-column align-items-start"
                @if(isset($parameters['folder']))
                    href="{{ route('doc.show', array_merge(['doc'=>$item->id], $trackedLinkParams), false) }}"
                @else
                    href="{{ "/doc/{$item->id}" }}"
                @endif
                @if (isset($trackedLinkParams))
                    data-debug="{{ route('doc.show', array_merge(['doc'=>$item->id], $trackedLinkParams), false) }}"
                @endif
                    >
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="sa-list-item-title">{{ $item->title }}</h5>
                        <small>{{ $item->created_at->format('Y-m-d') }}</small>
                    </div>
                    <p class="mb-1 ps-md-3 sa-list-item-summary">
                        <small>{{ $item->summary_var }}</small>
                    </p>
                    @if(isset($item->folder))
                    <div class="text-end">
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

            <nav id="navFolder">
                <div class="list-group" id="navFolderList"></div>
                <div class="d-flex w-100 justify-content-between my-2">
                    <div>
                        <a href="#" id="btnFolderNavEditModeCancel"
                        class="btn btn-sm" style="display:none" data-visible="only_edit">취소</a>
                    </div>
                    <div>
                        <a href="#" id="btnFolderNavEditModeToggle" class="btn btn-sm" data-visible="only_index">변경</a>
                        <a href="#" id="btnFolderNavEditModeSave" class="btn btn-sm btn-outline-success" style="display:none" data-visible="only_edit">순서변경 저장</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
@endsection
