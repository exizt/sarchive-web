<?php

namespace App\Http\Controllers\Archive;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;
use App\Models\SArchive\SADocument;
use App\Models\ArchiveCategoryRel;
use App\Models\ArchiveBookmark;

class DocumentController extends Controller {
    protected const VIEW_PATH = 'app.archive.document';
    protected const ROUTE_ID = 'doc';
    protected $ArchiveProfile;

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
        
        // 권한 체크
        $archive = SAArchive::select(['id', 'name'])
            ->where ( [['user_id', Auth::id() ],['id',$archiveId]])
            ->firstOrFail ();
            
        
        $folder = SAFolder::find($document->folder_id);
        $bookmark = ArchiveBookmark::firstOrNew(['id'=>$documentId]);

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['archive'] = $archive;
        $dataSet ['folder'] = $folder;
        //$dataSet ['folder']->paths_decode = json_decode($folder->path);
        $dataSet ['folder']->paths = $folder->paths();
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
     * 글 생성
     *
     */
    public function create(Request $request) {
        // 유효성 검증
        $validatedData = $request->validate([
            'archive_id' => 'required|integer'
        ]);

        // 파라미터
        $archiveId = $request->input('archive_id');

        // 권한 체크
        SAArchive::select(['id'])
            ->where ( [['user_id', Auth::id() ],['id',$archiveId]])
            ->firstOrFail ();
        
        $article = new Document;
        
        // 게시판 목록을 조회. 셀렉트박스 를 만들기 위함.
        $folderSelectList = $this->getFolderFormSelectList($archiveId);

        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['article'] = $article;
        //$dataSet ['parameters']['board'] = $boardId;
        $dataSet ['parameters']['profile'] = $archiveId;
        //$dataSet ['cancelButtonLink'] = $this->makePreviousListLink($request,$profileId);
        $dataSet ['cancelButtonLink'] = url()->previous();
        $dataSet ['selectedBoard'] = $document->folder_id;
        $dataSet ['boardList'] = $folderSelectList;
        return view ( self::VIEW_PATH . '.create', $dataSet );
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $documentId) {
        //
        $document = SADocument::findOrFail($documentId);
        $archiveId = $document->archive_id;

        // 권한 체크
        SAArchive::select(['id'])
            ->where ( [['user_id', Auth::id() ],['id',$archiveId]])
            ->firstOrFail ();

        // 게시판 목록을 조회. 셀렉트박스 를 만들기 위함.
        $folderSelectList = $this->getFolderFormSelectList($archiveId);

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['article'] = $document;
        $dataSet ['parameters']['profile'] = $archiveId;
        //$dataSet ['cancelButtonLink'] = $this->makePreviousShowLink($request,$profileId, $documentId);
        $dataSet ['cancelButtonLink'] = url()->previous();
        $dataSet ['selectedBoard'] = $document->folder_id;
        $dataSet ['boardList'] = $folderSelectList;
        return view ( self::VIEW_PATH . '.edit', $dataSet );
        
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request        	
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $profileId) {
        $this->setArchiveFromId($profileId);
        //
        $title = $request->input ( 'title' );
        $content = $request->input ( 'content', ' ' );
        $board_id = $request->input ( 'board_id' , '1');
        $reference = $request->input ('reference');
        $category = $request->input ('category');

        // insert row		
        $article = new Document;
        $article->title = $title;
        $article->content = $content;
        $article->board_id = $board_id;
        $article->reference = $reference;
        $article->category = $category;
        $article->profile_id = $profileId;
        $article->save ();
        
        $this->updateBoardCount();
        
        foreach($article->category_array as $item){
            ArchiveCategoryRel::create(['profile_id'=>$profileId,'archive_id'=>$article->id,'category'=>$item]);
        }

        // result processing
        return redirect ()->route ( self::ROUTE_ID . '.show',['profile'=>$profileId, 'archive'=>$article->id] )->withSuccess ( 'New Post Successfully Created.' );
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request        	
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $profileId, $archiveId) {
        $this->getArchiveProfile($request);
        
        $title = $request->input ( 'title' );
        $content = $request->input ( 'content',' ' );
        $board_id = $request->input ( 'board_id' , '1');
        $reference = $request->input ('reference');
        $category = $request->input ('category');

        // saving
        // 있는 값인지 id 체크
        $article = SADocument::findOrFail ( $archiveId );
        $beforeCategory = $article->category;
        $article->title = $title;
        $article->content = $content;
        $article->board_id = $board_id;
        $article->reference = $reference;
        $article->category = $category;
        $article->save ();

        $this->updateBoardCount();

        // category 관련 처리
        if($beforeCategory != $category){
            ArchiveCategoryRel::where('archive_id',$archiveId)->delete();

            foreach($article->category_array as $item){
                ArchiveCategoryRel::create(['profile_id'=>$profileId,'archive_id'=>$article->id,'category'=>$item]);
            }
        }

        // after processing
        if ($request->action === 'continue') {
            return redirect ()->back ()->withSuccess ( 'Post saved.' );
        }
        return redirect ()->route ( self::ROUTE_ID . '.show',['profile'=>$profileId, 'archive'=>$article->id])->withSuccess ( 'Post saved.' );
    }

