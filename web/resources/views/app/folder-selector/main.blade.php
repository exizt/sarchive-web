<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css" integrity="sha512-A5OJVuNqxRragmJeYTW19bnw9M2WyxoshScX/rGTgZYj5hRXuqwZ+1AVn2d6wYTZPzPXxDeAGlae0XwTQdXjQA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js" integrity="sha512-ekwRoEshEqHU64D4luhOv/WNmhml94P8X5LnZd9FNOiOfSKgkY12cDFz3ZC6Ws+7wjMPQ4bPf94d+zZ3cOjlig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body @isset($bodyParams) @foreach ($bodyParams as $k => $v) @if(!empty($v)) data-{{$k}}="{{$v}}" @endif @endforeach @endisset>
<div id="jstree_demo_div"></div>
</body>
<script>
/**
 * 더블클릭 버그 있으므로 확인 필요함
 */
var _jstreeSelector = '#jstree_demo_div';
jsTreeHandler()


function jsTreeHandler(){
    var jsTree = $(_jstreeSelector);
    // var excludedIds = getExcludedIds()

    // jsTree 생성
    jsTree.jstree({
        'core' : {
            'data' : loadRootData,
            "check_callback" : true,
        }
    });

    const parent_selectedFolderId_id = getFolderIdSelectorOfParent()
    const parent_selectedFolderName_id = getFolderNameSelectorOfParent()
    // jsTree 변경시 이벤트
    jsTree.on('changed.jstree', function(e, data){
        if(data && data.selected && data.selected.length){
            const id = data.selected.join(':')
            if(data.node.children.length == 0){
                loadChildData(id)
            }
            // 선택된 상황이므로 이벤트 처리.
            if(typeof parent !== "undefined"){
                var parent_selectedFolderId_el = parent.document.getElementById(parent_selectedFolderId_id)
                if(parent_selectedFolderId_el != null){
                    // folderId 변경
                    parent_selectedFolderId_el.value = id

                    // folderName 변경
                    parent.document.getElementById(parent_selectedFolderName_id).value = data.node.text
                }
            }
        }
    });

    /**
     * 최상단 노드 조회
     */
    function loadRootData(node, cb){
        var params = {
            archive_id : getArchiveId()
        }
        loadData(params, function(data){
            var ret = [];
            $.each(data, function(i, item){
                //console.log(item);
                const parent = (item.parent == '0') ? '#' : item.parent
                const node = {
                    id : item.id,
                    text : item.text,
                    parent: parent
                }
                if ( !isExcludedId(item.id) ) {
                    ret[i] = node
                }
            })
            cb.call(this, ret);
        })
    }

    /**
     * 자식 노드 조회
     */
    function loadChildData(parentId){
        var params = {
            folder_id: parentId
        }
        loadData(params, function(data){
            $.each(data, function(i, item){
                const node = {
                    id : item.id,
                    text : item.text,
                    parent: item.parent
                }
                if ( !isExcludedId(item.id) ) {
                    jstree_createNode(node)
                }
            })
            $(_jstreeSelector).jstree(true).open_node(parentId)
        })
    }

    /**
     * 폴더 목록 Ajax 조회
     */
    function loadData(params, callback){
        $.get('/ajax/folderList', params)
        .done(function(data){
            callback(data)
        })
    }

    /**
     * 노드 추가
     */
    function jstree_createNode(node) {
        var ref = $(_jstreeSelector).jstree(true),
            sel = ref.get_selected();
        if(!sel.length) { return false; }
        sel = sel[0];
        //sel = ref.create_node(sel, {"type":"file"});

        //create_node ([par, node, pos, callback, is_loaded])
        ref.create_node(sel, node)
    }

    /**
     * 폴더 id가 제외할 아이디인지 여부
     *
     * @return true 제외 대상 / false 제외 대상이 아님
     */
    function isExcludedId(id){
        const excludedIdList = getExcludedIds()
        // excludedIds 가 설정되어 있지 않으면 false 반환
        if ( !excludedIdList ){
            return false
        }
        // 제외할 아이디에 포함되어 있으면 true
        if( excludedIdList.includes(""+id) ){
            return true
        }
        return false
    }
}

/**
 * 아카이브 id를 가져옴
 */
function getArchiveId(){
    //return $("body").data("archive")
    return document.body.dataset.archive
}

/**
 * '폴더 아이디'를 기입할 부모창의 selector id 값
 */
function getFolderIdSelectorOfParent(){
    return document.body.dataset.folderIdOfParent ?? "selectedFolderId"
    // var s = document.body.dataset.folderIdOfParent
    // return (typeof s === "undefined" || s == "")? "selectedFolderId" : s
}

/**
 * '폴더 이름'을 기입할 부모창의 selector id 값
 */
function getFolderNameSelectorOfParent(){
    return document.body.dataset.folderNameOfParent ?? "selectedFolderName"
    // return ( !!s ) ? s : "selectedFolderName"
}

/**
 * 제외될 폴더 목록 설정 가져오기 (body 태그를 참조함)
 *
 * @return array|null
 */
function getExcludedIds(){
    const excludedStr = document.body.dataset.excluded
    return ( !!excludedStr ) ? `${excludedStr}`.split(",") : null
}
</script>
</html>
