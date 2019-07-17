@extends('layouts.admin_layout') 
@section('title',"아카이브 카테고리 관리")
@section('title-layout',"아카이브 카테고리 관리")
@section('content')
<div class="my-3">
	<h3>게시판 목록</h3>
	<a href="{{ route($ROUTE_ID.'.create') }}" class="btn btn-sm btn-outline-success">카테고리 추가</a>
</div>
<ul class="nav nav-tabs" id="navTab">
	<li class="nav-item"><a class="nav-link active" href="#" data-profile="1">개발 아카이브</a></li>
	<li class="nav-item"><a class="nav-link" href="#" data-profile="2">일반 아카이브</a></li>
</ul>
<div id="jstree"></div>
<button id="confirm">확인</button>
<script src="/assets/lib/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="/assets/lib/jstree/themes/default/style.min.css" />
<script>
var _selectedProfileId = "1";
$(function () {
	_selectedProfileId = $("#navTab a").first().data("profile")
	
	createJSTree();
	$('#navTab a').on('click', function (e) {
		e.preventDefault()
		$(this).tab('show')
		_selectedProfileId = $(this).data("profile")
		ajaxJSTree(_selectedProfileId)
	})

    ajaxJSTree(_selectedProfileId);

    $("#confirm").on("click",function(){
        var jsonNodes = $('#jstree').jstree(true).get_json('#', { 
			flat: true, no_state: true, no_li_attr:true, no_data:true, 
			no_a_attr:true })
        //console.log(jsonNodes);
        var jsonData = JSON.stringify(jsonNodes)
		//console.log(jsonData);
		doSave(jsonNodes);
    });
});


function doSave(jsonNodes){
	//console.log(jsonNodes);
	$.ajax({
        method : 'POST',
        url: '/admin/archiveBoard/updateList',
        dataType: 'json',
		data: { 
			profileId : _selectedProfileId,
			listData: jsonNodes
		}, //json 타입
        success: function(json){
			// ajaxJSTree(1)
			
        },
    });
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
    $('#jstree').jstree({
        "core":{
            "themes" : { "stripes" : true },
            "check_callback" : true,
        },
		"plugins" : [ "dnd", "types", "state" ],
		"types" : {     
			"#" : {
				"max_children" : 1
			}
		},
    }); 
}
function redrawJSTree(dataJson){
	$jstree = $('#jstree')
	$jstree.jstree(true).settings.core.data = dataJson
	$jstree.jstree(true).refresh()
}
</script>

@stop