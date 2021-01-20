@extends('layouts.sarchive_layout', ['layoutMode' => 'admin', 'currentMenu'=>'folder-control']) 
@section('title',"아카이브 카테고리 관리")
@section('content')
<div>
	<div class="my-3">
		<h3>폴더 목록</h3>
	</div>
	<div id="messages">
		<div class="shh-alert-msg-tpl alert alert-success alert-dismissible fade show" role="alert" style="display:none">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	</div>
	<ul class="nav nav-tabs" id="navTab">
		@foreach ($archiveList as $item)
		<li class="nav-item"><a class="nav-link" href="#" data-profile="{{$item->id}}">{{$item->name}}</a></li>
		@endforeach
	</ul>
	
	<div id="tree-container" class="mt-1 mb-3"></div>
	<button id="shh-btn-save" class="btn btn-sm btn-primary">저장</button>
	<button id="shh-btn-create" class="btn btn-sm btn-outline-success">게시판 추가</button>
	<button id="shh-btn-rename" class="btn btn-sm btn-outline-success">이름 변경</button>
	<button id="shh-btn-delete" class="btn btn-sm btn-outline-success">삭제</button>
	<button id="shh-btn-save-test" class="btn btn-sm btn-outline-success">저장 (테스트)</button>
</div>

<script src="/assets/lib/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="/assets/lib/jstree/themes/default/style.min.css" />
<script>
var _selectedArchiveId = "";
var _jstreeSelector = "#tree-container";
var _deletedIds = new Array()
$(function () {
	$("#navTab a").first().addClass("active");
	_selectedArchiveId = $("#navTab a").first().data("profile")
	
	createJSTree();
	$('#navTab a').on('click', function (e) {
		e.preventDefault()
		$(this).tab('show')
		_selectedArchiveId = $(this).data("profile")
		ajaxJSTree(_selectedArchiveId)
	})

    ajaxJSTree(_selectedArchiveId);

    $("#shh-btn-save").on("click",function(){
        saveJSTree(true)
	});
	$("#shh-btn-save-test").on("click",function(){
        saveJSTree(false)
	});
	
	$("#shh-btn-create").on("click",jstree_create)
	$("#shh-btn-rename").on("click",jstree_rename)
	$("#shh-btn-delete").on("click",jstree_delete)

});

/**
 * 해당 아카이브의 폴더 목록 조회
 */
 function ajaxJSTree(archiveId){
    $.ajax({
        method : 'GET',
        url: '/admin/folderMgmt/index_ajax',
        dataType: 'json',
        data: { archive_id : archiveId }, //json 타입
        success: function(json){
            var data = [];
            $.each(json.folders,function(i,item){
                var locData = {};
                locData.id = item.id;
                locData.text = item.name;
                if(item.depth == '1'){
					locData.parent = '#';
					locData.state = {opened:true};
                } else {
                    locData.parent = item.parent;
                }
                data[i] = locData;
            });
            redrawJSTree(data)
        },
    });
}

/**
 * 저장 시 이벤트
 * @param {bool} executeFlag (true) 실행, (false) 디버그만 함
 */
function saveJSTree(executeFlag){

	var jsonNodes = $(_jstreeSelector).jstree(true).get_json('#', { 
		flat: true, no_state: true, no_li_attr:true, no_data:true, 
		no_a_attr:true })
	//var jsonData = JSON.stringify(jsonNodes)
	var dataSet = {};
	dataSet.jsonNodes = jsonNodes
	dataSet.deleted = _deletedIds
	if(executeFlag){
		doSave(dataSet);
	} else {
		//console.log(dataSet);
		console.log(JSON.stringify(dataSet));
	}
}

function jstree_create() {
	var ref = $(_jstreeSelector).jstree(true),
		sel = ref.get_selected();
	if(!sel.length) { return false; }
	sel = sel[0];
	sel = ref.create_node(sel, {"type":"file"});
	if(sel) {
		ref.edit(sel);
	}
};
function jstree_rename() {
	var ref = $(_jstreeSelector).jstree(true),
		sel = ref.get_selected();
	if(!sel.length) { return false; }
	sel = sel[0];
	ref.edit(sel);
};
function jstree_delete() {
	var ref = $(_jstreeSelector).jstree(true),
		sel = ref.get_selected();
	if(!sel.length) { return false; }
	ref.delete_node(sel);
};

/**
 * 
 */
function doSave(dataSet){
	var s;
	$.post({
        url: '/admin/folderMgmt/updateList',
        dataType: 'json',
		beforeSend: function(){
			s = showAlertMessage("waiting...",'warning');
		},
		data: { 
			archive_id : _selectedArchiveId,
			deleted_list : dataSet.deleted,
			list_data: dataSet.jsonNodes
		},
    }).done(function(json){
		showAlertMessage("저장되었습니다.")
    }).always(function(){
		s.alert("close")
		ajaxJSTree(_selectedArchiveId)
	});
}


function showAlertMessage(msg, alert){
	var s = $("#messages").find(".shh-alert-msg-tpl").first().clone();
	if(msg==="") msg = "&nbsp;"
	s.removeClass("shh-alert-msg-tpl")
	s.append(msg)
	s.show() //tpl 에는 display:none 이기 때문에, 이것을 제거. (참고. 아직 DOM 에 추가되지 않은 object 변수상태임)
	// bootstrap 의 기능인 alert() 가 있을 시에 아래 동작.
	if(jQuery().alert) {
		s.alert()
	}
	if(alert=="warning"){
		s.removeClass("alert-success").addClass("alert-warning")
	} else if(alert=="danger"||alert=="error") {
		s.removeClass("alert-success").addClass("alert-danger")
	}
	s.appendTo("#messages");
	return s;
}





/**
 * 트리에서 바인딩할 이벤트
 */
function jstree_check_callback( op, node, parent, pos, more){
	// 루트 노드는 건들지 않도록 함.
	if(op === "delete_node"|| op === "copy_node" || op === "move_node"){
		/*
		if(parent.id === "#"){
			console.log("루트 노드는 이동,삭제,복사를 불허합니다.");
			return false;
		}
		*/
	}
	if(op === "delete_node"){
		// 신규가 아닌 경우에 한해서 삭제된 목록에 추가
		if(node.id.charAt(0)!=="j"){
			_deletedIds.push(node.id)
		}
		// 하위 노드 중에서 신규가 아닌 경우에 한해서 삭제된 목록에 추가
		$.each(node.children_d,function(k,v){
			if(v.charAt(0)!=="j"){
				_deletedIds.push(v)
			}
		})
	}
	return true;
}


/**
 * tree 를 생성
 */
function createJSTree(){
    //console.log(dataSet);
    $(_jstreeSelector).jstree({
        "core":{
            "themes" : { "stripes" : true },
            "check_callback" : jstree_check_callback,
			"multiple" : false
        },
		"plugins" : [ "dnd", "types", "state", "contextmenu", ],
		"types" : {     
			"#" : {
				"max_children" : 1
			}
		},
    }); 
}


/**
 * 트리를 새로 그림
 */
function redrawJSTree(dataJson){
	var $jstree = $(_jstreeSelector)
	$jstree.jstree(true).settings.core.data = dataJson
	$jstree.jstree(true).refresh()
	_deletedIds = new Array();
}

</script>
@endsection