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
	$(ck_include_header).on("change",convertString);
	$(ck_reverse).on("change",convertString);
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
	result = "";
	var data = $(textInput).val();
	var isReverse = $(ck_reverse).prop("checked");
	if(isReverse){
		result = decodeString(data);
	} else {
		result = encodeString(data);
	}
	$("#output").text(result);
	function encodeString(data)
	{
		var result = "";
		var list = data.split(/\n/);//줄 단위로 분리
		var isHeader = $(ck_include_header).prop("checked");
		if(isHeader){
			result += "{| class=\"wikitable sortable\"\n";
		}
		for(var k in list)
		{
			//change item
			var item = list[k];
			if(item.replace(/(^\s*)|(\s*$)/gi, "")==""){
				continue;
			}
			
			if(isHeader && k==0){
				item = item.replace(/\t/g," !! ");
			} else {
				item = item.replace(/\t/g," || ");
			}
			// append result
			result += "|-\n";
			if(isHeader && k==0){
				result += "! ";
			} else {
				result += "| ";
			}
			result += item;
			result += "\n";
		}
		if(isHeader){
			result += "|}";
		}
		return result;
	}
	function decodeString(data)
	{
		var result = "";
		data = data.replace(/(\n\|\})/,"");//제일 끝부분
		var list = data.split(/(\n\|-\n)/);//줄 단위로 분리
		//console.log(list);
		for(var k in list)
		{
			var item = list[k];
			if(item.replace(/(\n\|-\n)/gi, "")==""){
				continue;
			}
			//상단 구문 제거
			if(k<=1)
			{
				var firstcheck = /(^\{\|)/i;
				if(firstcheck.test(item))
				{
					continue;
				}
			}
			// 앞부분 처리
			item = item.replace(/(^!)/,"");
			item = item.replace(/(^\|)/,"");
			// 공백 처리
			item = item.replace(/(^\s)|(\s*$)/gi,"");// trim 처리 => 빈칸없앰
			item = item.replace(/(\s*\|\|\s*)/g,"\|\|");//세부별 trim 처리 => 빈칸없앰
			item = item.replace(/(\s*!!\s*)/g,"!!");//세부별 trim 처리 => 빈칸없앰
			// 탭으로 변경
			item = item.replace(/(\|\|)/gi,"\t");//|| => 탭
			item = item.replace(/(!!)/gi,"\t");// !! => 탭
			result += item;
			result += "\n";
		}
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
<h2>엑셀 -> 미디어위키 변환</h2>
<div class="was-validated">
	<div class="form-group">
		<label for="input">입력 (엑셀에서 복사 후 붙여넣기)</label>
		<textarea class="form-control" rows="14" cols="100" id="input"
			placeholder="엑셀에서 영역지정 복사 후 붙여넣기 합니다" autofocus></textarea>
	</div>
	<div class="pb-5">
		<div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="ck_include_header">
          <label class="custom-control-label" for="ck_include_header">헤더 포함</label>
        </div>
		<div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="ck_reverse">
          <label class="custom-control-label" for="ck_reverse">반전</label>
        </div>
	</div>
	<h3>처리 결과</h3>
	<div class="card">
		<div class="card-body">
			<pre id="output"></pre>
		</div>
	</div>
	<br>
	<button id="btn_copyresult" class="btn btn-primary" style="cursor:pointer">Copy</button>
</div>
@stop