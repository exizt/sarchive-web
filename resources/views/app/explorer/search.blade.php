@extends('layouts.sarchive_layout') 
@section('title',"검색 결과") 
@section('content')
<div class="container">
    <h6 class="mt-5 text-muted">검색 결과</h6>
    <div class="list-group list-group-flush">
        @foreach ($masterList as $item)
        <a class="list-group-item list-group-item-action flex-column align-items-start" 
            href="{{ "/doc/{$item->id}" }}">
            <div class="d-flex w-100 justify-content-between">
                <h4 class="mb-1">{{ $item->title }}</h4>
            </div>
            <p class="mb-1 cz-item-summary">
                <small class="text-muted">{{ $item->summary_var }}</small>
            </p>
            <div class="d-flex w-100 justify-content-between">
                <small>@if(isset($item->folder)) 폴더 : {{ $item->folder->name }} @endif</small>
                <small>{{ $item->created_at->format('Y-m-d') }}</small>
            </div>
        </a>
        @endforeach
    </div>
    <hr>
    <div class="text-xs-center">{{ $masterList->appends($paginationParams)->links() }}</div>
</div>
@endsection
