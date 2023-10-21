<div class="modal fade" id="modalChoiceFolder" tabIndex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">폴더 선택</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="jsTreeIframe" src="" width="100%" style="position: relative; width:100%"></iframe>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="selectedFolderId">
                <input type="hidden" id="selectedFolderName">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal" id="btnChangeFolderId">선택</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">취소</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal" id="btnChangeFolderIdNone">선택 없애기</button>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/folder-selector.js"></script>
