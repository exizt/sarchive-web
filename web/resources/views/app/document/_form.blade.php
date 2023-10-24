<div class="">
    <div class="mb-3">
        <input name="title" class="form-control" type="text" autofocus id="title" value="{{ $article->title }}" placeholder="제목 없음" aria-label="제목">
    </div>

    <div class="mb-3">
        <textarea name="content" class="form-control" rows="14" id="content" placeholder="내용">{!! $article->content !!}</textarea>
    </div>

    <div class="mb-3">
        <label class="mb-2" for="folderName">폴더 선택</label>
        <input id="folderName" class="form-control" type="text" placeholder="" readonly @isset($folder) value="{{ $folder->name }}" @endisset
            data-bs-toggle="modal" data-bs-target="#modalChoiceFolder"
            data-folder-id-target="#folder_id"
            data-folder-name-target="#folderName">
        <input name="folder_id" type="hidden" id="folder_id" @isset($folder) value="{{ $folder->id }}" @endisset>
        <small class="text-mute font-italic font-weight-light">* 미선택시 루트 경로가 됩니다.</small>
    </div>

    <div class="mb-3">
        <label class="mb-2" data-auto-click="true">분류 (ex: [분류명A] [분류명B])</label>
        <input name="category" type="text" class="form-control" value="{{ $article->category }}" placeholder="" aria-label="">
        <small class="text-mute font-italic font-weight-light">* 여러 개를 지정할 수 있는 분류입니다.</small>
    </div>


    <div class="mb-3">
        <label class="mb-2" for="articleReference">원문 출처</label>
        &nbsp;<span class="badge badge-secondary shh-evt-append-ref" data-value="나">내가 작성함</span>
        &nbsp;<span class="badge badge-secondary shh-evt-append-ref" data-value="펌">펌글</span>
        &nbsp;<span class="badge badge-secondary shh-evt-append-ref" data-value="">비우기</span>
        <input name="reference" type="text" id="articleReference" class="form-control" value="{{ $article->reference }}" placeholder="" aria-label="">
    </div>
</div>
@include('app.folder-selector.modal')
<!-- <script src="/assets/lib/tinymce/tinymce.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.11/tinymce.min.js" integrity="sha512-3tlegnpoIDTv9JHc9yJO8wnkrIkq7WO7QJLi5YfaeTmZHvfrb1twMwqT4C0K8BLBbaiR6MOo77pLXO1/PztcLg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.8/tinymce.min.js" integrity="sha512-Fwpo5bTphIDwASC+rpciyPlQL/tQkhLNviFX9fopa91iYw0KovDZSb6GRIF6Odfl1dcAVxvGPG3c9m3mwMrydQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
tinymce.init({
    selector:'textarea',
    height:500,
    menubar: false,
    plugins: "code lists advlist codesample",
    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent code codesample',
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
    ]
});
// forced_root_blcok : 'p' 태그 등이 자동으로 생성되는 옵션(true일 때 p 태그가 자동으로 붙음). 6.0부터는 removed됨.
</script>
<script>
    documentReady(function(){
         document.querySelectorAll(".shh-evt-append-ref").forEach(el => {
            el.addEventListener("click", function(){
                if(typeof $(this).data("value") !== "undefined"){
                    $("#articleReference").val($(this).data("value"))
                }
            })
        });
    });
</script>
