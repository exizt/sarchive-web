@extends('layouts.sarchive_layout')
@section('title',"폴더 신규")
@section('content')
<div class="container py-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST"
        action="{{ route($ROUTE_ID.'.store') }}">
        @csrf
        <input type="hidden" name="archive_id" value="{{ $archive->id }}">

        <h5 class="">폴더 추가</h5>
        <div class="card">
            <div class="card-body px-1 px-md-3">
                @include($VIEW_PATH.'._form')
                <nav>
                    <button hotkey="s" type="submit" class="btn btn-primary btn-sm">저장</button>
                    <a hotkey="z" class="text-secondary mx-2" style="font-size:14px"
                    href="javascript:history.back()" role="button">취소</a>
                </nav>
            </div>
        </div>
    </form>
</div>
@endsection
