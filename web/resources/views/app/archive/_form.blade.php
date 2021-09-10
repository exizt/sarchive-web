<div class="">
	<div class="form-group">
		<label for="frm-item-name">아카이브 명칭</label>
		<input name="name" class="form-control" type="text" autofocus id="frm-item-name"
			value="{{ $item->name }}" placeholder="명칭" aria-label="명칭">
		<small class="text-mute">(최초 생성시에 루트 카테고리명이 됩니다.)</small>
	</div>

	<div class="form-group">
		<label for="frm-item-text">요약 설명</label>
		<input name="comments" type="text" id="frm-item-text" class="form-control" value="{{ $item->comments }}" placeholder="" aria-label="">
		<small class="text-mute">(아카이브 프로필에 대해서 간략한 메모를 할 수 있습니다.)</small>
	</div>

	<div class="form-group">
		<div class="form-check">
			<input name="is_default" type="checkbox" class="form-check-input" id="frm-item-isdefault" {{ $item->is_default ? 'checked' : '' }}>
			<label class="form-check-label" for="frm-item-isdefault">기본 아카이브 지정</label>
		</div>
		<small class="text-mute">(해당 아카이브 프로필을 기본으로 지정합니다.)</small>
	</div>
</div>
