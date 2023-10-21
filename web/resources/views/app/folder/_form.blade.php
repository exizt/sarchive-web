<div class="">
    <div class="mb-3">
        <label class="mb-2" data-auto-click="true">폴더명</label>
        <input name="name" class="form-control" type="text" autofocus
            value="{{ $item->name }}" placeholder="명칭" aria-label="명칭">
    </div>

    <div class="mb-3">
        <label class="mb-2" data-auto-click="true">요약 설명</label>
        <input name="comments" type="text" class="form-control"
            value="{{ $item->comments }}" placeholder="" aria-label="">
    </div>


    <div class="mb-3">
        <label class="mb-2" for="parentFolderName">상위 폴더 선택</label>
        <input id="parentFolderName" class="form-control" type="text" placeholder="" readonly
            @isset($parentFolder) value="{{ $parentFolder->name }}" @endisset
            data-bs-toggle="modal" data-bs-target="#modalChoiceFolder"
            data-folder-excluded="{{ $item->id }}"
            data-folder-id-target="#parentFolderId"
            data-folder-name-target="#parentFolderName">
        <input name="parent_id" type="hidden" id="parentFolderId" @isset($parentFolder) value="{{ $parentFolder->id }}" @endisset>
    </div>
</div>

@include('app.folder-selector.modal')
