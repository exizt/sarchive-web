<div>
	<div class="form-group row">
		<label for="title" class="col-md-2">제목</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="title" autofocus
				id="title" value="{{ $post->title }}">
		</div>
	</div>

	<div class="form-group row">
		<label for="content_summary" class="col-md-2">요약</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="content_summary" id="content_summary" value="{{ $post->content_summary }}">
		</div>
	</div>

	<div class="form-group row">
		<label for="content" class="col-md-2">본문</label>
		<div class="col-md-10">
			<textarea class="form-control" name="content" rows="14" id="content">{{ $post->content }}</textarea>
		</div>
	</div>

	<div class="form-group row">
		<label for="tags" class="col-md-2">Tags</label>
		<div class="col-md-10">
			<select name="tags[]" id="tags" multiple data-role="tagsinput">
				@foreach ($post->tags as $item)
				<option value="{{ $item->tag }}">{{ $item->tag }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group row">
		<label for="publish_date" class="col-md-2">발행일시</label>
		<div class="col-md-10">
			<input class="form-control" name="publish_date" id="publish_date"
				type="datetime-local" min="2000-01-01T01:00" value="{{ $post->published_at->format('Y-m-d\TH:i') }}">
		</div>
	</div>
	
	<div class="form-group row">
		<label for="image_header" class="col-md-2">메인 이미지 (경로)</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="image_header" id="image_header" value="{{ $post->image_header }}">
		</div>
	</div>
	
	<div class="form-group row">
		<label for="is_secret" class="col-md-2">비공개 여부</label>
		<div class="col-md-10">
			<div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="is_secret" id="is_secret" {{ $post->is_secret ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_secret">비공개</label>
            </div>
		</div>
	</div>
	
	<div class="form-group row">
		<label for="is_incompleted" class="col-md-2">미완료 여부</label>
		<div class="col-md-10">
			<div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="is_incompleted" id="is_incompleted" {{ $post->is_completed ? '' : 'checked' }}>
                <label class="custom-control-label" for="is_incompleted">미완료</label>
            </div>
		</div>
	</div>
</div>