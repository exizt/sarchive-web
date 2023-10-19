<div class="">
    <div class="form-group">
        <label for="frm-item-name">폴더명</label>
        <input name="name" class="form-control" type="text" autofocus id="frm-item-name"
            value="{{ $item->name }}" placeholder="명칭" aria-label="명칭">
    </div>

    <div class="form-group">
        <label for="frm-item-text">요약 설명</label>
        <input name="comments" type="text" id="frm-item-text" class="form-control"
            value="{{ $item->comments }}" placeholder="" aria-label="">
    </div>


    <div class="form-group">
        <label for="folderName">상위 폴더 선택</label>
        <input id="folderName" class="form-control" type="text" placeholder="" readonly
            @isset($parentFolder) value="{{ $parentFolder->name }}" @endisset>
        <input name="parent_id" type="hidden" id="parent_id" @isset($parentFolder) value="{{ $parentFolder->id }}" @endisset>
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
    documentReady(function(){
        /*
        $("#folderName").on("click", function(){
            $('#modalChoiceFolder').modal('show')
            loadFolderSelectorIframe("jsTreeIframe",getArchiveId(), {
                idReturn: "selectedFolderId",
                nameReturn: "selectedFolderName",
                excluded: getBodyParam("folder")
            })
        })
        */
        // '폴더 선택' 입력창 클릭시 이벤트 바인딩
        document.getElementById("folderName").addEventListener('click', event => {
            $('#modalChoiceFolder').modal('show')
            loadFolderSelectorIframe("jsTreeIframe",getArchiveId(), {
                idReturn: "selectedFolderId",
                nameReturn: "selectedFolderName",
                excluded: getBodyParam("folder")
            })
        })
        bindFolderSelectorDialog()
    });

    function bindFolderSelectorDialog(){
        var folder_id_sel = "parent_id"
        var folder_name_sel = "folderName"
        // '변경' 버튼 클릭시 이벤트
        document.getElementById("btnChangeFolderId").addEventListener('click', event => {
            var folderId = document.getElementById("selectedFolderId").value
            var folderName = document.getElementById("selectedFolderName").value
            setFolderIdName(folderId, folderName)
        })
        // '선택 없애기' 버튼 클릭시 이벤트
        document.getElementById("btnChangeFolderIdNone").addEventListener('click', event => {
            setFolderIdName("0","")
        })

        // 폴더 id, name을 input에 지정하는 함수
        function setFolderIdName(id, name){
            document.getElementById(folder_id_sel).value = id
            document.getElementById(folder_name_sel).value = name
        }
    }
    </script>
