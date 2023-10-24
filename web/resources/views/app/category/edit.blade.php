@extends('layouts.sarchive_layout')
@section('title',"")
@section('content')
<div class="container-fluid mt-4 mb-5">
    <form class="form-horizontal prevent" role="form" method="POST"
        action="{{ route($ROUTE_ID.'.update',['archive'=>$archive->id,'category'=>$item->id]) }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="PUT">

        <div class="card mt-3">
            <h5 class="card-header">분류 : ({{ $item->name }}) 편집</h5>
            <div class="card-body px-1 px-md-3">
                @include($VIEW_PATH.'._form')
                <div class="d-flex w-100 justify-content-between">
                    <div>
                        <button hotkey="s" type="submit" class="btn btn-primary btn-sm" name="action" value="finished">저장</button>
                        <a hotkey="z" class="btn btn-outline-secondary btn-sm"
                            href="{{ route('explorer.category',['archive'=>$archive->id,'category'=>urlencode($item->name)]) }}" role="button">뒤로</a>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-delete">삭제</button>
                </div>
            </div>
        </div>
        <!-- //Card -->
    </form>
</div>
{{-- Confirm Delete --}}
@include('modules.dialog.delete.default',['action'=>route($ROUTE_ID.'.destroy',['archive'=>$archive->id,'category'=>$item->id]), 'token'=> csrf_token()])

@endsection
