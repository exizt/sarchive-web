<?php

namespace App\Http\Controllers\Archive;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;
use App\Models\SArchive\SADocument;
use App\Models\SArchive\SACategoryDocumentRel;
use App\Models\ArchiveBookmark;

class DocumentController extends Controller {
    protected const VIEW_PATH = 'app.document';
    protected const ROUTE_ID = 'doc';

    /**
     * 현재 보고 있는 Archive 개체
     */
    protected $archive = null;


    /**
     * 생성자
     */
    public function __construct() {
        $this->middleware ( 'auth' );
    }


    /**
     * 글 본문 읽기
     *
     */
    public function show(Request $request, $documentId) {

        // 문서 조회
        $document = SADocument::findOrFail($documentId);

        $archiveId = $document->archive_id;

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        $folder = SAFolder::find($document->folder_id);
        $bookmark = ArchiveBookmark::firstOrNew(['id'=>$documentId]);

        // create viewData
        $viewData = $this->createViewData ();
        $viewData ['archive'] = $archive;
        $viewData ['folder'] = $folder;
        //$viewData ['folder']->paths_decode = json_decode($folder->path);
        if(isset($folder)) $viewData ['folder']->paths = $folder->paths();
        $viewData ['article'] = $document;
        $viewData ['previousLink'] = url()->previous();
        $viewData ['bookmark'] = $bookmark;

        // 공용 파라미터 처리
        $viewData ['parameters']['archiveId'] = $archiveId;

        $linkParams = $this->buildLinkParams($request);
        $actionLinks = (object)[];
        $actionLinks->edit = route(self::ROUTE_ID.'.edit',array_merge($linkParams,['doc'=>$document->id]));
        // list 링크 생성
        $actionLinks->list = $this->generatePreviousListLink($archive->id, $linkParams);

        $viewData ['actionLinks'] = $actionLinks;
        return view ( self::VIEW_PATH . '.show', $viewData );
    }




    /**
     * 문서 생성
     *
     * archive_id 파라미터를 필수로 한다.
     */
    public function create(Request $request) {

        // 유효성 검증
        $validatedData = $request->validate([
            'archive' => 'required|integer'
        ]);

        // 파라미터
        $archiveId = $request->input('archive');

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // document 개체 생성
        $article = new SADocument;

        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['article'] = $article;
        $dataSet ['parameters']['archive_id'] = $archiveId;
        return view ( self::VIEW_PATH . '.create', $dataSet );
    }



    /**
     * 문서 편집
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $documentId) {

        // 문서 내용 조회
        $document = SADocument::findOrFail($documentId);
        $archiveId = $document->archive_id;

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // data 생성
        $viewData = $this->createViewData ();
        $viewData ['article'] = $document;

        $linkParams = $this->buildLinkParams($request);
        $actionLinks = (object)[];
        $actionLinks->cancel = route(self::ROUTE_ID.'.show', array_merge($linkParams,['doc'=>$document->id]) );

        $viewData ['actionLinks'] = $actionLinks;
        return view ( self::VIEW_PATH . '.edit', $viewData );


    }


    /**
     * 문서 생성 > 저장
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        // 유효성 검증
        $validatedData = $request->validate([
            'archive_id' => 'required|integer'
        ]);

        /**
         * 파라미터
         */
        $archiveId = $request->input ( 'archive_id');
        // 내용 관련 파라미터
        $title = $request->input ( 'title') ?? '제목 없음';
        $content = $request->input ( 'content', ' ' );
        // 문서 분류 관련 파라미터
        $folderId = $request->input ( 'folder_id');
        $reference = $request->input ('reference');
        $category = $request->input ('category');
        // 저장 옵션
        $submitAction = $request->input ('action');

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // 데이터 insert
        $article = new SADocument;
        $article->title = $title;
        $article->content = $content;
        $article->archive_id = $archiveId;

        if($folderId != null){
            $folder = SAFolder::findOrFail($folderId);
            $article->folder_id = $folderId;
        }
        $article->reference = $reference;
        $article->save ();

        // folder 의 문서 수 변경.
        if(isset($folder)){
            $this->updateFolderDocCount($folderId);
        }

        // 카테고리 입력. 값의 길이가 3이상일 때 입력.
        if($category!=null && strlen($category) >= 3){
            // 값 입력
            $article->category = $category;

            // Category 와 Document 의 릴레이션 갱신
            $this->updateCategoryDocumentRel($archiveId, $article->id, $article->category_array);
        }

