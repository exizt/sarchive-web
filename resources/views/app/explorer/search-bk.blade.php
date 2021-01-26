@extends('layouts.sarchive_layout') 
@section('title',"검색 결과") 
@section('content')
<div class="container">
    <div class="row px-0 mx-0 mt-5">
        <div class="d-flex w-100 justify-content-between">
            <h4 class="">검색 결과</h4>
            <small class="text-mute">Page {{ $masterList->currentPage() }} of {{ $masterList->lastPage() }}</small>
        </div>
    </div>
    <div class="list-group">
        @foreach ($masterList as $item)
        <a class="list-group-item list-group-item-action flex-column align-items-start" 
            href="{{ "/doc/{$item->id}" }}">
            <div class="d-flex w-100 justify-content-between">
                <h4 class="mb-1">{{ $item->title }}</h4>
                <small>{{ $item->created_at->format('Y-m-d') }}</small>
            </div>
            <p class="mb-1 pl-md-3 sarc-item-comments">
                <small class="text-muted">{{ $item->summary_var }}</small>
            </p>
            <div class="text-right">
                <small>@if(isset($item->folder)) 폴더 : {{ $item->folder->name }} @endif</small>
            </div>
        </a>
        @endforeach
    </div>
    <hr>
    <div class="text-xs-center">{{ $masterList->appends($paginationParams)->links() }}</div>
</div>
@endsection
