@extends('layouts.admin_layout') 
@section('title',"아카이브 카테고리 관리")
@section('content')
<div class="my-3">
	<h3>게시판 목록</h3>
</div>
<div id="messages">
	<div class="shh-alert-msg-tpl alert alert-success alert-dismissible fade show" role="alert" style="display:none">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
<ul class="nav nav-tabs" id="navTab">
	@foreach ($ArchiveProfileList as $item)
	<li class="nav-item"><a class="nav-link" href="#" data-profile="{{$item->id}}">{{$item->name}}</a></li>
	@endforeach
</ul>

<div id="tree-container" class="mt-1 mb-3"></div>
<button id="shh-btn-save" class="btn btn-sm btn-primary">저장</button>
<button id="shh-btn-create" class="btn btn-sm btn-outline-success">게시판 추가</button>
<button id="shh-btn-rename" class="btn btn-sm btn-outline-success">이름 변경</button>
<button id="shh-btn-delete" class="btn btn-sm btn-outline-success">삭제</button>
<button id="shh-btn-save-test" class="btn btn-sm btn-outline-success">저장 (테스트)</button>

<script src="/assets/lib/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="/assets/lib/jstree/themes/default/style.min.css" />
<script>
var _selectedProfileId = "";
var _jstreeSelector = "#tree-container";
var _deletedBoardIds = new Array()
$(function () {
	$("#navTab a").first().addClass("active");
	_selectedProfileId = $("#navTab a").first().data("profile")
	
	createJSTree();
	$('#navTab a').on('click', function (e) {
		e.preventDefault()
		$(this).tab('show')
		_selectedProfileId = $(this).data("profile")
		ajaxJSTree(_selectedProfileId)
	})

    ajaxJSTree(_selectedProfileId);

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

function saveJSTree(executeFlag){

	var jsonNodes = $(_jstreeSelector).jstree(true).get_json('#', { 
		flat: true, no_state: true, no_li_attr:true, no_data:true, 
		no_a_attr:true })
	//var jsonData = JSON.stringify(jsonNodes)
	var dataSet = {};
	dataSet.jsonNodes = jsonNodes
	dataSet.deleted = _deletedBoardIds
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
        url: '/admin/archiveBoard/updateList',
        dataType: 'json',
		beforeSend: function(){
			s = showAlertMessage("waiting...",'warning');
		},
		data: { 
			profileId : _selectedProfileId,
			deletedList : dataSet.deleted,
			listData: dataSet.jsonNodes
		},
    }).done(function(json){
		showAlertMessage("저장되었습니다.")
    }).always(function(){
		s.alert("close")
		ajaxJSTree(_selectedProfileId)
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


function ajaxJSTree(profileId){
    $.ajax({
        method : 'GET',
        url: '/admin/archiveBoard/index_ajax',
        dataType: 'json',
        data: {profile:profileId}, //json 타입
        success: function(json){
            var data = [];
            $.each(json.boards,function(i,item){
                var locData = {};
                locData.id = item.id;
                locData.text = item.name;
                if(item.parent == '0'){
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

function jstree_check_callback( op, node, parent, pos, more){
	// 루트 노드는 건들지 않도록 함.
	if(op === "delete_node"|| op === "copy_node" || op === "move_node"){
		if(parent.id === "#"){
			console.log("루트 노드는 이동,삭제,복사를 불허합니다.");
			return false;
		}
	}
	if(op === "delete_node"){
		if(node.id.charAt(0)!=="j"){
			_deletedBoardIds.push(node.id)
		}
		$.each(node.children_d,function(k,v){
			if(v.charAt(0)!=="j"){
				_deletedBoardIds.push(v)
			}
		})
	}
	return true;
}
function redrawJSTree(dataJson){
	var $jstree = $(_jstreeSelector)
	$jstree.jstree(true).settings.core.data = dataJson
	$jstree.jstree(true).refresh()
	_deletedBoardIds = new Array();
}

</script>
@endsection