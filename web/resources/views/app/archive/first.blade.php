@extends('layouts.sarchive_layout', ['layoutMode' => 'first'])
@section('content')
<div class="container">
<div class="search-wrap">
    @isset($archive->id)
    <form action="/archives/{{ $archive->id }}/search">
        <input hotkey="f" class="form-control form-control-lg me-sm-2"
        type="search" placeholder="{{$archive->name}}에서 검색"
        aria-label="Search" name="q" value="" autofocus>
    </form>
    <div class="text-center m-2">
        <a hotkey="a" class="btn btn-sm btn-outline-secondary"
        href="{{ route('doc.create',['archive'=>$archive->id]) }}">문서 추가</a>
        <a hotkey="w" class="btn btn-sm btn-outline-secondary"
        href="{{ route('explorer.archive',['archive'=>$archive->id]) }}">탐색</a>
    </div>
    </div>
    @endisset
</div>
<div>
</div>
<style>
.search-wrap{
    margin: 0 auto;
    max-width: 700px;
    margin-top: 200px;
    margin-bottom: 200px;
}
#sh-search{
    border: 0;
    border-bottom : 1px solid black;
}
</style>
@endsection
