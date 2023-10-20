{{-- bs5 style --}}
<div class="modal fade" id="modal-delete" tabIndex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead">
                    삭제하시겠습니까?
                </p>
            </div>
            <div class="modal-footer">
                <form method="POST"
                    action="{{ $action }}">
                    <input type="hidden" name="_token" value="{{ $token }}"> <input
                        type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">취소</button>
                    <button type="submit" class="btn btn-danger">삭제</button>
                </form>
            </div>
        </div>
    </div>
</div>
