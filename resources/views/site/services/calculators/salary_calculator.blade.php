@extends('layouts.calculator')

@section('title','연봉 실수령액 계산기')


@section('content')
<script src="/assets/lib/jquery-plugins/jquery.number.min.js"></script>
<script>
$(document).ready(function(){
	//--숫자포멧 지정
	$("#edMoney").number(true);
	$("#edOptionTaxFree").number(true);
	$("#edOptionFamily").number(true);
	$("#edOptionChild").number(true);

	//--Change Option
	$("input[name=moneyType]").on("change",function(){
		var salary_type = $(this).val();
		if(salary_type=="yearly"){
			$("#moneyLabel").text("연봉");
			$("#yearlyOption1").show();
		} else {
			$("#moneyLabel").text("월급");
			$("#yearlyOption1").hide();
		}
		//handleAction(false);
	});

	//$("input[name=yearlyOpSeverance]").on("change",function(){
		//handleAction(false);
	//});

	//바인딩
	//$("#edMoney").on("input",handleAction);
	//$("#edOptionFamily").on("input",handleAction);
	//$("#edOptionChild").on("input",handleAction);
	//$("#edOptionTaxFree").on("input",handleAction);	

	//툴팁
	$('[data-toggle="tooltip"]').tooltip()

	initDescriptionModal();

	$(".sh-event-valueswitch").on("click",handleValueSwitch).css("cursor","pointer");

	$("#doCalculate").on("click",handleAction);
});

function parseNonNegativeInteger(_val)
{
	var val = parseInt(_val);
	val = isNaN(val) ? 0 : val;
	return (val <0) ? 0 : val;	
}
function parsePositiveInteger(_val)
{
	var val = parseInt(_val);
	val = isNaN(val) ? 1 : val;
	return (val <0) ? 1 : val;
}

/**
 * 연산
 */
