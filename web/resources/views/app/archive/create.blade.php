@extends('layouts.sarchive_layout')
@section('title',"아카이브 신규")
@section('content')
<div class="container py-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="card mt-3">
            <h5 class="card-header">아카이브 신규</h5>
            <div class="card-body px-1 px-md-3">
                @include($VIEW_PATH.'._form')
                <div class="d-flex w-100 justify-content-between">
                    <button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s">신규 생성</button>
                    <a class="btn btn-secondary btn-sm site-shortcut-key-z" href="{{ route($ROUTE_ID.'.editableIndex') }}" role="button">취소</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
