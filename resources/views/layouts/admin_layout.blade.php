@extends('layouts.root_layouts.sarchive_root_layout') 
@section('layout_header')
<div class="container-fluid mt-4 mb-5">
    <div class="row">
        <div class="col-md-2">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="/admin/archiveBoard">게시판 설정</a></li>
                <li class="list-group-item">페이지 설정</li>
                <li class="list-group-item">아카이브 프로필 설정</li>
                <li class="list-group-item">&nbsp;</li>
                <li class="list-group-item">&nbsp;</li>
            </ul>
        </div>
        <div class="col-md-10">
@endsection
@section('layout_footer')
        </div>
    </div>
</div>
@endsection