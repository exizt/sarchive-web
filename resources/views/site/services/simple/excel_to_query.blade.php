@extends('layouts.service')
@section('content')
<script>
var textInput = "#input";
var ck_include_header = "#ck_include_header";//헤더 포함 체크박스ID
var ck_reverse = "#ck_reverse";
var result;
$(document).ready(function(){
	onChangeEventListener();
	onClickButtonEventListener();
});
/**
* on change
*/
function onChangeEventListener()
{
	$(textInput).on("input",convertString);
	$("#table_name").on("input",convertString);
}
function onClickButtonEventListener()
{
	$("#btn_copyresult").on("click",function(e){
		e.preventDefault();
		copyToClipboard(result);
	});
}
/**
* convert string
*/
function convertString()
{
	result = encodeString($(textInput).val());
	$("#output").text(result);
	
	function encodeString(data)
	{
		var result = "";
		var list = data.split(/\n/);//줄 단위로 분리
		result += "-- Insert 쿼리 생성\n";

		var prefix = "insert into "+$("#table_name").val() + " (";
		var cols = "";
		for(var k in list)
		{
			var item = list[k];

			// 빈 칸 처리
			if(item.replace(/(^\s*)|(\s*$)/gi, "")==""){
				continue;
			}

			//첫 번째 줄에 대한 처리
			if(k==0){

				cols = prefix;
				cols += item.replace(/\t/g," , ");
				cols += ") values (";
				
			} else {
				item = "'"+ item.replace(/\t/g,"' , '") + "'";

				result += cols;
				result += item;	
				result += ");\n";
			}
		}
		result += "-- 쿼리 완료";
		return result;
	}
}
/**
* source : http://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript
*/
function copyToClipboard(text) {
	var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
	if (window.clipboardData) { // Internet Explorer
		caseIE(text);
	} else if(isFirefox){
		caseFirefox(text);
	} else {
		if(typeof unsafeWindow != 'undefined'){
			caseFirefox(text);
		} else {
			executeCopy(text);
		}
	}
	/*
	* 익스플로러의 경우
	*/
	function caseIE(text)
	{
		if(console) console.log("caseIE");
		window.clipboardData.setData("Text", text);
	}
	/*
	* 파이어폭스의 경우
	*/
	function caseFirefox(text)
	{
		if(console) console.log("caseFirefox");
		if(typeof unsafeWindow != 'undefined'){
			unsafeWindow.netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  
		} else {
			if(console) console.log("caseFirefox error");
		}
		const clipboardHelper = Components.classes["@mozilla.org/widget/clipboardhelper;1"].getService(Components.interfaces.nsIClipboardHelper);  
		clipboardHelper.copyString(text);
	}
	/*
	* 일반적인 경우
	*/
	function executeCopy(text)
	{
		var input = document.createElement('textarea');
		document.body.appendChild(input);
		input.id="new";
		input.value = text;
		input.focus();
		input.select();
		try{
			document.execCommand('Copy');
		} catch(e) {
			if(console) console.log(e);
		}
		input.remove();
	}
	/*
	* 잘 안 됨.
	*/
	function caseJquery(text)
	{
		$("<textarea></textarea>").attr('id','new').val(text).appendTo('body').focus().select();
		try{
			document.execCommand('Copy');
		} catch(e) {
			if(console) console.log(e);
		}
		$("textarea#new").remove();
	}
}
</script>
<h2>엑셀 -> Insert 쿼리 변환</h2>
<div class="was-validated">
	<div style="padding-bottom:30px;">
		<p>입력 (엑셀에서 복사 후 붙여넣기)</p>
		<div class="form-group">
			<label for="table_name">테이블 이름</label> <input type="text"
				name="table_name" id="table_name" class="form-control" size="20"
				placeholder="테이블 이름" autofocus>
		</div>

		<div class="form-group">
			<label for="input">입력 (엑셀에서 복사 후 붙여넣기)</label>
			<textarea class="form-control" rows="14" cols="100" id="input"
				placeholder="엑셀에서 영역지정 복사 후 붙여넣기 합니다"></textarea>
		</div>
	</div>
	<h4>처리 결과</h4>
	<div class="card">
		<div class="card-body">
			<pre id="output"></pre>
		</div>
	</div>
	<br>
	<button id="btn_copyresult" class="btn btn-primary" style="cursor:pointer">Copy</button>
</div>

@stop