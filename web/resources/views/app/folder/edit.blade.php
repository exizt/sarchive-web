@extends('layouts.sarchive_layout')
@section('title',"폴더 편집")
@section('content')
<div class="container py-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST"
        action="{{ route($ROUTE_ID.'.update',$item->id) }}">
        @csrf
        <input type="hidden" name="_method" value="PUT">

        <h5 class="">폴더 변경</h5>
        <div class="card">
            <div class="card-body px-1 px-md-3">
                @include($VIEW_PATH.'._form')
                <div class="d-flex w-100 justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s" name="action" value="finished">저장</button>
                        <a class="text-secondary mx-2 site-shortcut-key-z" style="font-size:14px"
                            href="/folders/{{ $item->id }}" role="button">취소</a>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete">삭제</button>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- Confirm Delete --}}
@include('modules.dialog.delete.default',['action'=>route($ROUTE_ID.'.destroy',$item->id), 'token'=> csrf_token()])

@endsection
