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
    protected const VIEW_PATH = 'app.archive.document';
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

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['archive'] = $archive;
        $dataSet ['folder'] = $folder;
        //$dataSet ['folder']->paths_decode = json_decode($folder->path);
        if(isset($folder)) $dataSet ['folder']->paths = $folder->paths();
        $dataSet ['article'] = $document;
        //$dataSet ['previousList'] = $this->makePreviousListLink($request, $archiveId);
        $dataSet ['previousList'] = url()->previous();
        $dataSet ['bookmark'] = $bookmark;

        // 공용 파라미터 처리
        $dataSet ['parameters']['archiveId'] = $archiveId;
        //$dataSet ['parameters']['board'] = $archive->board_id;
        return view ( self::VIEW_PATH . '.show', $dataSet );
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

        /*
        // 권한 체크
        SAArchive::select(['id'])
            ->where ( [['user_id', Auth::id() ],['id',$archiveId]])
            ->firstOrFail ();
        */
        
        $article = new SADocument;
        
        // 게시판 목록을 조회. 셀렉트박스 를 만들기 위함.
        $folderSelectList = $this->getFolderFormSelectList($archiveId);

        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['article'] = $article;
        $dataSet ['parameters']['archive_id'] = $archiveId;
        //$dataSet ['parameters']['board'] = $boardId;
        //$dataSet ['cancelButtonLink'] = $this->makePreviousListLink($request,$profileId);
        $dataSet ['cancelButtonLink'] = url()->previous();
        $dataSet ['selectedBoard'] = $article->folder_id;
        $dataSet ['boardList'] = $folderSelectList;
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

        // 게시판 목록을 조회. 셀렉트박스 를 만들기 위함.
        $folderSelectList = $this->getFolderFormSelectList($archiveId);

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['article'] = $document;
        //$dataSet ['parameters']['profile'] = $archiveId;
        //$dataSet ['cancelButtonLink'] = $this->makePreviousShowLink($request,$profileId, $documentId);
        //$dataSet ['cancelButtonLink'] = url()->previous();
        $dataSet ['cancelButtonLink'] = route(self::ROUTE_ID.'.show', $document->id );
        $dataSet ['selectedBoard'] = $document->folder_id;
        $dataSet ['boardList'] = $folderSelectList;
        return view ( self::VIEW_PATH . '.edit', $dataSet );
        
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
        $title = $request->input ( 'title' );
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
        $article->folder_id = $folderId;
        $article->reference = $reference;
        $article->category = $category;
        $article->save ();
        
        //$this->updateBoardCount();
        /*
        foreach($article->category_array as $item){
            SACategoryDocumentRel::create(['profile_id'=>$profileId,'archive_id'=>$article->id,'category'=>$item]);
        }
        */
        $this->updateCategoryDocumentRel($archiveId, $documentId, $article->category_array);

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
        $title = $request->input ( 'title' );
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
        $beforeCategory = $document->category;
        $document->title = $title;
        $document->content = $content;
        $document->folder_id = $folderId;
        $document->reference = $reference;
        $document->category = $category;
        
        //$this->updateBoardCount();

        /*
        // category 관련 처리
        if($beforeCategory != $category){
            SACategoryDocumentRel::where('archive_id',$archiveId)->delete();

            foreach($document->category_array as $item){
                SACategoryDocumentRel::create(['profile_id'=>$profileId,'archive_id'=>$document->id,'category'=>$item]);
            }
        }
        */
        // Category 와 Document 의 릴레이션 갱신
        if($beforeCategory != $category){
            $this->updateCategoryDocumentRel($document->archive_id, $document->id, $document->category_array);
        }

        $document->save();

        // 결과 처리
        if ($submitAction === 'continue') {
            return redirect()->back()->with('message', '저장되었습니다.');
        }
        return redirect()->route( self::ROUTE_ID.'.show', $article->id)
        ->with('message', '저장되었습니다.' );
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
     * 문서 삭제.
     *
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // 데이터 조회
        $article = SADocument::findOrFail($id);
        
        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($article->archive_id);

        // delete 실행
        $article->delete();
        
        // Category x Document 릴레이션에서 기존의 해당하는 것 제거
        SACategoryDocumentRel::where('document_id',$id)->delete();
        
        //$this->updateBoardCount();
        
        return redirect('/archives/'.$archive->id)
        ->with('message', '삭제되었습니다.' );
    }
    
    
    /**
     * 문서 편집 때 폼에 나타날 '폴더 선택'을 위한 목록
     * 이거 향후 개선될 필요가 있음. 셀렉트박스로는 한계니까..
     */
    protected function getFolderFormSelectList($archiveId){
        // 게시판 목록을 조회. 셀렉트박스 를 만들기 위함.
        $list = SAFolder::select(['id','name','parent_id','depth'])
        ->where ( 'archive_id', $archiveId )
        ->orderBy('index','asc')->get();
        
        return $list;
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

        
    /**
     * boardList 테이블의 count 값을 갱신
     * @deprecated 코드 개선이 필요함.
     */
    private function updateBoardCount()
    {
        /*
        $affected = DB::update('update sa_boards 
            set count = (select count(id) from archives
            where archives.board_id = sa_boards.id
            group by board_id)');
        */
        
        // 좀 더 세밀화 된 쿼리
        DB::update('update sa_boards
            set count = (select count(sa_archives.id)
                from 
                sa_archives,
                (SELECT node.board_id, parent.board_id as parent_id
                    FROM sa_board_tree AS node ,
                             sa_board_tree AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                ) as cate
                WHERE 
                cate.board_id = sa_archives.board_id
                and cate.parent_id = sa_boards.id
                group by cate.parent_id)');
        
    }
    
    /**
     * 이전 링크 주소.
     * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
     * 뭔가 동작이 원하는 느낌이 아니다...살펴봐야 할 듯...
     * 
     * url()->previous()가 바로 직전 주소를 가져오는데, 새로고침을 해버리면 자신의 링크만을 가리키게 된다.
     * 그 경우를 보정하기 위한 메서드이다. 
     * @param Request $rqeust
     * @return string
     */
    protected function makePreviousListLink(Request $request, $archiveId)
    {
        $previous = url()->previous();
        
        $routeLink = route ( self::ROUTE_ID . '.index', ['profile'=> $archiveId]);

        //``

        return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
    }

    /**
     * '취소 링크' 생성.
     */
    protected function makePreviousShowLink(Request $request, $profileId, $archiveId)
    {
        $previous = url()->previous();
        
        $routeLink = route ( self::ROUTE_ID . '.show', ['profile'=> $profileId, 'archive'=>$archiveId]);

        return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
    }

    /**
     * 이전 링크 주소.
     * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
     * 뭔가 동작이 원하는 느낌이 아니다...살펴봐야 할 듯...
     * @param Request $rqeust
     * @return string
     * @deprecated 사용하지 않음.
     */
    protected function getPreviousLink(Request $request)
    {
        $session_previousName = 'devscrap-previousList';
        // 바로 이전 주소가 list 형태의 index 경로라면, flash session 에 저장.
        $request->session()->reflash();
        $request->session()->keep([$session_previousName]);
        
        $previous = url()->previous();
        $previous_identifier = strtok($previous,'?');
        
        // 해당 패턴과 일치하거나 index 의 주소인 경우에 previous 세션에 저장
        if($previous_identifier == route ( self::ROUTE_ID . '.index', ['profile'=>1])){
            $request->session()->flash($session_previousName, $previous);
        }
        
        //session 에 해당 값이 있으면 세션 값 사용. 없으면 목록 주소로 대체.
        return ($request->session()->get($session_previousName,'') != '') ?
        $request->session()->get($session_previousName,'') : route ( self::ROUTE_ID . '.index', ['profile'=>1]);
    }
}