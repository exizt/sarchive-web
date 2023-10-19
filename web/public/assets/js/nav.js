(function () {
    // folderNav의 셀렉터 아이디
    const navSelectorId = "shh-nav-board-list"

    var documentReady = function(f) {
        document.readyState == 'loading' ? document.addEventListener("DOMContentLoaded", f) : f();
    };

    documentReady(function(){
        if( !document.getElementById(navSelectorId) ) return
        doAjaxFolderList(getArchiveId(), getFolderId())
        bindEditModeBtn()
    });

    /**
     * 우측 folder nav를 그리는 함수
     * @requires axios
     */
    function doAjaxFolderList(archiveId, folderId, theme='bs4'){
        let mode = "folder"

        // archiveId가 없을 때는 실행하지 않음
        if( !archiveId ) return

        if( !document.getElementById(navSelectorId) ) return

        let params = {}
        // folderId가 없을 때
        if( !folderId ) {
            mode = "archive"
            params = { archive_id : archiveId }
        } else {
            params = { archive_id : archiveId, folder_id : folderId }
        }

        axios.get("/ajax/folder_nav", {
            params : params
        }).then(function(response){
            const data = response.data

            // 목록 생성
            buildFolderListItem(mode, data, theme)
        })

        /**
         * 하위 폴더 리스트 생성 함수
         * @param string mode
         * @param object data
         */
        function buildFolderListItem(mode, data, theme){
            // 임시로 사용될 id prefix 설정값 (겹치지 않도록 주의)
            const tempIdNode_prefix = 'folderNav-item-';
            // const tempIdNode_className = 'ar-folder-list-temp';

            // 선택된 폴더의 depth
            const currentDepth = (mode == "folder") ? data.currentFolder.depth : 0

            // 데이터 값
            const dataList = data.list;
            dataList.forEach(function(data, idx){
                result(idx, data)
            })
            // $.each(data.list, result);

            // $("span.ar-folder-list-temp").remove();

            // document.querySelectorAll(`span.${tempIdNode_className}`).forEach(e => e.remove());

            function result(i, item){
                let depth = item.depth - currentDepth
                let nav = document.getElementById(navSelectorId)
                if(depth == 1){
                    nav.innerHTML += buildHtml(item.id, `/folders/${item.id}`,item.name, item.count, depth)
                } else {
                    let repeatString = "❯".repeat(depth-1)
                    let label = `${repeatString}&nbsp;&nbsp;${item.name}`
                    let html = buildHtml(item.id, `/folders/${item.id}`,label, item.count, depth)
                    //document.getElementById(`${tempIdNode_prefix}${item.parent_id}`).nextElementSibling.innerHTML += html
                    // document.getElementById(`${tempIdNode_prefix}${item.parent_id}`).insertAdjacentHTML('beforebegin', html);
                    document.getElementById(`${tempIdNode_prefix}${item.parent_id}`).insertAdjacentHTML('afterend', html)
                }

                // html 생성
                function buildHtml(id, link, label, count, depth){
                    if(theme='bs4'){
                        return `<a href="${link}" id="${tempIdNode_prefix}${id}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center sarc-depth-${depth}"
                            data-id="${id}" data-label="${label}">
                                ${label}
                                <span class="arch-indexEditMode-hide badge badge-secondary badge-pill">${count}</span>
                                <span style="display:none;width:50px" class="arch-indexEditMode-show">
                                    <span class="badge badge-secondary arch-indexEditMode-up">▲</span>
                                    <span class="badge badge-secondary arch-indexEditMode-down">▼</span>
                                </span>
                        </a>`
                    } else if(theme='tailwind') {
                        return ''
                    }
                }
            }
        }
    }

    /* */
    function bindEditModeBtn(){
        var listItemClassName = "arch-indexEditMode-listitem";
        var listItemSelector = "."+listItemClassName;

        // folderNav에서 '변경' 버튼.
        // $("#btnIndexEditModeToggle").on("click",changeIndexEditModeOn)
        document.getElementById('btnIndexEditModeToggle').addEventListener("click", changeIndexEditModeOn)

        // folderNav에서 '취소' 버튼.
        // $("#btnIndexEditModeCancel").on("click",function(){location.reload();})
        document.getElementById('btnIndexEditModeCancel').addEventListener("click", e=>{ location.reload() })

        // folderNav에서 '순서변경 저장' 버튼.
        // $("#btnIndexEditModeSave").on("click",saveArchiveSort)
        document.getElementById('btnIndexEditModeSave').addEventListener("click", saveArchiveSort)

        /**
         * 아카이브의 순서를 변경하는 기능
         */
        function changeIndexEditModeOn(){
            // folderNav에 맞춘 작업
            $(".sarc-depth-1").addClass(listItemClassName);
            document.querySelectorAll('.sarc-depth-2').forEach(e => e.remove());
            document.querySelectorAll('.sarc-depth-3').forEach(e => e.remove());

            // 일반적인 보여지는 부분 처리
            // $(".arch-indexEditMode-hide").hide()
            document.querySelectorAll('.arch-indexEditMode-hide').forEach(e => {
                // e.remove()
                e.style.display = 'none'
            });
            // $(".arch-indexEditMode-show").show()
            document.querySelectorAll('.arch-indexEditMode-show').forEach(e => {
                // e.remove()
                e.style.display = 'block'
            });
            $(listItemSelector).attr("href","#");

            // 상하 버튼 이벤트 바인딩
            document.querySelectorAll('.arch-indexEditMode-up').forEach(el => {
                el.addEventListener("click", moveUpItem )
            });
            document.querySelectorAll('.arch-indexEditMode-down').forEach(el => {
                el.addEventListener("click", moveDownItem )
            });

            function moveUpItem(){
                // console.log(this)

                let item = this.closest(".sarc-depth-1")
                let before_item = item.previousElementSibling;
                // console.log(before_item)
                if( !! before_item ){
                    before_item.before(item)
                }
            }

            function moveDownItem(){
                // console.log(this)

                let item = this.closest(".sarc-depth-1")
                let next_item = item.nextElementSibling;
                // console.log(before_item)
                if( !! next_item){
                    next_item.after(item)
                }
            }
        }

        /**
         * 아카이브 순서 변경사항을 저장
         */
        function saveArchiveSort(){
            let dataList = [];
            document.querySelectorAll('.sarc-depth-1').forEach((el, index) => {
                // console.log(index)
                let item_id = el.dataset.id
                let item_label = el.dataset.label

                let data = {
                    id : item_id,
                    name : item_label,
                    index : index+1
                };
                dataList.push(data)
            });
            // console.log(dataList)

            ajaxSave(dataList)
        }

        /**
         * post ajax 전송
         */
        function ajaxSave(dataList){
            axios.post("/folders/updateSort", {
                'dataList': dataList
            }).then(function(response){
                location.reload()
            })
        }
    }
})();
