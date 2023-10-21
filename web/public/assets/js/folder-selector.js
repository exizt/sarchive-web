documentReady(function(){
    attachFolderSelectorModal()
});

/**
 * '폴더 선택' 모달 다이얼로그의 이벤트를 바인딩하고 기능을 구성하는 함수
 */
function attachFolderSelectorModal() {
    const folderSelectorModalSelectorId = "modalChoiceFolder"
    const folderSelectorModal = document.getElementById(folderSelectorModalSelectorId)

    if (folderSelectorModal) {
        folderSelectorModal.addEventListener('show.bs.modal', loadFolderSelectorEvent)
        attachFolderSelectorButtonEvents()
    }

    /**
     * 다이얼로그의 show되는 과정에서 벌어지는 이벤트
     */
    function loadFolderSelectorEvent(event) {
        // console.log(this)
        // 다이얼로그를 호출한 개체
        const target = event.relatedTarget
        // 폴더 목록에서 제외되야할 id 값들. (쉼표로 여러개 가능)
        const excludedIds = target.getAttribute("data-folder-excluded") ?? ""
        // folderId의 값이 입력되야 하는 개체의 target
        const folderIdTargetSelector = target.getAttribute("data-folder-id-target") ?? ""
        this.setAttribute("data-folder-id-target", folderIdTargetSelector)
        // folderName의 값이 입력되야 하는 개체의 target
        const folderNameTargetSelector = target.getAttribute("data-folder-name-target") ?? ""
        this.setAttribute("data-folder-name-target", folderNameTargetSelector)

        loadIframe("jsTreeIframe",getArchiveId(), {
            idReturn: "selectedFolderId",
            nameReturn: "selectedFolderName",
            excluded: excludedIds
        })

        /**
         * Modal 내의 iframe에 folderSelector 로드
         */
        function loadIframe(iframeId, archiveId, options) {
            var url = `/folder-selector?archive=${archiveId}`

            if(typeof options !== "undefined"){
                var s = new URLSearchParams(options).toString();
                url += "&"+s
            }
            document.getElementById(iframeId).src = url;
        }
    }

    /**
     * 폴더 선택 다이얼로그의 버튼 등의 이벤트 바인딩
     */
    function attachFolderSelectorButtonEvents() {
        // '변경' 버튼 클릭시 이벤트
        document.getElementById("btnChangeFolderId").addEventListener('click', event => {
            // selectedFolderId, selectedFolderName은 다이얼로그 내에 hidden으로 갖고 있는 input
            const selectedFolderId = document.getElementById("selectedFolderId").value
            const selectedFolderName = document.getElementById("selectedFolderName").value
            setFolderIdName(selectedFolderId, selectedFolderName)
        })

        // '선택 없애기' 버튼 클릭시 이벤트
        document.getElementById("btnChangeFolderIdNone").addEventListener('click', event => {
            setFolderIdName("0","")
        })

        // 폴더 id, name을 input에 지정하는 함수
        function setFolderIdName(folderId, folderName) {
            const id_selector = folderSelectorModal.getAttribute("data-folder-id-target") ?? ""
            const name_selector = folderSelectorModal.getAttribute("data-folder-name-target") ?? ""

            if( !!id_selector && !! (obj = document.querySelector(id_selector)) ){
                obj.value = folderId
                // document.querySelector(id_selector)?.value = folderId
            }
            if( !!name_selector && !! (obj = document.querySelector(name_selector)) ){
                obj.value = folderName
                // document.querySelector(name_selector)?.value = folderName
            }
        }
    }
}
