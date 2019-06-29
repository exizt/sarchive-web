<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * 이 페이지는 완료된 일정을 조회하는 페이지 입니다.
 * TODO
 * 1. 월일 로 보는 기능을 구현해야 합니다.
 * 2. 페이징이 가능하도록 구현해야 합니다.
 * 3. 현재 planner 컨트롤러에서 분리를 해야할수도 있습니다. (고려중)
 */
?>
<script>
$(document).ready(function(){
	appendPageContents();
	$(document).on("click",".event-option-complete",completeTask);
});
var json_plans;

/* 아이콘 설정 */
var icon_completed = '<i class="fa fa-check-circle fa-fw" aria-hidden="true"></i>';
var icon_incompleted = '<i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>';
var icon_item_remove = '<i class="fa fa-minus fa-fw" aria-hidden="true"></i>';
var icon_item_option = '<i class="fa fa-ellipsis-v fa-fw" aria-hidden="true"></i>';

/**
 * 업무를 완료 처리 한다.
 */
function completeTask(e)
{
	e.preventDefault();
	var data_srl = $(this).closest("tr").attr("data-srl");
	var data_completedYn = $(this).closest("tr").attr("data-completedYn");
	// ajax process
	$.ajax({
		url : current_url + "/toggleCompletedPlan",
		method : "post",
		data : json_csrf({data_srl:data_srl,completed_yn:data_completedYn}),
		dataType : "html",
		success : eventSuccess,
		error : function(xhr, status, error) {alert(error);}
	});

	function eventSuccess(data)
	{
		alert(data);
		appendPageContents();
        //reset();
	}
}

function json_csrf(data)
{
	return data;
	/*
	return $.extend(true,{"<?=$this->security->get_csrf_token_name()?>": '<?= $this->security->get_csrf_hash()?>'},data);
	*/
}


/**
 * 페이지 컨텐츠 구현
 */
function appendPageContents()
{
	getCompletePlans();
}
/**
 * 완료된 계획 조회
 * Ajax 통신으로 완료된 계획들을 조회하고, 화면에 그린다.
 */
function getCompletePlans()
{
	// ajax process
	$.ajax({
		url:current_url + "/JSONCompletePlans",
		method:"get",
		dataType:"json",
		success: eventSuccess,
		error: function(xhr, status, error) {alert(error);}
	});

	function eventSuccess(data)
	{
		appendItemsFromJSON(data,"#task-inbox>tbody");
		/*
		resetList();
		var html = "";
		var contents = data;
		for(var i in contents)
		{
			var item = contents[i];
			html += "<tr data-srl="+ item.plan_id+" pstatus-srl='Y'>";
			html += '<td style="width:30px;"><a href="#" class="event-option-complete">'+icon_completed+'</a></td>';
			html += '<td class="plans-subject">';
			html += '<div><del>' + item.subject + '</del></div>';
			html += '<div class="plans-details" style="display:none;">[수정]</div>';
			html += '</td>';
			html += '<td style="width:30px;"><a href="#" class="event-option-remove">' + icon_item_remove + '</a></td>';
			html += '<td style="width:30px;"><a href="#" class="event-option-detail">' + icon_item_option  + '</a></td>';
			html += "</tr>";
		}
		$("#task-inbox>tbody").append(html);
		json_plans = data;
		*/
	}
}
/**
 * 아이템을 화면에 그려주는 메서드
 */
function appendItemsFromJSON(data,selector)
{
 	$(selector).html("");
 	var html = "";
 	for(var i in data)
 	{
 		var item = data[i];
 		html += "<tr data-srl="+ item.todo_key+" data-completedYn="+item.details.completed_yn+">";
 		
 		var plans_subject = '<div>' + item.subject + '</div>';
 		if(item.details.completed_yn=='Y'){
 			plans_subject = '<div><del>' + item.subject + '</del></div>';
 			icon_status = icon_completed;
 		} else {
			icon_status = icon_incompleted;
 		}
 		
 		html += '<td style="width:30px;"><a href="#" class="event-option-complete">'+icon_status+'</a></td>';
 		html += '<td class="plans-subject">';
 		html += plans_subject;
 		html += '<div class="plans-details" style="display:none;">[수정]</div>';
 		html += '</td>';
 		html += '<td style="width:30px;"><a href="#" class="event-option-remove">' + icon_item_remove + '</a></td>';
 		html += '<td style="width:30px;"><a href="#" class="event-option-detail">' + icon_item_option  + '</a></td>';
 		html += "</tr>";
 	}
 	$(selector).append(html);
 	json_plans = data;
}
function resetList()
{
	$("#task-inbox>tbody").html("");
}
</script>
<div class="container">
	<div>
		<h3>완료된 항목</h3>
		<table class="table table-sm table-hover table-striped" id="task-inbox">
			<thead>
				<tr>
					<th colspan="4">To do 목록</th>
				</tr>
			</thead>		
			<tbody>
			</tbody>
		</table>
	</div>
</div>