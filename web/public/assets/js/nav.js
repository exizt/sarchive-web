/**
 * 우측 folder nav를 그리는 함수
 * @requires axios
 */
function doAjaxFolderList(archiveId, folderId, theme='bs4'){
    let mode = "folder"
    const navId = "shh-nav-board-list"

    // archiveId가 없을 때는 실행하지 않음
    if( !archiveId ) return

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
        const tempIdNode_className = 'ar-folder-list-temp';

        // 현재 폴더의 depth
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
            let nav = document.getElementById(navId)
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
        }
        function buildHtml(id, link, label, count, depth){
            const html = `<a href="${link}" id="${tempIdNode_prefix}${id}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center sarc-depth-${depth}"
                data-id="${id}" data-label="${label}">
                    ${label}
                    <span class="arch-indexEditMode-hide badge badge-secondary badge-pill">${count}</span>
            </a>`
            return html
        }
    }
}