function handleAction(status){
	if (typeof(status)==='undefined') status = true;

	//--inputValue
	var money = parseNonNegativeInteger($("#edMoney").val());//입력값
	var family = parsePositiveInteger($("#edOptionFamily").val());//부양가족수
	var child = parseNonNegativeInteger($("#edOptionChild").val());//20세이하자녀수
	var taxFree = parseNonNegativeInteger($("#edOptionTaxFree").val());//비과세액
	var annualBasis = false; // 입력값이 연봉인지 월급인지 여부. (true : 연봉 / fale : 월급)
	var includedSeverance = false; // 연봉값일 때, 퇴직금 포함인지 여부. (true : 포함 / false : 비포함 (기본값))

	if(isNaN(money)){
		if(status) handleErrorMessage("금액을 제대로 입력해주세요.");
		return false;
	}

	if(money<0){
		if(status) handleErrorMessage("금액을 제대로 입력해주세요.");
		return false;
	};

	if(money<100000)
	{
		return false;
	}

	
	var tSalary = 0;
	if($(":radio[name=moneyType]:checked").val()=="yearly"){
		annualBasis = true;

		tSalary = parseInt(money/12);
		if($(":radio[name=yearlyOpSeverance]:checked").val()=="Y"){
			includedSeverance = true;
			tSalary = parseInt(money/13);
		}
	} else {
		annualBasis = false;
		tSalary = money;
	}

	if(money < 1000000) return false;

	
	/*
	var baseSalary = 0;
	var baseSalaryY = 0;
	var viewSalary = 0;// 통상적 월급
	var viewSalaryY = 0;// 통상적 월급

	if(moneyType=="yearly"){
		var month = 12;
		var yOpSeverance = $(":radio[name=yearlyOpSeverance]:checked").val();
		if(yOpSeverance=="Y"){
			month = 13;
		}
		viewSalary = money/month;
	} else {
		viewSalary = money;
	}
	viewSalaryY = viewSalary * 12;
	baseSalary = viewSalary - taxFree;
	baseSalaryY = baseSalary * 12;
	*/
	


	//var salary = viewSalary;
	//--연산 호출
	$.ajax({
		url: SERVICE_URI
		,type: "post"
		,data : {
			mode: "run",
			inputMoney: money,
			taxExemption: taxFree,
			family: family,
			child: child,
			annualBasis: annualBasis,
			includedSeverance: includedSeverance
		}
		,dataType: "json"
		,success: appendResult
		,error: function(response, status, error) { 
			if (response.status == 422){
                displayFieldErrors(response);
            }else{
            	alert(error); 
            }
		}
	});

	function displayFieldErrors(response){
	    var responseJSON = response.responseJSON;
	    $.each(responseJSON.errors, function (key, item) {
			handleErrorMessage(item);
	    });
	    return;
	}
	/**
	* 결과 처리
	*/
	function appendResult(data)
	{
		//--요약정보
		$("#report_summary_annualsalary").text(getNumberString(data.summary_annualSalary)+" 원");//계산된연봉
		$("#report_summary_salary").text(getNumberString(data.summary_salary)+" 원");//계산된월급
		$("#report_summary_netsalary").text(getNumberString(data.netSalary) + " 원");//실수령액
		$("#report_summary_minus").text(getNumberString(parseInt(data.summary_insurance) + parseInt(data.summary_incomeTax)) + " 원");//차감될 금액합계
		
		$("#report_summary_taxExemption").text(getNumberString(data.summary_taxExemption) + " 원");//비과세액
		
		//--4대보험
		$("#insurance_nat").text(getNumberString(data.insurance_nationalPension) + " 원");
		$("#insurance_emp").text(getNumberString(data.insurance_employmentCare) + " 원");
		$("#insurance_hel").text(getNumberString(data.insurance_healthCare) + " 원");
		$("#insurance_rec").text(getNumberString(data.insurance_longTermCare) + " 원");

		//--소득세 주민세
		$("#tax_earned").text(getNumberString(data.tax_earned) + " 원");
		$("#tax_local").text(getNumberString(data.tax_local) + " 원");
	}
	handleErrorMessage("");
}
/* 값을 편하게 입력 */
function handleValueSwitch(event)
{
	event.preventDefault();
	
	var sel = $(this).attr("data-valuefor");
	var value = $(this).attr("data-valuecontrol");
	if(value=="0"){
		value="0";
		if($(this).attr("data-valuecontrol-default")){
			value = $(this).attr("data-valuecontrol-default");
		}
	} else {
		var bef_val = parseFloat($(sel).val()) || 0;
		value = parseFloat(bef_val) + parseFloat(value);
		// 최소값보다 작을 경우 보정
		var min = $(sel).attr("min");
		if (typeof attr !== typeof undefined && attr !== false) {
			min = 0;
		}
		if(value < min) value = min;
	}
	$(sel).val(value);
	//handleAction();
}

/**
 * 설명
 */
function initDescriptionModal(){
	$('#myModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget)
		var modal = $(this)
		var contentid = button.data('contentid')

		//ajax 
		$.ajax({
			url: SERVICE_URI
			,type: "POST"
			,data:{mode:"ajax_description",dataType : "xml",cid : contentid}
			,dataType: "json"
			,success: appendResult
			, error: function(xhr, status, error) { alert(error); }
		});
		function appendResult(data){
			var name = data.name;
			var description = (data.description==null)?'':data.description
			var process = (data.process==null)?'':data.process
			var history = (data.history==null)?'':data.history
			//modal.find('.modal-title').text(name.replace(/\n/gi,"<br>"));
			//modal.find('.modal-body').find('.modal-content-explain').html(description.replace(/\n/gi,"<br>"));
			//modal.find('.modal-body').find('.modal-content-process').html(process.replace(/\n/gi,"<br>"));
			//modal.find('.modal-body').find('.modal-content-history').html(history.replace(/\n/gi,"<br>"));
			modal.find('.modal-title').text(name);
			modal.find('.modal-body').find('.modal-content-explain').html(description);
			modal.find('.modal-body').find('.modal-content-process').html(process);
			modal.find('.modal-body').find('.modal-content-history').html(history);
		}
	})
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


/**
* 금액을 한글표기명으로 변경
*/
function changeKor(num) {
	//Console("입력"+num);

	if(parseInt(num)==0) return "영";
	if(typeof num == "number"){ num = num.toString();}
	var nums = new Array();
	nums = num.split('');

	var hanA = new Array("","일","이","삼","사","오","육","칠","팔","구","십");
	var danA = new Array("","십","백","천","","십","백","천","","십","백","천");
	var result = "";  

	for(i=0; i < nums.length; i++) {  
		str = "";
		han = hanA[nums[nums.length-(i+1)]];  
		if(han != "") str = han+danA[i];  
		if(i == 4) str += "만 ";  
		if(i == 8) str += "억 ";  
		result = str + result;  
	}

	//Console("결과"+result);
	return result;  
}

