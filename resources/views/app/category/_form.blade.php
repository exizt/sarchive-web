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
	<h4>{{ $item->name }}</h4>

	<input name="id" type="hidden" class="form-control" value="{{ $item->id }}" placeholder="" aria-label="">

	<div class="form-group">
		<textarea name="text" class="form-control" rows="14" id="content" placeholder="내용">{{ htmlentities($item->text) }}</textarea>
	</div>

	<div class="form-group">
		<label for="article-parent">상위 분류 (ex: [분류명A] [분류명B])</label>
		<input name="parent" type="text" id="article-parent" class="form-control" value="{{ $item->parent }}" placeholder="" aria-label="">
	</div>
</div>