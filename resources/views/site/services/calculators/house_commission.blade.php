@extends('layouts.calculator')

@section('title','부동산 중개 수수료 계산기')

@section('content')
<script src="/assets/lib/jquery-plugins/jquery.number.min.js"></script>
<script>
$(document).ready(function(){
	// 숫자 지정
	$("#deposit").number(true);
	$("#monthly_fee").number(true);
	
	// 바인딩
	$("#execute").on("click",handleAction);
	$("#deposit").on("input",handleAction);
	$("#monthly_fee").on("input",handleAction);	
});

/**
 * 연산
 */
function handleAction(status){
	if (typeof(status)==='undefined') status = true;
	
	//--inputValue
	var _deposit = $("#deposit").val();
	var _monthly_fee = $("#monthly_fee").val();
	
	
	if(_deposit==""){
		if(status) handleErrorMessage("제대로 입력해주세요.");
		return false;
	}

	$.ajax({
		url: SERVICE_URI
		,type: "post"
		,data : {mode:"run",deposit:_deposit,monthly_fee:_monthly_fee}
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
			var dataSet = data.dataSet;
			$("#result_commissionTotal").text(getNumberString(dataSet.commissionTotal));
			$("#result_commission").text(getNumberString(dataSet.commission));
			$("#result_VAT").text(getNumberString(dataSet.VAT));
		}
	}
	
	handleErrorMessage("");
}

/**
* 에러메시지 처리
*/
function handleErrorMessage(msg)
{
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
* jquery number 확장 함수
* 금액의 세자리 마다 , 추가
* jquery number 플러그인 보완. (모바일 대응)
* @author e2xist
*/
function getNumberString(number)
{
	return $.number(number,0,'.',',');
}
</script>
<h1>부동산 중개수수료 계산기</h1>
<br>
<div class="card">
	<div class="card-body">
		<div class="form-group">
			<label class="" for="typeOfPayment">거래 유형</label> <select
				class="form-control" id="typeOfPayment">
				<!-- <option value="evenPayment">매매/교환</option>
			<option value="evenPrincipal">전세(임대차)</option> -->
				<option value="monthly">월세(임대차)</option>
			</select>
		</div>
		<div class="form-group">
			<label class="" for="deposit">월세보증금</label>
			<div class="input-group">
				<input name="deposit" id="deposit" value="0" class="form-control"
					type="text" placeholder="보증금을 입력하세요" autofocus>
				<div class="input-group-append">
                  <span class="input-group-text">원</span>
                </div>
			</div>
		</div>
		<div class="form-group">
			<label class="" for="monthly_fee">월세액</label>
			<div class="input-group">
				<input name="monthly_fee" id="monthly_fee" value="0"
					class="form-control" placeholder="월세 액을 입력하세요" type="text">
				<div class="input-group-append">
                  <span class="input-group-text">원</span>
                </div>
			</div>
		</div>
	</div>
</div>
<div class="my-3" id="errorMessage"></div>
<div class="bs-callout bs-callout-primary">
	<h4>주의점</h4>
	<p>* 실제 결과와 다를 수 있습니다.</p>
</div>
<div class="card">
	<h5 class="card-header">결과</h5>
	<div class="card-body">
		<h5>계</h5>
		<ul class="list-group">
			<li class="list-group-item">
				<p class="mb-1 w-100">
					<b>중개수수료 (합계)</b>
				</p> <small class="text-muted"><span id="result_commissionTotal">&nbsp;</span></small>
			</li>
		</ul>
		<br>

		<h5>상세 내역</h5>
		<ul class="list-group">
			<li class="list-group-item">
				<p class="mb-1 w-100">수수료기준액</p> <small class="text-muted"><span
					id="result_commission">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">부가세</p> <small class="text-muted"><span
					id="result_VAT">&nbsp;</span></small>
			</li>
		</ul>
	</div>
</div>
@stop