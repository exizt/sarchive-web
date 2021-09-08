<script src="/assets/lib/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({ 
	selector:'textarea',
	height:500,
	menubar: false,
	plugins: "code lists advlist",
	toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent code codesample',
	forced_root_block : false,
    force_br_newlines : true,
	init_instance_callback: function (editor) {
		editor.on('keyup', function (e) {
			// 사이트에서 이용되는 단축키 기능
			if(typeof shortcutKeyEvent === "function"){
				shortcutKeyEvent(e);
			}
		});
	}
});
</script>
<div class="">
	<div class="form-group">
		<label for="item-content">분류 설명</label>
		<textarea name="comments" class="form-control" rows="10" 
			id="item-content" placeholder="내용">{{ htmlentities($item->comments) }}</textarea>
	</div>

	<div class="form-group">
		<label for="item-parent">상위 분류 (ex: [분류명A] [분류명B])</label>
		<input name="category" type="text" id="item-parent" class="form-control" value="{{ $item->category }}">
	</div>
</div>