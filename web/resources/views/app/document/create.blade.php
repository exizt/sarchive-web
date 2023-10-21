@extends('layouts.sarchive_layout')
@section('title',"글 작성")
@section('content')
<div class="container-fluid mt-4 mb-5">
    @include('modules.message.messages_and_errors.default')
    <form class="form-horizontal prevent" role="form" method="POST"
        action="{{ route($ROUTE_ID.'.store') }}">
        @csrf
        <input type="hidden" name="archive_id" value="{{ $archive->id }}">

        <h5 class="">신규 글 작성</h5>
        <div class="card">
            <div class="card-body px-1 px-md-3">
                @include($VIEW_PATH.'._form')
                <div>
                    <button type="submit" class="btn btn-primary btn-sm site-shortcut-key-s">저장</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" name="action" value="continue">저장 후 계속 편집</button>
                    <a class="text-secondary mx-2 site-shortcut-key-z" style="font-size:14px"
                    href="javascript:history.back()" role="button">취소</a>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
