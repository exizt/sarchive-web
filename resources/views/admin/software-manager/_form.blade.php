<script>
$(document).ready(function(){
	$("#add-screenshot-image").on("click",addScreenshotImage);
	
});

/**
 * 
 */
function addScreenshotImage()
{
	var a = "";
	a += '<div class="form-group">';
	a += '<label for="screenshot_images">파일 업로드 (업로드 되어 있는 파일 : )</label>';
	a += '<input type="file" class="form-control" name="screenshot_images[]">';
	a += '</div>';

	$("#wrap-screenshot-images").append(a);	
}

</script>
<div class="card">
	<div class="card-header">
    <ul class="nav nav-tabs card-header-tabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">기본</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">배포</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">개인정보처리방침</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="nav-images-tab" data-toggle="tab" href="#nav-images" role="tab" aria-controls="nav-images" aria-selected="false">이미지</a>
      </li>      
    </ul>
	</div>
	<div class="card-body">
    	<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <h3>최소 정보</h3>
                <div class="form-group">
                	<label for="software_sku">소프트웨어 키 (SKU 참고)</label>
                	<input type="text" class="form-control" name="software_sku" autofocus id="software_sku" value="{{ $item->software_sku }}">
                </div>
                <h3 class="pt-5">기본 정보</h3>
                <div class="form-group">
                	<label for="software_name">소프트웨어 명칭 (리스트에 표현될 명칭)</label>
                	<input type="text" class="form-control" name="software_name" id="software_name" value="{{ $item->software_name }}">
                </div>
                <div class="form-group">
                	<label for="description">간단 설명</label>
                	<input type="text" class="form-control" name="description" id="description" value="{{ $item->description }}">
                </div>
                <div class="form-group">
                	<label for="contents">표시될 컨텐츠&nbsp;<small>＃ Markdown 방식 입력</small></label>
                	<textarea class="form-control" name="contents" rows="14" id="contents">{{ $item->contents_markdown }}</textarea>
                </div>
                
                <h3 class="pt-5">페이지 정보</h3>
                <div class="form-group">
                	<label for="subject">사이트 표기 타이틀 (상세 페이지에서 사용되는 타이틀)</label>
                	<input type="text" class="form-control" name="subject" id="subject" value="{{ $item->subject }}">
                </div>
                <div class="form-group">
                	<label for="software_uri">자체 URI (URL 기준 식별자)&nbsp;<small>＃ 가능하면 소문자로 할 것</small></label>
                	<input type="text" class="form-control" name="software_uri" autofocus id="software_uri" value="{{ $item->software_uri }}">
                </div>
                <div class="form-group">
                	<label for="external_link">다른 페이지 연결 링크 (여기에 값이 있으면 URL 무시)</label>
                	<input type="text" class="form-control" name="external_link" autofocus id="external_link" value="{{ $item->external_link }}">
                </div>			
			
			</div>
			<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
				<div class="form-group">
					<label for="privacy_statement">개인정보 처리방침</label>
					<textarea class="form-control" name="privacy_statement" rows="14" id="privacy_statement">{{ $item->privacy_statement }}</textarea>
				</div>
			</div>
			<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                <h3>릴리즈 및 배포 정보</h3>
                <div class="form-group">
                	<label for="version_latest">최근 버전 번호</label>
                	<input type="text" class="form-control" name="version_latest" id="version_latest" value="{{ $item->version_latest }}">
                </div>
                <div class="form-group">
                	<label for="download_file">설치 파일 업로드 (업로드 되어 있는 파일 : {{ $item->download_file }})</label>
                	<input type="file" class="form-control" name="download_file" id="download_file">
                </div>
                
                <div class="form-group">
                	<label for="download_link">설치 파일 URL</label>
                	<input type="text" class="form-control" name="download_link" id="download_link" value="{{ $item->download_link }}">
                </div>
                <div class="form-group">
                	<label for="store_link">스토어 링크 (예시 <code>https://</code>)</label>
                	<input type="text" class="form-control" name="store_link" id="store_link" value="{{ $item->store_link }}">
                </div>
                			
                <h3 class="pt-5">미리보기</h3>
                <div class="form-group">
                	<label for="preview_file">미리보기 이미지 업로드 (업로드 되어 있는 파일 : {{ $item->preview_file }})</label>
                	<input type="file" class="form-control" name="preview_file" id="preview_file">
                </div>
                <div class="form-group">
                	<label for="preview_link">미리보기 이미지 URL</label>
                	<input type="text" class="form-control" name="preview_link" id="preview_link" value="{{ $item->preview_link }}">
                </div>
			</div>
			<div class="tab-pane fade" id="nav-images" role="tabpanel" aria-labelledby="nav-images-tab">
    			<button type="button" class="btn btn-sm btn-outline-secondary" id="add-screenshot-image">추가</button>
    			<hr>
    			<div id="wrap-screenshot-images">
    				<div class="form-group">
                    	<label for="screenshot_images">파일 업로드 (업로드 되어 있는 파일 : )</label>
                    	<input type="file" class="form-control" name="screenshot_images[]" id="screenshot_images" multiple>
                    </div>
                </div>
                
                @foreach ($item->screenshots as $screenshot)
				<img src="{{ $screenshot->uri }}" width="200px">
                <div class="custom-control custom-checkbox pb-5">
					<input type="checkbox" class="custom-control-input" name="delete_screenshots[]" id="screenshot_{{ $screenshot->id }}" value="{{ $screenshot->id }}">
					<label class="custom-control-label" for="screenshot_{{ $screenshot->id }}">삭제</label>
                </div>
                @endforeach
			</div>
		</div>
	</div>
</div>