/**
* 절삭 함수
* 원단위 절삭 rounddown(num,1)
* 십원단위 절삭 rounddown(num,2)
*/
function rounddown(num,d)
{
	var number = parseFloat(num);
	var dec = Math.pow(10,d);
	return Math.sign(number) * Math.floor(Math.abs(number)/dec)*dec;
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
	$("#errorMessage").append(html);
}

</script>
<style>
label {
	padding-right: .5rem;
}

.descriptions {
	padding-top: 30px;
}

.taxinfo-sign {
	color: rgb(100, 100, 120);
	padding-left: 3px;
}
.sh-valueswitch-badge-wrap {
	
}
.sh-valueswitch-badge-wrap a{
	font-size: 0.95em !important;
}
</style>
<div class='backLayer' style="display: none;">&nbsp;</div>
<h1>실수령액 연봉 계산기</h1>
<div class="pt-3">@include('layouts.modules.adsense')</div>
<br>
<div class="my-3" id="errorMessage"></div>
<div class="card">
	<div class="card-body">
		<div class="form-group">
			<label for="moneyYearly">입력 방식</label>
			<div class="custom-control custom-radio custom-control-inline">
              <input type="radio" class="custom-control-input" name="moneyType" id="moneyYearly" value="yearly" checked>
              <label class="custom-control-label" for="moneyYearly">연봉 입력</label>
            </div>
            
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" class="custom-control-input" name="moneyType" id="moneyMonthly" value="monthly">
              <label class="custom-control-label" for="moneyMonthly">월급 입력</label>
            </div>
		</div>

		<div class="form-group">
			<label class="" for="edMoney" id="moneyLabel">연봉</label>
			<div class="input-group">
				<input name="edMoney" id="edMoney" value="0" class="form-control" min="0"
					aria-describedby="basic-addon1" type="text" min="0"
					pattern="[0-9]*" placeholder="연봉을 입력하세요" autofocus>
				<div class="input-group-append">
                  <span class="input-group-text">원</span>
                </div>
			</div>
			<div class="pt-1 sh-valueswitch-badge-wrap">
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="0" class="badge badge-info badge-pill sh-event-valueswitch">초기화</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="10000000" class="badge badge-secondary badge-pill sh-event-valueswitch">+천만</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="1000000" class="badge badge-secondary badge-pill sh-event-valueswitch">+백만</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="100000" class="badge badge-secondary badge-pill sh-event-valueswitch">+십만</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="10000" class="badge badge-secondary badge-pill sh-event-valueswitch">+만</a>&nbsp;<br class="d-sm-none">
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="-10000000" class="badge badge-light badge-pill sh-event-valueswitch">-천만</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="-1000000" class="badge badge-light badge-pill sh-event-valueswitch">-백만</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="-100000" class="badge badge-light badge-pill sh-event-valueswitch">-십만</a>&nbsp;
				<a href="#" data-valuefor="#edMoney" data-valuecontrol="-10000" class="badge badge-light badge-pill sh-event-valueswitch">-만</a>&nbsp;
			</div>
		</div>

		<!-- <span id="edMoney_kor" style="display: none;"></span> -->
		<div class="form-group">
			<label class="" for="edOptionFamily">부양가족수(자신 포함)</label><i
				class="fa fa-question" aria-hidden="true" data-toggle="tooltip"
				data-placement="right" title="자신을 포함한 가족의 수 입니다. (최소 1명)"></i>
			<div class="input-group">
				<input name="edOptionFamily" id="edOptionFamily" value="1" type="number" min="1" max="11"
					class="form-control" aria-describedby="basic-addon1"
					min="0" pattern="[0-9]+" placeholder="부양가족수를 입력하세요(기본값 1)">
				<div class="input-group-append">
                  <span class="input-group-text">명</span>
                </div>
			</div>
			<div class="pt-1 sh-valueswitch-badge-wrap">
				<a href="#" data-valuefor="#edOptionFamily" data-valuecontrol="0" data-valuecontrol-default="1" class="badge badge-info badge-pill sh-event-valueswitch">기본값(1)</a>&nbsp;
				<a href="#" data-valuefor="#edOptionFamily" data-valuecontrol="1" class="badge badge-secondary badge-pill sh-event-valueswitch">+1</a>
				<a href="#" data-valuefor="#edOptionFamily" data-valuecontrol="-1" class="badge badge-light badge-pill sh-event-valueswitch">-1</a>
			</div>
		</div>

		<div class="form-group">
			<label class="" for="edOptionChild">20세이하자녀수</label><i
				class="fa fa-question" aria-hidden="true" data-toggle="tooltip"
				data-placement="right" title="20세 이하 자녀수에 따라 비과세 혜택이 있을 수 있습니다."></i>
			<div class="input-group">
				<input name="edOptionChild" id="edOptionChild" value="0" type="number" min="0" max="20"
					class="form-control" aria-describedby="basic-addon1"
					min="0" pattern="[0-9]*" placeholder="20세 이하 자녀수를 입력하세요(기본값 0)">
				<div class="input-group-append">
                  <span class="input-group-text">명</span>
                </div>
			</div>
			<div class="pt-1 sh-valueswitch-badge-wrap">
				<a href="#" data-valuefor="#edOptionChild" data-valuecontrol="0" class="badge badge-info badge-pill sh-event-valueswitch">기본값(0)</a>&nbsp;
				<a href="#" data-valuefor="#edOptionChild" data-valuecontrol="1" class="badge badge-secondary badge-pill sh-event-valueswitch">+1</a>
				<a href="#" data-valuefor="#edOptionChild" data-valuecontrol="-1" class="badge badge-light badge-pill sh-event-valueswitch">-1</a>
			</div>
		</div>

		<div class="form-group">
			<label class="" for="edOptionTaxFree">세금제외</label><i
				class="fa fa-question" aria-hidden="true" data-toggle="tooltip"
				data-placement="right"
				title="봉급 중 세금이 물리지 않을 금액(클수록 좋은 개념). '비과세액' 이라고도 부름. 식대, 야근수당 등이 해당됨."></i>
			<div class="input-group">
				<input name="edOptionTaxFree" id="edOptionTaxFree" value="100000"
					class="form-control" aria-describedby="basic-addon1" type="text"
					min="0" pattern="[0-9]*" placeholder="비과세액을 입력하세요(기본값 10만원)">
				<div class="input-group-append">
                  <span class="input-group-text">원</span>
                </div>
			</div>
			<div class="pt-1 sh-valueswitch-badge-wrap">
				<a href="#" data-valuefor="#edOptionTaxFree" data-valuecontrol="0" class="badge badge-info badge-pill sh-event-valueswitch">기본값(십만)</a>&nbsp;
				<a href="#" data-valuefor="#edOptionTaxFree" data-valuecontrol="100000" class="badge badge-secondary badge-pill sh-event-valueswitch">+십만</a>&nbsp;
				<a href="#" data-valuefor="#edOptionTaxFree" data-valuecontrol="10000" class="badge badge-secondary badge-pill sh-event-valueswitch">+만</a>&nbsp;
				<a href="#" data-valuefor="#edOptionTaxFree" data-valuecontrol="-100000" class="badge badge-light badge-pill sh-event-valueswitch">-십만</a>&nbsp;
				<a href="#" data-valuefor="#edOptionTaxFree" data-valuecontrol="-10000" class="badge badge-light badge-pill sh-event-valueswitch">-만</a>&nbsp;
			</div>
		</div>

		<div class="form-group" id="yearlyOption1">
			<label for="yearlyOpSeverance_N">퇴직금</label><i class="fa fa-question"
				aria-hidden="true" data-toggle="tooltip" data-placement="right"
				title="가끔 연봉에 퇴직금이 포함되어있다고 할 때가 있습니다. 그럴 경우 의 옵션입니다. 언급이 없다면 포함되지 않았다는 뜻입니다."></i>
			<div class="custom-control custom-radio custom-control-inline">
              <input type="radio" class="custom-control-input" name="yearlyOpSeverance" id="yearlyOpSeverance_N" value="N" checked>
              <label class="custom-control-label" for="yearlyOpSeverance_N">비포함(12개월)</label>
            </div>
 			<div class="custom-control custom-radio custom-control-inline">
              <input type="radio" class="custom-control-input" name="yearlyOpSeverance" id="yearlyOpSeverance_Y" value="Y">
              <label class="custom-control-label" for="yearlyOpSeverance_Y">포함(13개월)</label>
            </div>           
		</div>
	</div>
