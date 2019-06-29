<div class="form-group row">
	<label for="mform-yearmonth" class="col-md-2 control-label">년월</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="yearmonth" autofocus id="mform-yearmonth" value="@isset($item){{ $item->yearmonth }}@endisset">
		<small>※ '변경하기' 일 때에는 수정할 수 없습니다.</small>
	</div>
</div>
<div class="form-group row">
	<label for="mform-national_pension" class="col-md-2">국민연금료율</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="national_pension" id="mform-national_pension" value="@isset($item){{ $item->national_pension }}@endisset">
	</div>
</div>
<div class="form-group row">
	<label for="mform-process" class="col-md-2">건강 보험료율</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="health_care" id="mform-process" value="@isset($item){{ $item->health_care }}@endisset">
	</div>
</div>
<div class="form-group row">
	<label for="mform-long_term_care" class="col-md-2">요양보험료율</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="long_term_care" id="mform-long_term_care" value="@isset($item){{ $item->long_term_care }}@endisset">
	</div>
</div>
<div class="form-group row">
	<label for="mform-employment_care" class="col-md-2">고용보험료율</label>
	<div class="col-md-10">
		<input type="text" class="form-control" name="employment_care" id="mform-employment_care" value="@isset($item){{ $item->employment_care }}@endisset">
	</div>
</div>