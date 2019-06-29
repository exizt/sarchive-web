<div class="form-group row">
	<label for="mform-name" class="col-md-2 control-label">명칭</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="name" autofocus id="mform-name" value="@isset($item){{ $item->name }}@endisset">
	</div>
</div>
<div class="form-group row">
	<label for="mform-description" class="col-md-2">설명</label>
	<div class="col-md-10">
		<textarea class="form-control" name="description" rows="14" id="mform-description">@isset($item){{ $item->description }}@endisset</textarea>
	</div>
</div>
<div class="form-group row">
	<label for="mform-process" class="col-md-2">계산 과정</label>
	<div class="col-md-10">
		<textarea class="form-control" name="process" rows="14" id="mform-process">@isset($item){{ $item->process }}@endisset</textarea>
	</div>
</div>
<div class="form-group row">
	<label for="mform-history" class="col-md-2">세율 기록</label>
	<div class="col-md-10">
		<textarea class="form-control" name="history" rows="14" id="mform-history">@isset($item){{ $item->history }}@endisset</textarea>
	</div>
</div>
<div class="form-group row">
	<label for="mform-cid" class="col-md-2 control-label">Cid</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="cid" autofocus id="mform-cid" value="@isset($item){{ $item->cid }}@endisset">
		 <small>(필수값 : 이 값을 외부적으로 이용할 때에 이용할 키 값이다. 서로 겹치지 않도록 하고. 영어로 한다. 빈칸이 없도록 한다. 예시: national_pension)</small>
	</div>
</div>