        // 결과 처리
        if ($submitAction === 'continue') {
            return redirect()->route ( self::ROUTE_ID . '.edit', $article->id )
            ->with('message', '저장되었습니다.' );
        }
        return redirect()->route ( self::ROUTE_ID . '.show', $article->id )
        ->with('message', '저장되었습니다.' );
    }


    /**
     * 문서 편집 > 저장
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        /**
         * 파라미터
         */
        // 내용 관련 파라미터
        $title = $request->input ( 'title') ?? '제목 없음';
        $content = $request->input ( 'content',' ' );
        // 문서 분류 관련 파라미터
        $folderId = $request->input ( 'folder_id');
        $reference = $request->input ('reference');
        $category = $request->input ('category');
        // 저장 옵션
        $submitAction = $request->input ('action');

        // 문서 데이터 조회
        $document = SADocument::findOrFail ( $id );

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($document->archive_id);

        // 데이터 update
        $document->title = $title;
        $document->content = $content;
        $document->reference = $reference;

        // folderId 의 유효성 체크 및 처리
        $changedFolderIds = array();
        if($folderId != null){
            // 변경 전의 folder_id
            $beforeFolderId = $document->folder_id;

            // folderId 가 0이면 folder 분류에서 제외시키겠다는 것을 가정한다.
            if($folderId == 0){
                /// 폴더에서 제외시킬 때

                $document->folder_id = null;

                // 기존의 folder 에서 카운트 변경
                $changedFolderIds[] = $beforeFolderId;

            } else if($beforeFolderId != $folderId){
                /// 폴더 변경이 이루어졌을 때
                // 새로운 folderId가 유효한지 체크
                $folder = SAFolder::findOrFail($folderId);

                // 값의 변경
                $document->folder_id = $folderId;

                if(!empty($beforeFolderId)) $changedFolderIds[] = $beforeFolderId;
                $changedFolderIds[] = $folderId;
            }
        }

        // category 갱신
        if($document->category != $category){
            // 값 변경
            $document->category = $category;

            // Category 와 Document 의 릴레이션 갱신
            $this->updateCategoryDocumentRel($document->archive_id, $document->id, $document->category_array);
        }

        // 저장 처리
        $document->save();

        // folder 의 문서 수 변경.
        if(!empty($changedFolderIds)){
            foreach($changedFolderIds as $curFolderId){
                $this->updateFolderDocCount($curFolderId);
            }
        }


        // 결과 처리
        if ($submitAction === 'continue') {
            return redirect()->back()->with('message', '저장되었습니다.');
        } else {
            $linkParams = $this->buildLinkParamsByUrl(url()->previous());

            return redirect()->route( self::ROUTE_ID.'.show', array_merge($linkParams,['doc'=>$document->id]))
            ->with('message', '저장되었습니다.' );
        }
    }


    /**
     * 문서 삭제.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // 데이터 조회
        $article = SADocument::findOrFail($id);
        $folderId = $article->folder_id;

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($article->archive_id);

        // delete 실행
        $article->delete();

        // Category x Document 릴레이션에서 기존의 해당하는 것 제거
        SACategoryDocumentRel::where('document_id',$id)->delete();

        // folder 의 문서 수 변경.
        $this->updateFolderDocCount($folderId);

        $linkParams = $this->buildLinkParamsByUrl(url()->previous());
        $previousListLink = $this->generatePreviousListLink($archive->id, $linkParams);

        // @todo 폴더의 게시물 목록 or 아카이브의 게시물 목록으로 이동
        return redirect($previousListLink)
        ->with('message', '삭제되었습니다.' );
    }



    /**
     * bookmark, favorite 기능 구현
     */
    public function doAjax_mark(Request $request){

        $archiveId = $request->input('archive');
        //$profileId = $request->input('profile_id');
        $mode = $request->input('mode');

        $archive = SADocument::findOrFail($archiveId);
        $profileId = $archive->profile_id;

        $item = ArchiveBookmark::firstOrNew(
            ['archive_id' => $archiveId]
        );

        if($mode == 'bookmark'){
            if($item->is_bookmark){
                $item->is_bookmark = false;
            } else {
                $item->is_bookmark = true;
            }
        } else if($mode == 'favorite'){
            if($item->is_favorite){
                $item->is_favorite = false;
            } else {
                $item->is_favorite = true;
            }
        }
        $item->profile_id = $profileId;
        $item->save();


        $dataSet = array();
        $dataSet ['data'] = [
            'archive' => $archiveId,
            'is_bookmark' => ($item->is_bookmark)? 1: 0,
            'is_favorite' => ($item->is_favorite)? 1:0
        ];
        $dataSet['success'] = '변경 완료되었습니다.';

        $request->session()->flash('message', '변경 완료되었습니다.');

        return response()->json($dataSet);
    }



    /**
     * 목록으로 돌아가는 링크를 생성.
     * 일반적인 경우는 route메소드에 인자값만 추가로 넘겨주면 되지만,
     * 목록으로 돌아가는 링크는 제각기 다르므로 이 메서드에서 정의해준다.
     */
    private function generatePreviousListLink($archiveId, $linkParams){
        $params = array();

        if(isset($linkParams['lfolder'])){
            // 폴더에 의한 최신 게시물
            $explorerRouteId = 'explorer.folder';
            $params['id'] = $linkParams['lfolder'];
        } else if(isset($linkParams['lcategory'])){
            // 카테고리에 의한 최신 게시물
            $explorerRouteId = 'explorer.category';
            $params['archive'] = $archiveId;
            $params['category'] = $linkParams['lcategory'];
            // 카테고리 접근
            //$cat = urlencode($params['lcategory']);
        } else {
            // 아카이브의 최신 게시물(기본값)
            $explorerRouteId = 'explorer.archive';
            $params['archive'] = $archiveId;
        }
        $link = route($explorerRouteId, $params);
        return $link;
    }



    /**
     * Request를 통해서 링크에 이용될 파라미터 배열을 생성.
     *
     * @return array
     */
    private function buildLinkParams(Request $request){
        $parameters = array();
        $paramKeys = ['larchive','lcategory','lfolder'];
        foreach($paramKeys as $pKey){
            $value = $request->input($pKey);
            if(!empty($value)){

                $parameters[$pKey] = $value;
            }
        }

        return $parameters;
    }



    /**
     * URL 문자열을 통해서 링크에 이용될 파라미터 배열을 생성한다.
     *
     * @return array
     */
    private function buildLinkParamsByUrl($url){
        $allowed_keys = ['lcategory','lfolder','larchive'];
        $matches = array();

        // query 부분을 배열화
        $parts = parse_url($url);
        if(!empty($parts['query'])){
            parse_str($parts['query'], $queryArray);

            // 허용된 것만 처리
            $matches = array_intersect_key($queryArray, array_flip($allowed_keys));
        }
        return $matches;
    }



    /**
     * Category 와 Document 의 릴레이션 갱신
     */
    private function updateCategoryDocumentRel($archiveId, $documentId, $categoryNames){
        //if(!is_array($categoryNames)) return;

        // 기존의 해당하는 것 제거
        SACategoryDocumentRel::where('document_id',$documentId)->delete();

        if(count($categoryNames) > 0){
            // insert 할 데이터 생성
            $datas = array();
            foreach($categoryNames as $k => $categoryName){
                if(strlen(trim($categoryName))>0){
                    $datas[$k] = [
                        'archive_id' => $archiveId,
                        'category_name' => trim($categoryName),
                        'document_id' => $documentId,
                        'created_at' => Carbon::now()
                    ];
                }
            }

            // 대량 할당
            if(count($datas)>0){
                SACategoryDocumentRel::insert($datas);
            }
        }
    }



    /**
     * id를 통한 archive 조회 및 권한 체크
     * @param int $id 아카이브 Id
     */
    private function retrieveAuthArchive($id){

        $this->archive = SAArchive::select(['id','name','route'])
            ->where ( [['user_id', Auth::id() ],['id',$id]])
            ->firstOrFail ();
        return $this->archive;
    }



    /**
     * folder 테이블의 doc_count 값을 갱신
     */
    private function updateFolderDocCount($folderId)
    {
        if(empty($folderId)) return;
        SAFolder::updateDocCountAll($folderId);
    }



    /**
     *
     * @return string[]
     */
    protected function createViewData() {
        $data = array ();
        $data ['ROUTE_ID'] = self::ROUTE_ID;
        $data ['VIEW_PATH'] = self::VIEW_PATH;
        $data ['parameters'] = array();

        $layoutParams = array();
        $bodyParams = array();
        if(isset($this->archive) && $this->archive != null){
            $layoutParams['archiveId'] = $this->archive->id;
            $layoutParams['archiveName'] = $this->archive->name;
            $bodyParams['archive'] = $this->archive->id;
        }
        $data ['layoutParams'] = $layoutParams;
        $data ['bodyParams'] = $bodyParams;
        return $data;
    }
}
