<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/themes/default/style.min.css" integrity="sha512-P8BwDSUInKMA7I116Z1RFg/Dfk85uFdliEUYO7vQlwtxLVMNvZimfMAQsaf++9EhlAGOVX6yhDQAIY3/70jDUg==" crossorigin="anonymous" />
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/jstree.min.js" integrity="sha512-bU6dl4fd2XN3Do3aWypPP2DcKywDyR3YlyszV+rOw9OpglrGyBs6TyTsbglf9umgE+sy+dKm1UHhi07Lv+Vtfg==" crossorigin="anonymous"></script>
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
    var excludedIds = getExcludedIds()
    jsTree.jstree({
      'core' : {
        'data' : loadRootData,
        "check_callback" : true,
      }
    });

    var pFolderIdSel = getFolderIdSelectorOfParent()
    var pFolderNameSel = getFolderNameSelectorOfParent()
    jsTree.on('changed.jstree', function(e, data){
      if(data && data.selected && data.selected.length){
        var id = data.selected.join(':')
        if(data.node.children.length == 0){
          loadChildData(id)
        }
        // 선택된 상황이므로 이벤트 처리.
        if(typeof parent !== "undefined"){
          var parSelectedFolderId = parent.document.getElementById(pFolderIdSel)
          if(parSelectedFolderId != null){
            // folderId 변경
            parSelectedFolderId.value = id

            // folderName 변경
            parent.document.getElementById(pFolderNameSel).value = data.node.text
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
          var parent = (item.parent == '0') ? '#' : item.parent
          var node = {
            id : item.id,
            text : item.text,
            parent: parent
          }
          if(validFolderId(item.id)){
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
          var node = {
            id : item.id,
            text : item.text,
            parent: item.parent
          }
          if(validFolderId(item.id)){
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
     * 폴더 id 유효성 체크
     * excludeIds 에 포함된 것은 제외(false 반환)
     */
    function validFolderId(id){
      if(typeof excludedIds === 'undefined' || !excludedIds.includes(""+id)){
        return true
      } else {
        return false
      }
    }
  }

  /**
   *
   */
  function getArchiveId(){
    //return $("body").data("archive")
    return document.body.dataset.archive
  }

  /**
   *
   */
  function getFolderIdSelectorOfParent(){
    var s = document.body.dataset.folderIdOfParent
    return (typeof s === "undefined" || s == "")? "selectedFolderId" : s
  }

  /**
   *
   */
  function getFolderNameSelectorOfParent(){
    var s =  document.body.dataset.folderNameOfParent
    return (typeof s === "undefined" || s == "")? "selectedFolderName" : s
  }

  /**
   * 제외될 폴더 목록 설정 가져오기 (body 태그를 참조함)
   */
  function getExcludedIds(){
    var excludedStr = document.body.dataset.excluded

    //console.log(typeof excludedStr)
    //console.log("1,3,5".split(","))
    if(typeof excludedStr !== "undefined" && excludedStr != ""){
      //
      return `${excludedStr}`.split(",")
    } else {
      return undefined
    }
  }
</script>
</html>