    /**
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function search(Request $request) {
        $this->getArchiveProfile($request);

        $boardId = $request->input('board', $this->ArchiveProfile->root_board_id);
        $word = $request->input('q','');
        
        if(mb_strlen($word) < 2){
            echo '검색어가 너무 짧음.';
        } else {
            $masterList = SADocument::select(['id', 'title','summary_var','reference','board_id','created_at','updated_at','category','profile_id'
                ,DB::raw('(select name from sa_boards as s where s.id = board_id) as category_name')])
                ->join(DB::raw("(
                select node.board_id as t_board_id, parent.board_id as t_parent_id
                from sa_board_tree AS node ,
                    sa_board_tree AS parent
                where node.lft between parent.lft and parent.rgt
                ) as t"),'t.t_board_id','=','board_id')
                ->where ( 't.t_parent_id',$boardId )
                ->orderBy ( 'created_at', 'desc' )
                ->search($word)
                ->paginate(30);

            // dataSet 생성
            $dataSet = $this->createViewData ();
            $dataSet ['articles'] = $masterList;
            $dataSet ['parameters']['board'] = $boardId;
            $dataSet ['parameters']['profile'] = $this->ArchiveProfile->id;
            $dataSet ['parameters']['q'] = $word;
            $dataSet ['pagParameters']['board'] = $boardId;
            $dataSet ['pagParameters']['profile'] = $this->ArchiveProfile->id;
            $dataSet ['pagParameters']['q'] = $word;
            return view ( self::VIEW_PATH . '.search', $dataSet );
            
        }
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
     * Remove the specified resource from storage.
     *
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function destroy($profileId, $archiveId) {
        $this->setArchiveFromId($profileId);

        $item = SADocument::findOrFail($archiveId);
        $item->delete();
        
        $this->updateBoardCount();
        
        return redirect()
        ->route(self::ROUTE_ID.'.index', ['profile'=>$profileId])
        ->withSuccess('Post deleted.');
    }
    
    /**
     * 
     * @return string[]
     */
    protected function createViewData() {
        $dataSet = array ();
        $dataSet ['ROUTE_ID'] = self::ROUTE_ID;
        $dataSet ['VIEW_PATH'] = self::VIEW_PATH;
        $dataSet ['parameters'] = array();
        return $dataSet;
    }

    
    /**
     * 분기에 따른 처리.
     * '개발 전용' 과 '일반 전용' 으로 구분. 향후에 더 나뉘어질 수 있음. 귀찮으니 하드코딩한다. 
     */
    private function getArchiveProfile(Request $request)
    {
        $userId = Auth::id();

        if($request->has('profile')){
            $profileId = $request->input('profile');
            // routeId 를 이용한 접근
            //$this->ArchiveProfile = SAArchive::select(['name','root_board_id','route'])->where ( [['user_id', $userId ],['route',$ArchiveRouteId]])->firstOrFail ();
    
            // profileId 를 이용한 접근
            $this->ArchiveProfile = SAArchive::select(['id','name','root_board_id','route'])
                ->where ( [['user_id', $userId ],['id',$profileId]])
                ->firstOrFail ();
            
        } else {
            $this->ArchiveProfile = SAArchive::select(['id','name','root_board_id','route'])
            ->where ( [['user_id', $userId ],['is_default','1']])
            ->firstOrFail ();
        }
    }

        
    /**
     * Archive Profile 의 정보를 조회. 
     * this->ArchiveProfile 에 내용이 담긴다.
     * profileId 를 인수로 받고, userID 를 통하여 일차적으로 인증을 한다.
     * 해당 profile Id 에 접근 권한이 있는지 체크하게 됨. 권한이 없으면 Fail
     */
    private function setArchiveFromId($profileId)
    {
        $userId = Auth::id();
        // profileId 를 이용한 접근
        $this->ArchiveProfile = SAArchive::select(['id','name','root_board_id','route'])
            ->where ( [['user_id', Auth::id() ],['id',$profileId]])
            ->firstOrFail ();
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