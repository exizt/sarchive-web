@extends('layouts.sarchive_layout', ['layoutMode' => 'admin', 'currentMenu'=>'folder-control'])
@section('title',"아카이브 카테고리 관리")
@section('content')
<div>
	<div class="my-3">
		<h3>폴더 목록</h3>
	</div>
    {{-- ajax 결과로 펼쳐지는 messages 영역 --}}
	<div id="messages">
		<div class="alert alert-success alert-dismissible fade show shh-alert-msg-tpl" role="alert" style="display:none">
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	</div>
    {{-- tabs 영역 --}}
	<ul class="nav nav-tabs" id="navTab">
		@foreach ($archiveList as $item)
		<li class="nav-item"><a class="nav-link" href="#" data-profile="{{$item->id}}">{{$item->name}}</a></li>
		@endforeach
	</ul>
    {{-- jsTree 영역 --}}
	<div id="tree-container" class="mt-1 mb-3"></div>
    {{-- Buttons --}}
    <nav>
        <button id="btnFolderDataSave" class="btn btn-sm btn-primary">저장</button>
        <button id="shh-btn-create" class="btn btn-sm btn-outline-success">게시판 추가</button>
        <button id="shh-btn-rename" class="btn btn-sm btn-outline-success">이름 변경</button>
        <button id="shh-btn-delete" class="btn btn-sm btn-outline-success">삭제</button>
        <button id="shh-btn-save-test" class="btn btn-sm btn-outline-success">저장 (테스트)</button>
    </nav>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css" integrity="sha512-A5OJVuNqxRragmJeYTW19bnw9M2WyxoshScX/rGTgZYj5hRXuqwZ+1AVn2d6wYTZPzPXxDeAGlae0XwTQdXjQA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js" integrity="sha512-ekwRoEshEqHU64D4luhOv/WNmhml94P8X5LnZd9FNOiOfSKgkY12cDFz3ZC6Ws+7wjMPQ4bPf94d+zZ3cOjlig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/assets/js/page-js/admin.folder_mgmt.js"></script>
@endsection
