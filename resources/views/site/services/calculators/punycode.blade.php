@extends('layouts.calculator')

@section('title','Punycode 변환기')

@section('content')
<script src="/assets/lib/jquery-plugins/jquery.number.min.js"></script>
<script>
$(document).ready(function(){
	// 바인딩
	$("#domain").on("input",handleAction);
});

/**
 * 연산
 */
function handleAction(status){
	if (typeof(status)==='undefined') status = true;
	
	//--inputValue
	var _domain = $("#domain").val();
	
	
	if(_domain==""){
		if(status) handleErrorMessage("제대로 입력해주세요.");
		return false;
	}

	$.ajax({
		url: SERVICE_URI
		,type: "post"
		,data : {mode:"run",domain:_domain}
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
			$("#resultDomain").text(dataSet.domain);
			$("#resultPunyCode").text(dataSet.punycode);
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
<h1>퓨니코드 변환기</h1>
<br>
<div class="card">
	<div class="card-body">
		<div class="form-group">
			<label class="" for="domain">도메인</label>
			<div class="input-group">
				<input name="domain" id="domain" value="" class="form-control"
					placeholder="도메인을 입력하세요(예시:가나다.com)" autofocus>
			</div>
		</div>
	</div>
</div>
<div class="my-3" id="errorMessage"></div>
<div class="bs-callout bs-callout-primary">
	<h4>Punycode 란</h4>
	퓨니코드 는 한글 주소로 접속 할 때에 쓰이는 코드 입니다. 호스트 셋팅 할 때나 도메인 을 사용할 때에 활용이 되어집니다.
</div>
<div class="bs-callout bs-callout-primary">
	<h4>사용법</h4>도메인 값을 입력하면 아래 '결과' 부분 에서 변환해서 보여줍니다.
</div>
<div class="card">
	<h5 class="card-header">결과</h5>
	<div class="card-body">
		<ul class="list-group">
			<li class="list-group-item">
				<p class="mb-1 w-100">입력한 도메인</p> <small class="text-muted"><span
					id="resultDomain">&nbsp;</span></small>
			</li>
			<li class="list-group-item">
				<p class="mb-1 w-100">퓨니코드</p> <small class="text-muted"><span
					id="resultPunyCode">&nbsp;</span></small>
			</li>
		</ul>
	</div>
</div>
@stop