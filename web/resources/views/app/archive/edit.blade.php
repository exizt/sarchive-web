@extends('layouts.sarchive_layout')
@section('title',"아카이브 편집")
@section('content')
<div class="container py-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST" action="{{ route($ROUTE_ID.'.update',$item->id) }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PUT">

        <h5>아카이브 정보 편집</h5>
        <div class="card">
            <div class="card-body">
                @include($VIEW_PATH.'._form')
                <div class="d-flex w-100 justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s" name="action" value="finished">저장</button>
                        <a class="text-secondary mx-2 site-shortcut-key-z" style="font-size:14px"
                            href="{{ route($ROUTE_ID.'.editableIndex') }}" role="button">취소</a>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete">삭제</button>
                </div>
            </div>
        </div>
        <!-- //Card -->
    </form>
</div>
<!-- //.container-fluid -->
{{-- Confirm Delete --}}
<div class="modal fade" id="modal-delete" tabIndex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST" action="{{ route($ROUTE_ID.'.destroy',$item->id) }}">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="lead">정말 삭제하시겠습니까?</p>
                <p class="lead small"><small>해당되는 게시물을 이동하게 될 아카이브 선택하기.</small></p>
                <select name="will_move" class="form-select" title="아카이브 프로필 선택">
                @foreach ($archiveList as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
                </select>
                <div class="mt-3 text-end">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"> <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">닫기</button>
                    <button type="submit" class="btn btn-danger">예</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
