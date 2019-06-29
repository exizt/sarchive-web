@extends('layouts.calculator')

@section('title','대출 이자 계산기')

@section('content')
<script src="/assets/lib/jquery-plugins/jquery.number.min.js"></script>
<script>
$(document).ready(function(){
	// 숫자 지정
	$("#principal").number(true);
	$("#interestRate").number(false);
	$("#termLoan").number(true);

	// 바인딩
	$("#principal").on("input",handleAction);
	$("#interestRate").on("input",handleAction);
	$("#termLoan").on("input",handleAction);
	$("#typeOfPayment").on("input",handleAction);

	$(".sh-event-valueswitch").on("click",handleValueSwitch).css("cursor","pointer");
});

/**
 * 연산
 */
function handleAction(status){
	if (typeof(status)==='undefined') status = true;
	
	//--inputValue
	var _principal = parseInt($("#principal").val());//할부원금
	var _interestRate = parseFloat($("#interestRate").val());//할부금리
	var _amortizationPeriod = parseInt($("#termLoan").val());//할부기간
	var _typeOfPayment = $("#typeOfPayment").val();
	
	if(isNaN(_principal)){
		if(status) handleErrorMessage("금액을 제대로 입력해주세요.");
		return false;
	}

	$.ajax({
		url: SERVICE_URI
		,type: "POST"
		,data : {mode:"run",principal:_principal,interestRate:_interestRate,amortizationPeriod:_amortizationPeriod,typeOfPayment:_typeOfPayment}
		,dataType: "json"
		,success: appendResult
		,error: function(xhr, status, error) { alert(error); }
	});

	/**
	* 결과 처리
	*/
	function appendResult(data)
	{
		if(data.result){
			initTable();//테이블 내용 초기화
			var schedule = data.schedule;
			var html = "";
			var inData = null;
			var index = "";
			var payment = "";
			var principalPaid = "";
			var interestPaid = "";
			var loanBalance = "";
			for(var i in schedule)
			{
				inData = schedule[i];
				index = inData.index;
				payment = inData.payment;
				principalPaid = inData.principalPaid;
				interestPaid = inData.interestPaid;
				loanBalance = inData.loanBalance;
				html = "<tr>";
				html += "<th scope='row'>"+index+"</th>";
				html += "<td>"+payment+"</td>";
				html += "<td>"+principalPaid+"</td>";
				html += "<td>"+interestPaid+"</td>"
				html += "<td>"+loanBalance+"</td>"
				//html += "<td>-</td>";
				html += "</tr>";
				$("#dataSet").append(html);
			}
		}
	}
	
	handleErrorMessage("");
}

/* 값을 편하게 입력 */
function handleValueSwitch()
{
	var sel = $(this).attr("data-valuefor");
	var value = $(this).attr("data-valuecontrol");
	if(value=="0"){
		value="0";
	} else {
		var bef_val = parseFloat($(sel).val()) || 0;
		value = parseFloat(bef_val) + parseFloat(value);
	}
	$(sel).val(value);
	handleAction();
}

function initTable()
{
	$("#dataSet>tbody").html("");
}


/**
 * 에러메시지 처리
 */
function handleErrorMessage(msg) {
	var html = "";
	if(msg!="")
	{
		html += '<div class="alert alert-danger alert-dismissible" role="alert">';
		html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		html += '<strong>알림!</strong>&nbsp;<span>'+msg+'</span>';
		html += '</div>';
	}
	$("#errorMessage").html(html);
}

