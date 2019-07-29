<script src="/assets/lib/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({ 
	selector:'textarea',
	height:500,
	menubar: false,
	plugins: "code lists advlist codesample",
	toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent code codesample',
	forced_root_block : false,
    force_br_newlines : true,
    codesample_languages: [
        {text: 'HTML/XML', value: 'markup'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'CSS', value: 'css'},
        {text: 'PHP', value: 'php'},
        {text: 'Java', value: 'java'},
        {text: 'C', value: 'c'},
        {text: 'C++', value: 'cpp'},
        {text: 'C#', value: 'csharp'},
        {text: 'Ruby', value: 'ruby'},
        {text: 'Python', value: 'python'},
        {text: 'Bash', value: 'bash'},
        {text: 'PowerShell', value: 'powershell'},
        {text: 'SQL', value: 'sql'},
        {text: 'Wiki Markup', value: 'wiki'},
        {text: 'JSON', value: 'json'},
        {text: 'INI', value: 'ini'},
	],
	init_instance_callback: function (editor) {
		editor.on('keyup', function (e) {
			if(typeof shortcutKeyEvent === "function"){
				// 사이트에서 이용되는 단축키 기능
				shortcutKeyEvent(e);
			}
		});
	}
});
</script>
<div class="">
	<div class="form-group">
		<input name="title" class="form-control" type="text" autofocus id="title" value="{{ $article->title }}" placeholder="제목" aria-label="제목">
	</div>

	<div class="form-group">
		<textarea name="content" class="form-control" rows="14" id="content" placeholder="내용">{{ htmlentities($article->content) }}</textarea>
	</div>

	<div class="form-group">
		<label for="boardSelectInput">게시판 선택</label>
    	<select name="board_id" class="form-control" title="게시판 선택" id="boardSelectInput">
    		@foreach ($boardList as $cate)
				@isset($selectedBoard)
				<option value="{{ $cate->id }}" @if ($cate->id == $selectedBoard) selected @endif>{{ str_repeat('🢒',$cate->depth).' '.$cate->name }}</option>
				@else
				<option value="{{ $cate->id }}">{{ str_repeat('🢒',$cate->depth).' '.$cate->name }}</option>
				@endisset
    		@endforeach
    	</select>
	</div>

	<div class="form-group">
		<label for="article-category">분류 (ex: [분류명A] [분류명B])</label>
		<input name="category" type="text" id="article-category" class="form-control" value="{{ $article->category }}" placeholder="" aria-label="">
	</div>

		
	<div class="form-group">
		<label for="articleReference">원문 출처</label>
		&nbsp;<span class="badge badge-secondary shh-evt-append-ref" data-value="나">내가 작성함</span>
		&nbsp;<span class="badge badge-secondary shh-evt-append-ref" data-value="펌">펌글</span>
		&nbsp;<span class="badge badge-secondary shh-evt-append-ref" data-value="">비우기</span>
		<input name="reference" type="text" id="articleReference" class="form-control" value="{{ $article->reference }}" placeholder="" aria-label="">
	</div>	
</div>
<script>
$(function(){
	$(".shh-evt-append-ref").on("click",function(){
		if(typeof $(this).data("value") !== "undefined"){
			$("#articleReference").val($(this).data("value"))
		}
	});
});

</script>