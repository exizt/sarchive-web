<div class="">
    <div class="mb-3">
        <label class="mb-2" data-auto-click="true">분류 설명</label>
        <input name="comments" type="text" class="form-control" value="{{ $item->comments }}">
    </div>

    <div class="mb-3">
        <label class="mb-2" data-auto-click="true">상위 분류 (ex: [분류명A] [분류명B])</label>
        <input name="category" type="text" class="form-control" value="{{ $item->category }}">
    </div>
</div>
