(function () {
    // 버튼까지 포함한 영역의 selector id
    const containerId = "navFolder"

    // listGroup에 대한 selector id
    const navListSelectorId = "navFolderList"

    var documentReady = function(f) {
        document.readyState == 'loading' ? document.addEventListener("DOMContentLoaded", f) : f();
    };

    documentReady(function(){
        if( !document.getElementById(navListSelectorId) ) return
        doAjaxFolderList(getArchiveId(), getFolderId())
        bindReorderMode()
    });

    /**
     * 우측 folder nav를 그리는 함수
     * @requires axios
     */
    function doAjaxFolderList(archiveId, folderId, theme='bs4'){
        let mode = "folder"

        // archiveId가 없을 때는 실행하지 않음
        if( !archiveId ) return

        if( !document.getElementById(navListSelectorId) ) return

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
            const tempIdNode_prefix = 'tmpFolderNav-item-';
            // const tempIdNode_className = 'ar-folder-list-temp';

            // 선택된 폴더의 depth
            const currentDepth = (mode == "folder") ? data.currentFolder.depth : 0

            // 데이터 값
            const dataList = data.list;
            dataList.forEach(function(data, idx){
                result(idx, data)
            })

            // 임시로 만든 span 태그 삭제
            document.querySelectorAll(`#${navListSelectorId} > span`).forEach(el => el.remove());

            /**
             * 결과를 html로 표현.
             *
             * 1단계 아이템은 순서대로 잘 생성이 되지만, 2단계부터는 순서가 애매하다.
             * 1단계 A아이템 뒤로 붙인다고 가정할 때, 하나씩 붙다보면 순서가 역순이 되버릴 수 있다.
             * 그래서 span 태그를 하나 추가하고, 그 앞에 붙이는 식으로 트릭을 써야 한다.
             */
            function result(i, item){
                let depth = item.depth - currentDepth
                let nav = document.getElementById(navListSelectorId)
                if(depth == 1){
                    nav.innerHTML += buildHtml(item.id, `/folders/${item.id}`,item.name, item.count, depth)
                } else {
                    let repeatString = "❯".repeat(depth-1)
                    let label = `${repeatString}&nbsp;&nbsp;${item.name}`
                    let html = buildHtml(item.id, `/folders/${item.id}`,label, item.count, depth)
                    //document.getElementById(`${tempIdNode_prefix}${item.parent_id}`).nextElementSibling.innerHTML += html
                    document.getElementById(`${tempIdNode_prefix}${item.parent_id}`).insertAdjacentHTML('beforebegin', html)
                    // document.getElementById(`${tempIdNode_prefix}${item.parent_id}`).insertAdjacentHTML('afterend', html)
                }

                // html 생성
                function buildHtml(id, link, label, count, depth){
                    if(theme='bs4'){
                        return `<a href="${link}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            data-id="${id}" data-label="${label}" data-depth="${depth}">
                                ${label}
                                <span class="nav-count-badge" data-visible="only_index">${count}</span>
                                <div class="nav-updown-wrap" data-visible="only_edit" style="display:none">
                                    <span class="nav-updown-badge arch-indexEditMode-up">▲</span>
                                    <span class="nav-updown-badge arch-indexEditMode-down">▼</span>
                                </div>
                        </a><span style="display:none" id="${tempIdNode_prefix}${id}"></span>`
                    } else if(theme='tailwind') {
                        return ''
                    }
                }
            }
        }
    }

    /**
     *  '변경' 버튼 클릭시. 순서 변경 모드로 전환.
     */
    function bindReorderMode(){
        if( !document.getElementById(navListSelectorId) ) return

        // folderNav에서 '변경' 버튼.
        document.getElementById('btnFolderNavEditModeToggle').addEventListener("click", toggleReorderMode)

        // folderNav에서 '취소' 버튼.
        document.getElementById('btnFolderNavEditModeCancel').addEventListener("click", e=>{ location.reload() })

        // folderNav에서 '순서변경 저장' 버튼.
        document.getElementById('btnFolderNavEditModeSave').addEventListener("click", saveArchiveSort)

        /**
         * depth의 셀렉터 문자열을 반환하는 함수
         * @param {int} depth
         * @returns
         */
        function getDepthSelector(depth){
            // return `.${navDepthClassPrefix}-${depth}`
            return `#${navListSelectorId} > a[data-depth="${depth}"]`
        }

        /**
         * 모드 변경시 변경할 사항에 대한 선택.
         */
        function getVisibleSelector(mode){
            return `#${containerId} *[data-visible="only_${mode}"]`
        }

        /**
         * 목록 중 하나의 아이템에 대한 가장 큰 범위
         * @returns string
         */
        function getListItemSelector(){
            return `#${navListSelectorId} > a[data-depth="1"]`
        }

        /**
         * 아카이브의 순서를 변경하는 기능
         */
        function toggleReorderMode(){
            // folderNav에 맞춘 작업
            // $(getDepthSelector(1)).addClass(listItemClassName);
            document.querySelectorAll(getDepthSelector(2)).forEach(e => e.remove());
            document.querySelectorAll(getDepthSelector(3)).forEach(e => e.remove());

            // 일반적인 보여지는 부분 처리
            // '순서변경 모드'에서 숨길 것을 hide
            document.querySelectorAll(getVisibleSelector("index")).forEach(e => {
                e.style.display = 'none'
            });
            // '순서변경 모드'에서 숨길 것을 show
            document.querySelectorAll(getVisibleSelector("edit")).forEach(e => {
                e.style.display = 'block'
            });
            // 링크 기능을 해제.
            document.querySelectorAll(getListItemSelector()).forEach(el => {
                el.href = "#"
            });

            // 상하 버튼 이벤트 바인딩
            document.querySelectorAll(".arch-indexEditMode-up").forEach(el => {
                el.addEventListener("click", moveUpItem )
            });
            document.querySelectorAll(".arch-indexEditMode-down").forEach(el => {
                el.addEventListener("click", moveDownItem )
            });

            /**
             * 아이템을 위로 이동
             */
            function moveUpItem(){
                // console.log(this)
                let item = this.closest(getListItemSelector())
                let before_item = item.previousElementSibling;
                // console.log(before_item)
                if( !! before_item ){
                    before_item.before(item)
                }
            }

            /**
             * 아이템을 아래로 이동
             */
            function moveDownItem(){
                // console.log(this)
                let item = this.closest(getListItemSelector())
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
            document.querySelectorAll(getListItemSelector()).forEach((el, index) => {
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
    }
})();