/**
* 계산식 테스트
*/
function Test()
{
	Console("=====================");
}
</script>
<style>
.form-group .sh-event-valueswitch{
padding-right:1em;
}
</style>
<h1>대출이자 계산기</h1>
<br>
<div class="card">
	<div class="card-body">
		<div class="form-group">
			<label class="" for="principal">대출원금</label>&nbsp;<br class="d-sm-none">
			<span data-valuefor="#principal" data-valuecontrol="0" class="badge badge-pill badge-info sh-event-valueswitch">초기화</span>&nbsp;
			<span data-valuefor="#principal" data-valuecontrol="1000000" class="badge badge-pill badge-secondary sh-event-valueswitch">+백만</span>
			<span data-valuefor="#principal" data-valuecontrol="10000000" class="badge badge-pill badge-secondary sh-event-valueswitch">+천만</span>
			<span data-valuefor="#principal" data-valuecontrol="100000000" class="badge badge-pill badge-secondary sh-event-valueswitch">+억</span>
			<div class="input-group">
				<input name="principal" id="principal" value="" type="text"
					class="form-control" aria-describedby="basic-addon1"
					placeholder="대출원금을 입력하세요(예시 : 2000000)" autofocus>
				<div class="input-group-append">
                  <span class="input-group-text">원</span>
                </div>
			</div>
		</div>
		<div class="form-group">
			<label class="" for="interestRate">대출금리</label>&nbsp;<br class="d-sm-none">
			<span data-valuefor="#interestRate" data-valuecontrol="0" class="badge badge-pill badge-info sh-event-valueswitch">초기화</span>
			<span data-valuefor="#interestRate" data-valuecontrol="1" class="badge badge-pill badge-secondary sh-event-valueswitch">+1</span>
			<span data-valuefor="#interestRate" data-valuecontrol="0.5" class="badge badge-pill badge-secondary sh-event-valueswitch">+0.5</span>
			<span data-valuefor="#interestRate" data-valuecontrol="0.25" class="badge badge-pill badge-secondary sh-event-valueswitch">+0.25</span>
			<span data-valuefor="#interestRate" data-valuecontrol="0.1" class="badge badge-pill badge-secondary sh-event-valueswitch">+0.1</span>
			<span data-valuefor="#interestRate" data-valuecontrol="0.01" class="badge badge-pill badge-secondary sh-event-valueswitch">+0.01</span>
			<div class="input-group">
				<input name="interestRate" id="interestRate" type="number" value=""
					class="form-control" aria-describedby="basic-addon1"
					placeholder="대출금리을 입력하세요(예시 : 3.5)"> 
				<div class="input-group-append">
                  <span class="input-group-text">%</span>
                </div>
			</div>
		</div>
		<div class="form-group">
			<label class="" for="termLoan">상환기간</label>&nbsp;<br class="d-sm-none">
			<span data-valuefor="#termLoan" data-valuecontrol="0" class="badge badge-pill badge-info sh-event-valueswitch">초기화</span>
			<span data-valuefor="#termLoan" data-valuecontrol="1" class="badge badge-pill badge-secondary sh-event-valueswitch">+1개월</span>
			<span data-valuefor="#termLoan" data-valuecontrol="10" class="badge badge-pill badge-secondary sh-event-valueswitch">+10</span>
			<span data-valuefor="#termLoan" data-valuecontrol="12" class="badge badge-pill badge-secondary sh-event-valueswitch">+12</span>
			<span data-valuefor="#termLoan" data-valuecontrol="60" class="badge badge-pill badge-secondary sh-event-valueswitch">+60</span>
			<span data-valuefor="#termLoan" data-valuecontrol="120" class="badge badge-pill badge-secondary sh-event-valueswitch">+120</span>
			<div class="input-group">
				<input name="termLoan" id="termLoan" value="" type="number"
					class="form-control" aria-describedby="basic-addon1"
					placeholder="상환기간을 입력하세요(예시 : 12 또는 60 또는 240, 최대 360)">
				<div class="input-group-append">
                  <span class="input-group-text">개월</span>
                </div>					
			</div>
		</div>
		<div class="form-group">
			<label class="" for="typeOfPayment">상환방식</label>
			<select class="form-control" id="typeOfPayment">
				<option value="evenPayment">원리금균등분할</option>
				<option value="evenPrincipal">원금균등분할</option>
				<!-- <option value="balloon">원금만기일시상환</option> -->
			</select>
		</div>
	</div>
</div>
<div class="my-3" id="errorMessage"></div>
<div class="bs-callout bs-callout-primary">
	<h4>About 대출이자 계산기</h4>
	<p>대출이자 상환기간 에 따른 잔금 과 이자 에 대한 시나리오 를 예상해 볼 수 있습니다.</p>
	<p>* 원단위 오차나 미세한 오차, 각 대출기관의 미세한 계산법의 차이로 인해서 실제와 다를 수 있습니다.</p>
</div>

<!-- 결과 -->
<div>
	<!--
		<table class="table_main" style="width: 100%;">
		<tr>
		<th colspan="2">간이결과</th>
		</tr>
		<tr>
		<th style="width: 150px;">월평균 상환금</th>
		<td><span id="result_year_normal">&nbsp;</span></td>
		</tr>
		<tr>
		<th>월 납입원금</th>
		<td><span id="result_salary">&nbsp;</span></td>
		</tr>
		<tr>
<th>총이자</th>
<td><span id="result_salary_real">&nbsp;</span></td>
</tr>
</table>
-->
	<table class="table table-striped table-hover" style="width: 100%"
		id="dataSet">
		<thead class="thead-inverse">
			<tr>
				<th>회차</th>
				<th>월불입금</th>
				<th>상환원금</th>
				<th>상환이자</th>
				<th>잔금</th>
				<!-- <th>상환일</th> -->
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>-</td>
				<td>-</td>
				<td>-</td>
				<td>-</td>
				<td>0</td>
				<!-- <td>-</td> -->
			</tr>
		</tbody>
	</table>
</div>
<div>
	<!-- 로그인 한 경우만 받게 끔 처리 -->
	<!-- 
		<a href="/assets/files/원리금균등분할_계산기.xlsx">엑셀 계산기(원리금균등분할) 다운받기</a> <br> <a href="/assets/files/원금균등분할_계산기.xlsx">엑셀
			계산기(원금균등분할) 다운받기</a>
			 -->
</div>
@stop