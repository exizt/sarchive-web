@extends('layouts.calculator')

@section('title','평형 변환기')

@section('content')
<script src="/assets/lib/jquery-plugins/jquery.number.min.js"></script>
<script>
$(document).ready(function(){
	//--숫자포멧 지정
	$("#pyeong").number(false);
	$("#squareMeter").number(false);

	//--이벤트 연결
	$("#pyeong").on("input",function(){
		$("#squareMeter").val(0);
		handleAction();
	});
	$("#squareMeter").on("input",function(){
		$("#pyeong").val(0);
		handleAction();
	});
	
	//$("#pyeong").focus();
});


/**
 * 연산 을 호출하는 메서드
 * 
 */
function handleAction(status){
	if (typeof(status)==='undefined') status = true;
	
	//--inputValue
	var _pyeong = parseInt($("#pyeong").val());//평
	var _squareMeter = parseFloat($("#squareMeter").val());//평방미터
	
	if(isNaN(_pyeong)) _pyeong = 0;
	if(isNaN(_squareMeter)) _squareMeter = 0;

	$.ajax({
		url: SERVICE_URI
		,type: "post"
		,data : {mode:"run",pyeong:_pyeong,squareMeter:_squareMeter}
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
			$("#resultPyeong").text(dataSet.pyeong);
			$("#resultSquareMeter").text(dataSet.squareMeter);
		}
	}
	handleErrorMessage("");
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

</script>
<h1>평형 변환기</h1>
<br>
<div class="card">
	<div class="card-body">
		<div class="form-group">
			<label class="" for="pyeong">평</label>
			<div class="input-group">
				<input type="number" name="principal" id="pyeong" value=""
					class="form-control number" placeholder="평수를 입력하세요." autofocus>
				<div class="input-group-append">
                  <span class="input-group-text">평</span>
                </div>
			</div>
		</div>
		<div class="form-group">
			<label class="" for="squareMeter">㎡</label>
			<div class="input-group">
				<input type="number" name="interestRate" id="squareMeter" value=""
					class="form-control" placeholder="평방미터 를 입력하세요">
				<div class="input-group-append">
                  <span class="input-group-text">㎡</span>
                </div>					
			</div>
		</div>
	</div>
</div>
<div class="my-3" id="errorMessage"></div>
<div class="bs-callout bs-callout-primary">
	<h4>About 평형 변환기</h4>
	<p>'평' 과 '㎡' 를 변환 연산 합니다. </p>
</div>
<div class="card">
	<h5 class="card-header">결과</h5>
	<div class="card-body">
		<ul class="list-group">
			<li class="list-group-item"><p class="mb-1 w-100">평수</p> <small class="text-muted"><span
					id="resultPyeong">&nbsp;</span></small>
			</li>
			<li class="list-group-item"><p class="mb-1 w-100">㎡</p> <small class="text-muted"><span
					id="resultSquareMeter">&nbsp;</span></small>
			</li>			
		</ul>
	</div>
</div>
@stop