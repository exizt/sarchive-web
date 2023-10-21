@extends('layouts.sarchive_layout')
@section('title',"아카이브 목록 편집")
@section('content')
@push('scripts')
<script src="/assets/js/sub_archive.js"></script>
@endpush
{{-- 아카이브 목록 편집 (/archives/editableIndex) --}}
<div class="container py-5" id="reorderArchive">
    @include('modules.message.messages_and_errors.toast')
    <div class="row px-0 mx-0">
        <div class="d-flex w-100 justify-content-between">
            <h4 class="">아카이브 목록 편집</h4>
            <small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
        </div>
    </div>
    <hr class="mt-1">
    <div class="list-group" id="archiveListGroup">
        @foreach ($masterList as $item)
        <a class="list-group-item list-group-item-action flex-column align-items-start shh-profile-list" href="{{ route($ROUTE_ID.'.edit',$item->id) }}"
            data-id="{{$item->id}}" data-label="{{$item->name}}">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="">{{ $item->name }}@if ($item->is_default) &nbsp;&nbsp;&nbsp;<span class="badge badge-success pull-right">기본</span> @endif </h5>
                <div>
                    <span class="shh-ordermode-hide" data-visible="only_index">
                        <small>{{ $item->created_at->format('Y-m-d') }}</small>
                    </span>
                    <span class="shh-ordermode-show" data-visible="only_edit" style="display:none">
                        <button type="button" class="btn btn-primary btn-sm ar-btn-ordermode-up">▲</button>
                        <button type="button" class="btn btn-primary btn-sm ar-btn-ordermode-down">▼</button>
                    </span>
                </div>
            </div>
            <p class="mb-1 ps-md-3 sa-list-item-summary">
                <small>{{ $item->comments }}</small>
            </p>
        </a>
        @endforeach
    </div>
    <hr>
    <div class="d-flex w-100 justify-content-between">
        <div>
            <a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-outline-success btn-sm shh-ordermode-hide" data-visible="only_index">신규</a>
            <a href="{{ route($ROUTE_ID.'.index') }}" class="text-secondary mx-2" style="font-size:14px" data-visible="only_index">아카이브 선택으로 돌아가기</a>
            <a href="#" id="btnOrderSave" class="btn btn-outline-success btn-sm shh-ordermode-show" style="display:none" data-visible="only_edit">순서변경 저장</a>
        </div>
        <span>
            <a href="#" id="btnOrderEditModeToggle" class="btn btn-outline-success btn-sm shh-ordermode-hide" data-visible="only_index">순서변경</a>
            <a href="#" id="btnOrderEditModeCancel" class="btn btn-sm" style="display:none;" data-visible="only_edit">취소</a>
        </span>
    </div>
    <hr>
    <div class="text-xs-center">{{ $masterList->onEachSide(2)->links() }}</div>
</div>
@endsection
