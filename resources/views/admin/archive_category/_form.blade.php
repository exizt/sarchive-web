<div class="form-group">
	<label for="category_name">카테고리 명</label>
	<input type="text" class="form-control" name="name" autofocus id="category_name" value="{{ $item->name }}">
</div>
@if( ! empty($categories))
<div class="form-group">
	<label for="category_parent">상위 카테고리 선택</label> 
	<select name="parent_id" class="form-control" title="상위 카테고리" id="category_parent">
	@foreach ($categories as $cate) 
		@if ($cate->id == $item->parent_id)
		<option value="{{ $cate->id }}" selected>{{ $cate->name }}</option> 
		@else
		<option value="{{ $cate->id }}">{{ $cate->name }}</option> 
		@endif 
	@endforeach
	</select>
</div>
@endif
<div class="form-group">
	<label for="comment">설명</label>
	<div>
		<input type="text" class="form-control" name="comment" id="comment" value="{{ $item->comment }}">
	</div>
</div>