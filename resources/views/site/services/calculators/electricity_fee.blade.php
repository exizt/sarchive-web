@extends('layouts.calculator')

@section('title','전기세 계산기')

@section('content')
<script src="/assets/lib/jquery-plugins/jquery.number.min.js"></script>
<script>
$(document).ready(function(){
	// 숫자포멧 입력 지정
	$("#e_usage").number(false);

	// 바인딩
	$("#e_usage").on("input",handleAction);

});

/**
 * 연산
 */
function handleAction(){
	handleErrorMessage("");
	var usage = $("#e_usage").val();
	var taxBasic = calcBasicTax(usage);
	var taxUsage = calcUsageTax(usage);
	var taxBasicUsage = taxBasic + taxUsage;

	var vat = calcVAT(taxBasicUsage);
	var taxOption1 = calcTaxOption1(taxBasicUsage);
	
	//10원 미만 절사
	var taxTotal = rounddown(taxBasicUsage + vat + taxOption1,2);

	// 결과 대입
	$("#taxBasic").text(getNumberString(taxBasic)+" 원");
	$("#taxUsage").text(getNumberString(taxUsage)+" 원");
	$("#taxBasicUsage").text(getNumberString(taxBasicUsage)+" 원");
	$("#vat").text(getNumberString(vat) + " 원");
	$("#taxOption1").text(getNumberString(taxOption1) + " 원");
	$("#taxTotal").text(getNumberString(taxTotal) + " 원");
}

/**
* 전력산업기반기금
* 단위 : 10원미만 절사
*/
function calcTaxOption1(amount)
{
	return rounddown(amount * 0.037,2);
}

/**
* VAT 부가가치세 계산
* 단위 : 원미만 4사 5입(즉, 반올림)
*/
function calcVAT(amount)
{
	return Math.round(amount * 0.1);
}
/**
* 기본요금 계산
* 특징 : 원미만 절사
* 계산방법 : 총사용량에 따라 기본요금이 정해진다.
*/
function calcBasicTax(amount)
{
	var section = [
		[410,0,100],
		[910,101,200],
		[1600,201,300],
		[3850,301,400],
		[7300,401,500],
		[12940,501,0],
	];
	var x = amount;
	var y = 0;
	for(var i in section){
		var j = section.length -1 -i;
		if(x >= section[j][1]){
			y = section[j][0];
			break;
		}
	}
	var ret = y;
	return ret;
}
/**
* 전력량 요금 계산
* 특징 : 원미만 절사
* 계산방법 : 누적 계산
*/
function calcUsageTax(amount)
{
	/**
	* 구간별 계산 입니다.
	* [0] 곱할 금액
	* [1] 구간(초과)
	* [2] 구간(이하)
	*/
	var section = [
		[60.7,0,100],
		[125.9,100,200],
		[187.9,200,300],
		[280.6,300,400],
		[417.7,400,500],
		[709.5,500,0],
	];
	var x = amount;
	var y = 0;

	for(var i in section){
		var j = section.length -1 -i;
		if(x >= section[j][1]){
			y += (x-section[j][1]) * section[j][0];
			x = section[j][1];
		}
	}

	var ret = rounddown(y,1);
	return ret;
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
/**
* deprecated
* 금액의 세자리 마다 , 추가
* jquery number 플러그인이 동작이 잘 안 되서 추가함.
* (아이패드, 아이폰에서 해당 플러그인이 잘 안 됨. 나중에 업데이트 되면
* $.number(인수) 로 대체하면 된다.
*/
function numFormat(value)
{
	var number = parseFloat(value);
	if(isNaN(value)) return "0";
	if(number==0) return "0";

	var reg = /(^[+-]?\d+)(\d{3})/;
	var n = (number + '');
	while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');
	return n;
}

/**
* 절삭 함수
* 원단위 절삭 rounddown(num,1)
* 십원단위 절삭 rounddown(num,2)
*/
function rounddown(num,point)
{
	var number = parseFloat(num);
	var p10 = 1;
	if(point > 1){
		var p10 = (point-1) * 10;
	}
	return Math.floor(number/p10)*p10;
}
</script>
<h1>전기세 사용량 -> 요금 계산</h1>
<br>
<div class="card">
	<div class="card-body">
		<div class="form-group">
			<label class="" for="e_usage">사용량</label>
			<div class="input-group">
				<input name="e_usage" id="e_usage" type="number" value="0"
					class="form-control" aria-describedby="basic-addon1"
					placeholder="전기세 사용량을 입력하세요(기본값 0)" autofocus>
				<div class="input-group-append">
                  <span class="input-group-text">kWh</span>
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
		<ul class="list-group">
			<li class="list-group-item">
				<p class="mb-1 w-100">기본 요금</p> <small class="text-muted"><span
					id="taxBasic">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">전력량별 요금</p> <small class="text-muted"><span
					id="taxUsage">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">전기요금계(기본요금+전력량요금)</p> <small
				class="text-muted"><span id="taxBasicUsage">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">부가가치세</p> <small class="text-muted"><span
					id="vat">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">전력산업기반기금</p> <small class="text-muted"><span
					id="taxOption1">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">청구금액</p> <small class="text-muted"><span
					id="taxTotal">&nbsp;</span></small>
			</li>
		</ul>
	</div>
</div>
@stop