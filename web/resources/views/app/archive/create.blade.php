@extends('layouts.sarchive_layout')
@section('title',"아카이브 신규")
@section('content')
<div class="container py-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST" action="{{ route($ROUTE_ID.'.store') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <h5>아카이브 신규</h5>
        <div class="card">
            <div class="card-body">
                @include($VIEW_PATH.'._form')
                <div>
                    <button type="submit" hotkey="s" class="btn btn-primary btn-sm">아카이브 생성</button>
                    <a hotkey="z" class="text-secondary mx-2" style="font-size:14px"
                    href="javascript:history.back()" role="button">취소</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