</div>

<div class="py-1">
	<button type="button" class="btn btn-secondary btn-lg btn-block" id="doCalculate">계산하기</button>
</div>

<div class="card">
	<!-- Tab panes -->
	<div class="card-body">
		<div class="mb-5">
			<h3>간단 요약</h3>
			<ul class="list-group">
				<li class="list-group-item">
					<p class="mb-1 w-100">실수령액(월)</p> <small class="text-muted"><span
						id="report_summary_netsalary">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">보험+세금</p> <small class="text-muted"><span
						id="report_summary_minus">0 원</span></small>
				</li>
			</ul>
		</div>
		
		<div class="my-5">
			<h3>4대보험</h3>
			<ul class="list-group">
				<li class="list-group-item">
					<p class="mb-1 w-100">
						국민연금&nbsp;
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#myModal" data-contentid="national_pension">정보보기</button>
					</p> <small class="text-muted"><span id="insurance_nat">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">
						건강보험&nbsp;
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#myModal" data-contentid="health_care">정보보기</button>
					</p> <small class="text-muted"><span id="insurance_hel">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">
						장기요양
							&nbsp;
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#myModal" data-contentid="long_term_care">정보보기</button>
					</p> <small class="text-muted"><span id="insurance_rec">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">
						고용보험
							&nbsp;
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#myModal" data-contentid="employment_care">정보보기</button>
					</p> <small class="text-muted"><span id="insurance_emp">0 원</span></small>
				</li>
			</ul>
		</div>
		
		<div class="my-5">
			<h3>세금</h3>
			<ul class="list-group">
				<li class="list-group-item">
					<p class="mb-1 w-100">
						소득세
							&nbsp;
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#myModal" data-contentid="income_tax">정보보기</button>
					</p> <small class="text-muted"><span id="tax_earned">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">
						주민세
							&nbsp;
							<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
							data-target="#myModal" data-contentid="income_local_tax">정보보기</button>
					</p> <small class="text-muted"><span id="tax_local">0 원</span></small>
				</li>
			</ul>
		</div>
		<div class="my-5">
			<h3>입력값</h3>
			<ul class="list-group">
				<li class="list-group-item">
					<p class="mb-1 w-100">계산된연봉</p> <small class="text-muted"><span
						id="report_summary_annualsalary">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">계산된월급</p> <small class="text-muted"><span
						id="report_summary_salary">0 원</span></small>
				</li>
				<li class="list-group-item">
					<p class="mb-1 w-100">입력한비과세액</p> <small class="text-muted"><span
						id="report_summary_taxExemption">0 원</span></small>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="bs-callout bs-callout-primary">
	<h4>About 실수령액 계산기</h4>
	<p>실수령액을 계산해보는 계산기 입니다. 연봉 또는 월급 입력 후 실수령액과 세금 등을 계산해봅니다. </p>
	<p>참고<br>
		* 세율 변화로 오차가 생길 수 있습니다.<br>* 공제금액까지 전부 계산하므로, 공식 계산법이 변경된 경우 오차가 생길 수
		있습니다.
	</p>
</div>

<script>
// input type=number 인 것에 한해서, empty 값 방지.
const numInputs = document.querySelectorAll('input[type=number]')
numInputs.forEach(function (input) {
  input.addEventListener('change', function (e) {
    if (e.target.value == '') {
      e.target.value = 0
    }
  })
})

</script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
	aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<h5 class="">간략 설명</h5>
				<div class="modal-content-explain" style="white-space:pre-wrap"></div>
				<h5 class="mt-5">계산 방법</h5>
				<div class="modal-content-process" style="white-space:pre-wrap"></div>
				<h5 class="mt-5">세율 기록</h5>
				<div class="modal-content-history" style="white-space:pre-wrap"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
			</div>
		</div>
	</div>
</div>
@stop