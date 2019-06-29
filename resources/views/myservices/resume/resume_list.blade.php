@extends('layouts.myservice_layout')
@section('content')
<script>
var g_items = null;
var g_resume_srl = null;
$(document).ready(function(){
	setBeforeItems();
	//onloadMasterResume();
	//$("#btn-submit").on("click",onSaveMasterEvent);
	$("#btn-preview").on("click",onPreviewEvent);
	$(".event-option-additem").on("click",addItemEvent);
	$(".event-option-removeitem").on("click",removeItemEvent);

	setSelOptions();
});

function setSelOptions()
{
	$("select[data-value]").each(function(){
		if($(this).find("option[value='"+$(this).attr("data-value")+"']").length > 0){
			$(this).find("option[value='"+$(this).attr("data-value")+"']").attr('selected', 'selected');//셀렉트박스
		}
	});
}
/**
 * 리스트 를 위한 부분.
 * ajax 를 하기 전에 미리 html 을 각각 로드 해놓는다. 
 * 이 내용으로 append 하게 된다.
 */
function setBeforeItems()
{
	g_items = {
		"itemSchools":  $("#itemSchools .item-contents-dataform")[0].outerHTML,
		"itemCareers":  $("#itemCareers .item-contents-dataform")[0].outerHTML,
		"itemCerts":    $("#itemCerts .item-contents-dataform")[0].outerHTML,
		"itemLanguages":$("#itemLanguages .item-contents-dataform")[0].outerHTML,
		"itemSkills":   $("#itemSkills .item-contents-dataform")[0].outerHTML,
		"itemProjects": $("#itemProjects .item-contents-dataform")[0].outerHTML,
	};
}

/**
 * 항목 추가 이벤트
 */
function addItemEvent(e)
{
	e.preventDefault();
	var id = $(this).closest(".item-container").attr("id");
	var count = $("#"+id).find(".item-contents-dataform").length;
	var text = g_items[id];
	text = text.replace(/\[0\]/gi,"["+count+"]");
	
	$("#"+id).find(".item-contents-dataform").parent("div").append(text);
}
/**
 * 항목 추가 이벤트
 */
function removeItemEvent(e)
{
	e.preventDefault();
	var id = $(this).closest(".item-container").attr("id");
	var count = $(this).closest(".item-container").find(".item-contents-dataform").length-1;
	if(count!=0){
		$("#"+id).find(".item-contents-dataform:last-child").remove();
	}
}

/**
 * 미리보기 버튼의 이벤트
 * 새창으로 띄우려고 하는데 잘 안 되서 고민중.
 */
function onPreviewEvent(e)
{
	e.preventDefault();

	// 미리 팝업을 띄워놓고. 
	var w = window.open("about:blank","_blank");
	
	submitMaster(eventSuccess);
	function eventSuccess(data)
	{
		//여기서 팝업된 창의 주소를 변경하자.
		w.location.href = "./Resume/Preview";
	}
}

