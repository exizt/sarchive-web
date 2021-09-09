@extends('layouts.sarchive_layout', ['layoutMode' => 'first'])
@section('content')
<div class="container">
  <div class="search-wrap">
      @isset($archive->id)
      <form action="/archives/{{ $archive->id }}/search">
        <input class="form-control form-control-lg mr-sm-2 site-shortcut-key-f"
          type="search" placeholder="{{$archive->name}} 검색"
          aria-label="Search" name="q" value="" autofocus>
      </form>
      <div class="text-center m-2">
        <a class="btn btn-sm btn-outline-secondary site-shortcut-key-n site-shortcut-key-a"
          href="{{ route('doc.create',['archive'=>$archive->id]) }}">문서 추가</a>
        <a class="btn btn-sm btn-outline-secondary"
          href="{{ route('explorer.archive',['id'=>$archive->id]) }}">탐색</a>
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
