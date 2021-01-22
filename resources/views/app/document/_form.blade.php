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
		<input name="title" class="form-control" type="text" autofocus id="title" value="{{ $article->title }}" placeholder="제목" aria-label="제목">
	</div>

	<div class="form-group">
		<textarea name="content" class="form-control" rows="14" id="content" placeholder="내용">{!! $article->content !!}</textarea>
	</div>

	<div class="form-group">
    <label for="folderName">폴더 선택</label>
    <input id="folderName" class="form-control" type="text" placeholder="" readonly @isset($article->folder) value="{{ $article->folder->name }}" @endisset>
		<input name="folder_id" type="hidden" id="folder_id" value="{{ $article->folder_id }}">
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
<div class="modal fade" id="modalChoiceFolder" tabIndex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title">폴더 선택</h6>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
        <iframe id="jsTreeIframe" src=""></iframe>
			</div>
			<div class="modal-footer">
        <input type="hidden" id="selectedFolderId">
        <input type="hidden" id="selectedFolderName">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnChangeFolderId">변경</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
        <button type="button" class="btn btn-default" data-dismiss="modal" id="btnChangeFolderIdNone">선택 없애기</button>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	$(".shh-evt-append-ref").on("click",function(){
		if(typeof $(this).data("value") !== "undefined"){
			$("#articleReference").val($(this).data("value"))
		}
	});
	$("#folderName").on("click", function(){
		console.log("click")
		$('#modalChoiceFolder').modal('show')
    loadJsTreeIframe()
  })
  $("#btnChangeFolderId").on("click", function(){
    var folderId = $("#selectedFolderId").val()
    $("#folder_id").val(folderId)
    var folderName = $("#selectedFolderName").val()
    $("#folderName").val(folderName)
  })
  $("#btnChangeFolderIdNone").on("click", function(){
    $("#folder_id").val("")
    $("#folderName").val("")
  })
});

function loadJsTreeIframe(){
  document.getElementById("jsTreeIframe").src = "/jstree-ajax.html";
}


</script>
