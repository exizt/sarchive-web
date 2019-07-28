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
	@if ($errors->any())
	<div class="alert alert-danger alert-dismissible fade show"role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
	@if (session()->has('message'))
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		{{ session()->get('message') }}
	</div>
	@endif

	<div class="form-group">
		<input name="title" class="form-control" type="text" autofocus id="title" value="{{ $item->title }}" placeholder="제목" aria-label="제목">
	</div>

	<div class="form-group">
		<textarea name="content" class="form-control" rows="14" id="content" placeholder="내용">{{ htmlentities($item->content) }}</textarea>
	</div>
</div>