</script>
<div class="mt-5 mb-5 container">
	<h3>이력서 관리</h3>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible text-center" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		&nbsp; {{ session('status') }}
	</div>
	@endif
	
	<div class="bs-callout bs-callout-info" style="display: none;">
		<h4>버튼 설명</h4>
		저장하기 : 이력서 작성사항을 저장합니다.<br> 백업불러오기 : 작업중 <br>백업 : 작업중
	</div>
	<div role="tabpanel">
		<form action="/service/resume" id="resumes_form" method="post" accept-charset="utf-8">
		 {{ csrf_field() }}
		<input type="hidden" name="resume_id" value="{{$resume_id}}">
		<input type="hidden" name="mode" value="save">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item"><a class="nav-link active" href="#home"
				role="tab" data-toggle="tab">인적사항</a></li>
			<li class="nav-item"><a class="nav-link" href="#profile" role="tab"
				data-toggle="tab">경력</a></li>
			<li class="nav-item"><a class="nav-link" href="#messages" role="tab"
				data-toggle="tab">자기소개</a></li>
			<li class="nav-item"><a class="nav-link" href="#settings" role="tab"
				data-toggle="tab">질문답변</a></li>
		</ul>


		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="home">

				<div class="bs-callout bs-callout-primary">
					<h4>기본 인적사항 입력</h4>
					기본 인적사항 과 학력사항 등을 입력합니다. <br> <span
						class="glyphicon glyphicon-bullhorn" aria-hidden="true"></span>
					전화번호 및 주민등록번호 항목은 개인정보이므로 제외 하였습니다.
				</div>

				<div class="card">
					<div class="card-header">기본 인적사항</div>
					<div class="card-block">
						<div class="form-group row">
							<label for="name_han" class="col-sm-2 col-form-labe">이름(한글)</label>
							<div class="col-sm-4">
								<input type="text" name="details[name]" id="name_han"
									class="form-control" placeholder="홍길동" title="이름(한글)"
									value="{{$view_details_name}}">
							</div>
							<label for="name_eng" class="col-sm-2 col-form-labe">이름(영문)</label>
							<div class="col-sm-4">
								<input type="text" name="details[name_english]" id="name_eng"
									class="form-control"
									placeholder="gil-dong hong 또는 hong, gil-dong" title="이름(영문)"
									value="{{$view_details_name_english}}">
							</div>
						</div>

						<div class="form-group row">
							<label for="name_hanja" class="col-sm-2 control-label">이름(한자)</label>
							<div class="col-sm-4">
								<input type="text" name="details[name_hanja]" id="name_hanja"
									class="form-control" placeholder="洪吉洞" title="이름(한자)"
									value="{{$view_details_name_hanja}}">
							</div>
							<label for="birthday" class="col-sm-2 control-label">생년월일</label>
							<div class="col-sm-4">
								<input type="text" name="details[birthday]" id="birthday"
									class="form-control" placeholder="0000.00.00" title="생년월일"
									value="{{$view_details_birthday}}">
							</div>
						</div>

						<div class="form-group row">
							<label for="email" class="col-sm-2 control-label">이메일</label>
							<div class="col-sm-10">
								<input type="email" name="details[email]" id="email"
									class="form-control" placeholder="sample@sample.domain"
									title="이메일" disabled>
							</div>
						</div>

						<div class="form-group row">
							<label for="married" class="col-sm-2 control-label">결혼여부</label>
							<div class="col-sm-4">
								<select name="details[married]" class="form-control"
									title="결혼여부" data-value="{{$view_details_married}}">
									<option value="N">미혼</option>
									<option value="Y">기혼</option>
								</select>
							</div>
							<label for="family" class="col-sm-2 control-label">가족사항</label>
							<div class="col-sm-4">
								<input type="text" name="details[family]" id="family"
									class="form-control" placeholder="0남 0녀 중 0째" title="가족사항"
									value="{{$view_details_family}}">
							</div>
						</div>

						<div class="form-group row">
							<label for="expected_salary" class="col-sm-2 control-label">희망연봉</label>
							<div class="col-sm-4">
								<input type="text" name="details[expected_salary]"
									id="expected_salary" class="form-control" placeholder="0000"
									title="희망연봉" value="{{$view_details_expected_salary}}">
							</div>
							<label for="expected_term" class="col-sm-2 control-label">입사가능시기</label>
							<div class="col-sm-4">
								<select class="form-control" name="details[expected_term]"
									id="expected_term" title="입사가능시기" data-value="">
									<option>즉시 입사가능</option>
									<option>2주 내 입사가능</option>
									<option>2주이상 후 입사가능</option>
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label for="address" class="col-sm-2 control-label">주소</label>
							<div class="col-sm-10">
								<input type="text" name="details[address]" class="form-control"
									placeholder="서울시 A구 B동" title="주소" disabled>
							</div>
						</div>
					</div>
				</div>

				<div class="card item-container" id="itemSchools">
					<div class="card-header">
						학력사항 <a href="#" class="event-option-additem"><i
							class="fa fa-plus fa-fw" aria-hidden="true"></i></a> <a href="#"
							class="event-option-removeitem"><i class="fa fa-minus fa-fw"
							aria-hidden="true"></i></a>
					</div>
					<div class="card-block">
						@foreach ($view_schools as $item)
						<div class="card item-contents-dataform">
							<div class="card-block">
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">학력구분</label>
									<div class="col-sm-2">
										<select name="school[0][type]" class="form-control"
											title="학력구분" data-value="{{$item->type}}">
											<option value="고등학교">고등학교</option>
											<option value="전문대학">전문대학</option>
											<option value="대학교">대학교</option>
											<option value="대학원">대학원</option>
											<option value="기타학교">기타학교</option>
										</select>
									</div>
									<label for="expected_term" class="control-label col-sm-2">학교명</label>
									<div class="col-sm-2">
										<input type="text" name="school[0][name]" class="form-control"
											placeholder="**대학교" title="학교명" value="{{$item->name}}">
									</div>
									<label for="expected_term" class="control-label col-sm-2">입학일</label>
									<div class="col-sm-2">
										<input type="text" name="school[0][dateAdmission]"
											class="form-control" placeholder="0000.00.00" title="입학일"
											value="{{$item->date_admission}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_term" class="control-label col-sm-2">졸업(예정)일</label>
									<div class="col-sm-2">
										<input type="text" name="school[0][dateGraduation]"
											class="form-control" placeholder="0000.00.00" title="졸업일"
											value="{{$item->date_graduation}}">
									</div>
									<label for="expected_term" class="control-label col-sm-2">학위</label>
									<div class="col-sm-2">
										<select name="school[0][degree]" class="form-control"
											title="학위" data-value="{{$item->degree}}">
											<option value="학사" selected>학사</option>
											<option value="석사">석사</option>
											<option value="박사">박사</option>
											<option value="기타학위">기타학위</option>
										</select>
									</div>
									<label for="expected_term" class="control-label col-sm-2">전공</label>
									<div class="col-sm-2">
										<input type="text" name="school[0][major]"
											class="form-control" placeholder="" title="전공"
											value="{{$item->major}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_term" class="control-label col-sm-2">복수/부전공</label>
									<div class="col-sm-2">
										<input type="text" name="school[0][minor]"
											class="form-control" placeholder="" title="복수/부전공"
											value="{{$item->minor}}">
									</div>
									<label for="expected_term" class="control-label col-sm-2">졸업구분</label>
									<div class="col-sm-2">
										<select name="school[0][graduation]" class="form-control"
											title="졸업구분" data-value="{{$item->graduation}}">
											<option value="졸업">졸업</option>
											<option value="졸업예정">졸업예정</option>
											<option value="수료">수료</option>
											<option value="재학">재학</option>
											<option value="중퇴">중퇴</option>
											<option value="검정">검정</option>
										</select>
									</div>
									<label for="expected_term" class="control-label col-sm-2">학점</label>
									<div class="col-sm-2">
										<input type="text" name="school[0][gpa]" class="form-control"
											id="" placeholder="0.00" title="학점" value="{{$item->gpa}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_term" class="control-label col-sm-2">만점기준</label>
									<div class="col-sm-2">
										<select name="school[0][scoreScale]" class="form-control"
											title="만점기준" data-value="{{$item->scorescale}}">
											<option value="4.5">4.5</option>
											<option value="4.3">4.3</option>
											<option value="100">100</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<!-- //item-contents-dataform -->
						@endforeach
					</div>
				</div>

				<div class="card">
					<div class="card-header">병역사항</div>
					<div class="card-block">
						<div class="form-group row">
							<label for="mil_category" class="col-sm-2 control-label">구분</label>
							<div class="col-sm-3">
								<select name="details[mil_category]" id="mil_category"
									class="form-control" title="병역구분"
									data-value="{{$view_details_mil_category}}">
									<option>현역필</option>
									<option>보충역필</option>
									<option>특례필</option>
									<option>복무중</option>
									<option>미필</option>
									<option>면제</option>
									<option>비대상</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="mil_kind" class="col-sm-2 control-label">군별</label>
							<div class="col-sm-4">
								<select name="details[mil_kind]" id="mil_kind"
									class="form-control" title="병역 군별"
									data-value="{{$view_details_mil_kind}}">
									<option>육군</option>
									<option>공군</option>
									<option>해군</option>
									<option>기타</option>
								</select>
							</div>
							<label for="mil_grade" class="col-sm-2 control-label">계급</label>
							<div class="col-sm-4">
								<select name="details[mil_grade]" id="mil_grade"
									class="form-control" title="계급"
									data-value="{{$view_details_mil_grade}}">
									<option>병장</option>
									<option>상병</option>
									<option>일병</option>
									<option>이등병</option>
									<option>상사</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="mil_date_start" class="col-sm-2 control-label">입대일</label>
							<div class="col-sm-4">
								<input name="details[mil_date_start]" type="text"
									id="mil_date_start" class="form-control"
									placeholder="0000.00.00" title="입대일"
									data-value="{{$view_details_mil_date_start}}">
							</div>
							<label for="mil_date_End" class="col-sm-2 control-label">전역일</label>
							<div class="col-sm-4">
								<input name="details[mil_date_End]" type="text"
									id="mil_date_End" class="form-control" placeholder="0000.00.00"
									title="전역일" data-value="{{$view_details_mil_date_end}}">
							</div>
						</div>
						<div class="form-group row">
							<label for="mil_branch" class="col-sm-2 control-label">병과</label>
							<div class="col-sm-4">
								<input name="details[mil_branch]" type="text" id="mil_branch"
									class="form-control" placeholder="" title="병과"
									data-value="{{$view_details_mil_branch}}">
							</div>
							<label for="mil_note" class="col-sm-2 control-label">면제(미필) 사유</label>
							<div class="col-sm-4">
								<input name="details[mil_note]" type="text" id="mil_note"
									class="form-control" placeholder="" title="면제사유"
									data-value="{{$view_details_mil_note}}">
							</div>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-header">장애사항</div>
					<div class="card-block">
						<div class="form-group row">
							<label for="disability_category" class="col-sm-1 control-label">구분</label>
							<div class="col-sm-2">
								<select name="details[disability_category]"
									id="disability_category" class="form-control" title="장애구분"
									data-value="{{$view_details_disability_category}}">
									<option>해당없음</option>
									<option>등록장애인</option>
									<option>국가유공장애인</option>
									<option>기타</option>
								</select>
							</div>
							<label for="disability_date" class="col-sm-2 control-label">일자</label>
							<div class="col-sm-3">
								<input name="details[disability_date]" id="disability_date"
									type="text" class="form-control" id="" placeholder="0000.00.00"
									title="일자" value="{{$view_details_disability_date}}">
							</div>
							<label for="disability_grade" class="col-sm-1 control-label">등급</label>
							<div class="col-sm-3">
								<select name="details[disability_grade]" id="disability_grade"
									class="form-control" title="장애등급"
									data-value="{{$view_details_disability_grade}}">
									<option>1급</option>
									<option>2급</option>
									<option>3급</option>
									<option>4급</option>
									<option>5급</option>
									<option>6급</option>
									<option>7급</option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-header">보훈사항</div>
					<div class="card-block">
						<div class="form-group row">
							<label for="veteran_category" class="col-sm-1 control-label">구분</label>
							<div class="col-sm-2">
								<select name="details[veteran_category]" id="veteran_category"
									class="form-control" title="보훈구분"
									data-value="{{$view_details_veteran_category}}">
									<option>해당없음</option>
									<option>보훈대상</option>
								</select>
							</div>
							<label for="veteran_number" class="col-sm-2 control-label">보훈번호</label>
							<div class="col-sm-3">
								<input name="details[veteran_number]" id="veteran_number"
									type="text" class="form-control" id="" placeholder=""
									title="보훈번호" value="{{$view_details_veteran_number}}">
							</div>
							<label for="veteran_organ" class="col-sm-1 control-label">보훈청</label>
							<div class="col-sm-3">
								<input name="details[veteran_org]" id="veteran_organ"
									type="text" class="form-control" id="" placeholder=""
									title="보훈청" value="{{$view_details_veteran_org}}">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane fade" id="profile">
				<div class="bs-callout bs-callout-primary">
					<h4>경력 사항 입력</h4>
					경력 및 스킬 과 자격증에 대한 항목입니다.
				</div>

				<div class="card item-container" id="itemCareers">
					<div class="card-header">
						경력사항 <a href="#" class="event-option-additem"><i
							class="fa fa-plus fa-fw" aria-hidden="true"></i></a> <a href="#"
							class="event-option-removeitem"><i class="fa fa-minus fa-fw"
							aria-hidden="true"></i></a>
					</div>
					<div class="card-block">
						@foreach ($view_careers as $item)
						<div class="card item-contents-dataform">
							<div class="card-block">
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">입사일</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][dateStart]"
											class="form-control" id="" placeholder="" title="입사일"
											value="{{$item->date_start}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">퇴사일</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][dateEnd]"
											class="form-control" id="" placeholder="" title="퇴사일"
											value="{{$item->date_end}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">직장명</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][company]"
											class="form-control" id="" placeholder="" title="직장명"
											value="{{$item->company}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">인력구분</label>
									<div class="col-sm-2">
										<select name="career[0][type]" class="form-control"
											title="인력구분" data-value="{{$item->type}}">
											<option value="정규직">정규직</option>
											<option value="계약직">계약직</option>
											<option value="인턴">인턴</option>
											<option value="기타">기타</option>
										</select>
									</div>

									<label for="expected_salary" class="control-label col-sm-2">소속</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][department]"
											class="form-control" id="" placeholder="" title="소속"
											value="{{$item->department}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">직책</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][position]"
											class="form-control" id="" placeholder="" title="직책"
											value="{{$item->position}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">담당업무</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][role]" class="form-control"
											id="" placeholder="" title="담당업무" value="{{$item->role}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">연봉(만원)</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][salary]"
											class="form-control" id="" placeholder="" title="연봉"
											value="{{$item->salary}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">퇴직사유</label>
									<div class="col-sm-2">
										<input type="text" name="career[0][reason]"
											class="form-control" id="" placeholder="" title="퇴직사유"
											value="{{$item->reason}}">
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>

				<div class="card item-container" id="itemCerts">
					<div class="card-header">
						자격증 정보 <a href="#" class="event-option-additem"><i
							class="fa fa-plus fa-fw" aria-hidden="true"></i></a> <a href="#"
							class="event-option-removeitem"><i class="fa fa-minus fa-fw"
							aria-hidden="true"></i></a>
					</div>
					<div class="card-block">
						@foreach ($view_certificates as $item)
						<div class="card item-contents-dataform">
							<div class="card-block">
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">자격/면허</label>
									<div class="col-sm-2">
										<input type="text" name="cert[0][name]" class="form-control"
											placeholder="" title="자격/면허" value="{{$item->name}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">취득일</label>
									<div class="col-sm-2">
										<input type="text" name="cert[0][date]" class="form-control"
											placeholder="" title="취득일" value="{{$item->date}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">자격번호</label>
									<div class="col-sm-2">
										<input type="text" name="cert[0][number]" class="form-control"
											placeholder="" title="자격번호" value="{{$item->number}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">발급기관</label>
									<div class="col-sm-2">
										<input type="text" name="cert[0][institute]"
											class="form-control" placeholder="" title="발급기관"
											value="{{$item->institute}}">
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>

				<div class="card item-container" id="itemLanguages">
					<div class="card-header">
						어학 정보 <a href="#" class="event-option-additem"><i
							class="fa fa-plus fa-fw" aria-hidden="true"></i></a> <a href="#"
							class="event-option-removeitem"><i class="fa fa-plus fa-fw"
							aria-hidden="true"></i></a>
					</div>
					<div class="card-block">
						@foreach ($view_language_skills as $item)
						<div class="card item-contents-dataform">
							<div class="card-block">
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">구분</label>
									<div class="col-sm-2">
										<select name="language[0][category]" class="form-control"
											title="어학구분" data-value="{{$item->category}}">
											<option>영어</option>
											<option>중국어</option>
											<option>일본어</option>
											<option>기타</option>
										</select>
									</div>
									<label for="expected_salary" class="control-label col-sm-2">시험명</label>
									<div class="col-sm-2">
										<input type="text" name="language[0][examination]"
											class="form-control" placeholder="" title="시험명"
											value="{{$item->examination}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">평가일</label>
									<div class="col-sm-2">
										<input type="text" name="language[0][date]"
											class="form-control" placeholder="" title="평가일"
											value="{{$item->date}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">평가기관(주관)</label>
									<div class="col-sm-2">
										<input type="text" name="language[0][institute]"
											class="form-control" placeholder="" title="평가기관"
											value="{{$item->institute}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">점수</label>
									<div class="col-sm-2">
										<input type="text" name="language[0][score]"
											class="form-control" placeholder="" title="평가점수"
											value="{{$item->score}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">등급</label>
									<div class="col-sm-2">
										<input type="text" name="language[0][grade]"
											class="form-control" placeholder="" title="평가등급"
											value="{{$item->grade}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">특이사항</label>
									<div class="col-sm-2">
										<input type="text" name="language[0][note]"
											class="form-control" placeholder="" title="특이사항"
											value="{{$item->note}}">
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>

				<div class="card item-container" id="itemSkills">
					<div class="card-header">
						보유 기술 <a href="#" class="event-option-additem"><i
							class="fa fa-plus fa-fw" aria-hidden="true"></i></a> <a href="#"
							class="event-option-removeitem"><i class="fa fa-minus fa-fw"
							aria-hidden="true"></i></a>
					</div>
					<div class="card-block">
						@foreach ($view_skills as $item)
						<div class="card item-contents-dataform">
							<div class="card-block">
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">구분</label>
									<div class="col-sm-2">
										<select name="skill[0][category]" class="form-control"
											title="보유기술 구분" data-value="{{$item->category}}">
											<option value="웹프로그래밍">웹프로그래밍</option>
											<option value="서버프로그래밍">서버프로그래밍</option>
											<option value="클라이언트프로그래밍">클라이언트프로그래밍</option>
											<option value="시스템기술">시스템기술</option>
											<option value="DB기술">DB기술</option>
											<option value="보안기술">보안기술</option>
											<option value="디자인">디자인</option>
											<option value="기타">기타</option>
										</select>
									</div>
									<label for="expected_salary" class="control-label col-sm-2">세부기술</label>
									<div class="col-sm-2">
										<input type="text" name="skill[0][detail]"
											class="form-control" id="" placeholder="" title="보유 세부기술"
											value="{{$item->detail}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">기술수준</label>
									<div class="col-sm-2">
										<select name="skill[0][grade]" class="form-control"
											title="기술수준" data-value="{{$item->grade}}">
											<option value="상">상</option>
											<option value="중">중</option>
											<option value="하">하</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">비고(구현/활용경험
										등)</label>
									<div class="col-sm-2">
										<input type="text" name="skill[0][note]" class="form-control"
											id="" placeholder="" title="비고(구현/활용경험 등)" value="{{$item->note}}">
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>

				<div class="card item-container" id="itemProjects">
					<div class="card-header">
						프로젝트 이력 <a href="#" class="event-option-additem"><i
							class="fa fa-plus fa-fw" aria-hidden="true"></i></a> <a href="#"
							class="event-option-removeitem"><i class="fa fa-minus fa-fw"
							aria-hidden="true"></i></a>
					</div>
					<div class="card-block">
						@foreach ($view_projects as $item)
						<div class="card item-contents-dataform">
							<div class="card-block">
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">프로젝트명</label>
									<div class="col-sm-2">
										<input type="text" name="project[0][subject]"
											class="form-control" id="" placeholder="" title="프로젝트명"
											value="{{$item->subject}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">시작일</label>
									<div class="col-sm-2">
										<input type="text" name="project[0][dateStart]"
											class="form-control" id="" placeholder="" title="시작일"
											value="{{$item->date_start}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">종료일</label>
									<div class="col-sm-2">
										<input type="text" name="project[0][dateEnd]"
											class="form-control" id="" placeholder="" title="종료일"
											value="{{$item->date_end}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">소속회사명</label>
									<div class="col-sm-2">
										<input type="text" name="project[0][company]"
											class="form-control" id="" placeholder="" title="소속회사명"
											value="{{$item->company}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">발주처</label>
									<div class="col-sm-2">
										<input type="text" name="project[0][company2]"
											class="form-control" id="" placeholder="" title="발주처"
											value="{{$item->company2}}">
									</div>
									<label for="expected_salary" class="control-label col-sm-2">주요
										역할</label>
									<div class="col-sm-2">
										<input type="text" name="project[0][role]"
											class="form-control" id="" placeholder="" title="주요역할"
											value="{{$item->role}}">
									</div>
								</div>
								<div class="form-group row">
									<label for="expected_salary" class="control-label col-sm-2">세부내용</label>
									<div class="col-sm-10">
										<textarea name="project[0][note]" class="form-control"
											rows="3" title="세부내용">{{$item->note}}</textarea>
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>


			</div>
			<div role="tabpanel" class="tab-pane fade" id="messages">
				<div class="bs-callout bs-callout-primary">
					<h4>주로 나오는 자기소개 항목</h4>
					보통 기본적으로 물어보는 자기소개 에 관련된 항목입니다. 불필요한 부분은 안 적으시면 됩니다.
				</div>

				<div class="card">
					<div class="card-header">공통적 사항</div>
					<div class="card-block">
						<div class="form-group">
							<label for="pr_common_1">성장 과정</label>
							<textarea name="answers[pr_common_1]" class="form-control"
								rows="5" id="pr_common_1">{{$view_answers_pr_common_1}}</textarea>
						</div>

						<div class="form-group">
							<label for="pr_common_2">학교 생활</label>
							<textarea name="answers[pr_common_2]" class="form-control"
								rows="5" id="pr_common_2">{{$view_answers_pr_common_2}}</textarea>
						</div>

						<div class="form-group">
							<label for="pr_common_3">좌우명</label>
							<textarea name="answers[pr_common_3]" class="form-control"
								rows="5" id="pr_common_3">{{$view_answers_pr_common_3}}</textarea>
						</div>

						<div class="form-group">
							<label for="pr_common_4">경력 사항</label>
							<textarea name="answers[pr_common_4]" class="form-control"
								rows="5" id="pr_common_4">{{$view_answers_pr_common_4}}</textarea>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-header">회사에 따라 다른 사항</div>
					<div class="card-block">
						<div class="form-group">
							<label for="pr_common_5">지원 동기</label>
							<textarea name="answers[pr_common_5]" class="form-control"
								rows="5" id="pr_common_5">{{$view_answers_pr_common_5}}</textarea>
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane fade" id="settings">
				<div class="bs-callout bs-callout-primary">
					<h4>이력서 질문 항목</h4>
					각 항목은 주로 나오는 질문답변 에 대한 항목입니다. <br> 작성하지 않아도 될 부분은 작성하지 않으셔도 됩니다.<br>
					항목은 계속 추가가 될 예정입니다.
				</div>

				<div class="card">
					<div class="card-header">추가적인 질문 항목</div>
					<div class="card-block">
						<div class="form-group mb-5">
							<label for="pr_answer_1">본인을 표현 할 수 있는 단어 세가지</label>
							<textarea name="answers[pr_answer_1]" class="form-control"
								rows="5" id="pr_answer_1">{{$view_answers_pr_answer_1}}</textarea>
						</div>

						<div class="form-group mb-5">
							<label for="pr_answer_2">자신의 성격 장단점에 대해 기술하여 주십시오.</label>
							<textarea name="answers[pr_answer_2]" class="form-control"
								rows="5" id="pr_answer_2">{{$view_answers_pr_answer_2}}</textarea>
						</div>

						<div class="form-group mb-5">
							<label for="pr_answer_3">지원동기 및 자신이 가지고 있는 핵심역량과 보유 기술에 대해 자유롭게
								기술하여 주십시오.</label>
							<textarea name="answers[pr_answer_3]" class="form-control"
								rows="5" id="pr_answer_3">{{$view_answers_pr_answer_3}}</textarea>
						</div>
						<div class="form-group mb-5">
							<label for="pr_answer_4">지금까지 겪었던 일들 중 가장 큰 성공 또는 실패의 경험과 성공의 이유
								또는 실패를 통해 얻은 교훈에 대해 기술하여 주십시오.</label>
							<textarea name="answers[pr_answer_4]" class="form-control"
								rows="5" id="pr_answer_4">{{$view_answers_pr_answer_4}}</textarea>
						</div>
						<div class="form-group mb-5">
							<label for="pr_answer_5">장기적인 경력 목표(Career Goal)은 무엇이며 그 목표를 이루기
								위한 계획 및 포부에 대해 기술하여 주십시오.</label>
							<textarea name="answers[pr_answer_5]" class="form-control"
								rows="5" id="pr_answer_5">{{$view_answers_pr_answer_5}}</textarea>
						</div>
						<div class="form-group">
							<label for="pr_answer_6">자신에 대해 자유롭게 기술하여 주십시오.</label>
							<textarea name="answers[pr_answer_6]" class="form-control"
								rows="5" id="pr_answer_6">{{$view_answers_pr_answer_6}}</textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="pt-3">
			<button type="submit" class="btn btn-primary" style="cursor:pointer">저장하기</button>
		</div>
		</form>
	</div>
	<div class="pt-3">
		<!-- <button type="button" id="btn-submit" class="btn btn-primary">저장하기</button>
		<button type="button" id="btn-preview" class="btn btn-info">저장후미리보기</button> -->
		<!-- <button type="button" class="btn btn-info">백업불러오기</button> -->
		<!-- <button type="button" class="btn btn-success">백업</button>-->
	</div>
</div>
@stop