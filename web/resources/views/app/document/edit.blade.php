@extends('layouts.sarchive_layout')
@section('title',"글 수정 : $article->title")
@section('content')
<div class="container-fluid mt-4 mb-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST" action="{{ route($ROUTE_ID.'.update',['doc'=>$article->id]) }}">
        @csrf
        <input type="hidden" name="_method" value="PUT">

        <h5 class="">글 수정</h5>
        <div class="card">
            <div class="card-body px-1 px-md-3">
                @include($VIEW_PATH.'._form')
                <div class="d-flex w-100 justify-content-between">
                    <div>
                        <button hotkey="s" type="submit" class="btn btn-primary btn-sm" name="action" value="finished">저장</button>
                        <button type="submit" class="btn btn-outline-success btn-sm" name="action" value="continue">저장 후 계속 편집</button>
                        <a hotkey="z" class="text-secondary mx-2" style="font-size:14px"
                            href="{{ $actionLinks->cancel }}" role="button">취소</a>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete">삭제</button>
                </div>
            </div>
        </div>
        <!-- //Card -->
    </form>
</div>
{{-- Confirm Delete --}}
@include('modules.dialog.delete.default',['action'=>route($ROUTE_ID.'.destroy',['doc'=>$article->id]), 'token'=> csrf_token()])

@endsection
