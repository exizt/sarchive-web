@extends('layouts.sarchive_layout')
@section('title',"단축키")
@section('content')
<div class="container-fluid mt-4 mb-5">
    <div class="card">
        <div class="card-body"><h3>단축키</h3>
            <br> <kbd>Alt + Shift</kbd> 다음에 아래의 키를 누른다.<br><br><br>
            <h4>예약된 키</h4>
            <ol>
            <li>글쓰기 : <kbd>A</kbd></li>
            <li>글수정 : <kbd>E</kbd></li>
            <li>검색 포커스 : <kbd>F</kbd></li>
            <li>저장 : <kbd>S</kbd></li>
            <li>뒤로가기(또는 취소/목록) : <kbd>Z</kbd></li>
            </ol>
        </div>
    </div>
</div>
@endsection
