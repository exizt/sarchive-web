@extends('layouts.myservice_layout')

@section('content')

<script src="/assets/lib/jquery-plugins/jquery.cookie.js"></script>
<script>
$(document).ready(function(){
	redrawPageContents();
	$("#form-task").on("submit",submitTask);
	$(document).on("click",".event-option-remove",removeTask);
	$(document).on("click",".event-option-complete",completeTask);
	$(document).on("click",".event-option-detail",detailTask);
});
/* 아이콘 설정 */
var icon_completed = '<i class="fa fa-check-circle fa-fw" aria-hidden="true"></i>';
var icon_incompleted = '<i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>';
var icon_item_remove = '<i class="fa fa-minus fa-fw" aria-hidden="true"></i>';
var icon_item_option = '<i class="fa fa-ellipsis-v fa-fw" aria-hidden="true"></i>';
var json_plans;
function redrawPageContents()
{
	getInBoxList();
	getTodayComplete();
}
/**
 * Inbox 목록을 조회하는 메서드
 */
function getInBoxList()
{
	/*
	* Ajax 통신
	*/
	$.ajax({
		url : SERVICE_URI + "/JSONInBox",
		method : "get",
		dataType : "json",
		success : eventSuccess,
		error : function(xhr, status, error) {alert(error);}
	});
	/*
	* 성공시
	*/
	function eventSuccess(data)
	{
		appendItemsFromJSON(data,"#task-inbox>tbody");
	}
}
/**
 * 
 * 금일 완료 목록 조회
 * 
 */
function getTodayComplete()
{
	/*
	* Ajax 통신
	*/
	$.ajax({
		url: SERVICE_URI + "/JSONCompleteToday",
		method:"get",
		dataType:"json",
		success: eventSuccess,
		error: function(xhr, status, error) {alert(error);}
	});
	/*
	* 성공시
	*/
	function eventSuccess(data)
	{
		appendItemsFromJSON(data,"#task-todayComplete>tbody");
	}
}
/**
 * 업무의 상세보기
 */
function detailTask(e)
{
	e.preventDefault();
	var parent_tr = $(this).closest("tr");
	var data_srl = parent_tr.attr("data-srl");

	parent_tr.find(".plans-details").toggle();
}
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
		url : SERVICE_URI,
		method : "POST",
		data : {mode:"toggleCompletedPlan",data_srl:data_srl,completed_yn:data_completedYn},
		dataType : "html",
		success : eventSuccess,
		error : function(xhr, status, error) {alert(error);}
	});

	function eventSuccess(data)
	{
		//alert(data);
		redrawPageContents();
        //reset();
	}
}
/**
 * 할일 삭제
 * 삭제 여부를 물어본 뒤 삭제를 처리한다.
 */
function removeTask(e)
{
	e.preventDefault();
	var parent_tr = $(this).closest("tr");
	var data_srl = parent_tr.attr("data-srl");
	var flag = false; //중복 이벤트 발생 방지
	var subject = parent_tr.find("td.plans-subject>div").html();
	var dialog_contents = "계획명 : "+subject;

	//call Modal Dialog
	confirmModal("삭제하시겠습니까?", dialog_contents, "취소", "삭제", function(){
		removeTaskAjax(data_srl);
	});
	
	function removeTaskAjax(data_srl)
	{
		// ajax process
		$.ajax({
			url : SERVICE_URI,
			method : "POST",
			data : {mode:"removePlan",data_srl:data_srl},
			dataType : "html",
			success : eventSuccess,
			error : function(xhr, status, error) {alert(error);}
		});
	}

	function eventSuccess(data)
	{
		//alert(data);
		redrawPageContents();
        //reset();
	}
}
/**
 * Inbox 입력 메서드
 */
function submitTask(e)
{
	e.preventDefault();
	var form = $(this);

	// ajax process
	$.ajax({
		url: SERVICE_URI,
		method:"POST",
		data: {mode: "addPlan", task_description:$("#task_description").val()},
		dataType:"html",
		success: eventSuccess,
		error: function(xhr, status, error) {alert(error);}
	});

	function eventSuccess(data)
	{
		//alert(data);
        reset();
        redrawPageContents();
	}

	function reset()
	{
		form.each(function() {  
            this.reset();  
         });
	}
}
/**
 * 클릭시에 바로 인스턴스로 모달 창을 생성해서 화면에 그려준다.
 */
function confirmModal(heading, question, cancelButtonTxt, okButtonTxt, callback) {
	$("div.modal").remove();
	var confirmModal = 
		$('<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">' +        
	        '<div class="modal-dialog modal-sm">' +
	        '<div class="modal-content">' +
	        '<div class="modal-header">' +
			'<button type="button" class="close" data-dismiss="modal" aria-label="Close">'+
			'<span aria-hidden="true">&times;</span>'+
			'</button>' +
			'<h3 class="modal-title">' + heading +'</h3>' +
	        '</div>' +

	        '<div class="modal-body">' +
			'<p>' + question + '</p>' +
	        '</div>' +

			'<div class="modal-footer">' +
			'<button type="button" class="btn btn-primary" id="okButton">' + 
				okButtonTxt + 
			'</button>' +
			'<button type="button" class="btn btn-default" data-dismiss="modal">' + 
				cancelButtonTxt + 
			'</button>' +
	        '</div>' +
	        '</div>' +
	        '</div>' +
	      '</div>');
  
	confirmModal.find('#okButton').click(function(event) {
		confirmModal.modal('hide');
		callback();
	});
	confirmModal.modal('show');    
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

</script>
<div class="container mt-5">
	<div class="mb-5">
		<h3>InBOX</h3>
		<form id="form-task" action="" onsubmit="return false;">
			<div class="input-group">
				<input type="text" id="task_description" name="task_description"
				class="form-control" placeholder="할 일을 입력(255자 내외)">
				<span class="input-group-btn">
					<button class="btn btn-secondary" type="submit">입력</button>
				</span>
			</div>
		</form>
		<div class="card">
			<div class="card-block">
				<table class="table table-sm table-hover table-striped"
					id="task-inbox">
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
	</div>
	<div class="mb-5">
		<h3>24시간 이내 완료 목록</h3>
		<div class="card">
			<div class="card-block">
				<table class="table table-sm table-hover" id="task-todayComplete">
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
	</div>
	<div class="mb-5 pb-5">
		<h3>해야할 일</h3>
	</div>
	<div class="mb-5 pb-5">
		<h3>해두면 좋은 일</h3>
	</div>
	<div class="mb-5 pb-5">
		<h3>안 해도 되는 일</h3>
	</div>
</div>
@stop
