<?php

namespace App\Http\Controllers\Archive;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;
use App\Models\SArchive\SADocument;
use App\Models\ArchiveCategoryRel;
use App\Models\ArchiveBookmark;

class ArchiveController extends BaseController {
    protected const VIEW_PATH = 'app.archive';
    protected const ROUTE_ID = 'archives';
    protected $ArchiveProfile;
    protected $docColumns = ['sa_documents.id', 'title','summary_var','reference','folder_id','sa_documents.created_at','sa_documents.updated_at','category'];


    /**
     * 생성자
     */
    public function __construct() {
        $this->middleware ( 'auth' )->except(['doAjax_getBoardList','doAjax_getHeaderNav']);
    }


    /**
     * 'archive_id' 기준으로 문서 조회
     * 
     */
    public function retrieveDocsByArchive(Request $request, $archiveId){
        $this->setArchiveFromId($archiveId);

        // 아카이브의 문서 조회
        $masterList = SADocument::select($this->docColumns)
            ->where ( 'archive_id',$archiveId )
            ->with ('folder')
            ->orderBy ( 'created_at', 'desc' )
            ->paginate(20);


        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
        $dataSet ['bodyParams']['archive'] = $archiveId;
        return view ( self::VIEW_PATH . '.index', $dataSet );
    }


    /**
     * 'folder_id'기준으로 문서 조회
     * 
     */
    public function retrieveDocsByFolder(Request $request, $folderId) {
        //$this->setArchiveFromId($archiveId);

        $folder = SAFolder::findOrFail($folderId);
        $archive = $folder->archive;

        // 해당 폴더에 해당하는 것만 조회하는 옵션. false 일 때에는 하위 폴더까지 조회.
        $is_only = (bool)$request->input( 'only' , false);


        if(! $is_only){
            /**
             * 하위 폴더까지 게시물 목록 조회
             */
            //DB::enableQueryLog();
            $folder_path = $folder->system_path;

            $masterList = SADocument::select($this->docColumns)
                ->join('sa_folders','sa_documents.folder_id','=','sa_folders.id')
                ->whereRaw ( 'left(system_path, length(?)) = ?', [$folder_path, $folder_path] )
                ->with ('folder')
                ->orderBy ( 'created_at', 'desc' )
                ->paginate(20);
                //print_r(DB::getQueryLog());
        } else {
            /**
             * 해당 폴더에만 해당하는 게시물 목록 조회 (하위 폴더 제외함)
             */
            $masterList = SADocument::select($this->docColumns)
            ->where ( 'folder_id',$folder->id )
            ->with ('folder')
            ->orderBy ( 'created_at', 'desc' )
            ->paginate(20);
        }

        //$archiveBoard = SAFolder::select(['name', 'comment'])->where ( 'id', $boardId )->firstOrFail ();
        //$categoryName = SAFolder::select('name')->where ( 'id', $boardId )->firstOrFail ()->name;
        
        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
        $dataSet ['bodyParams']['archive'] = $archive->id;
        $dataSet ['bodyParams']['folder'] = $folderId;
        if($is_only) $dataSet ['mPaginationParams']['only'] = true;
        return view ( self::VIEW_PATH . '.index', $dataSet );

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
     * 상단 네비게이션 아이템 조회
     */
    public function doAjax_getHeaderNav(Request $request){

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

        //$archiveBoardList = SAFolder::find($article->board_id);
        $masterList = $archiveBoardList = SAFolder::select(['id','name','parent_id','depth'])
        //->where([['profile_id',$this->ArchiveProfile->id],['depth','2']])
        ->where('depth','1')
        ->where('archive_id', $archiveId)
        ->orderBy('index','asc')->get();

        $dataSet['list'] = $masterList;
        return response()->json($dataSet);
    }


    /**
     * 아카이브 화면에서 nav 메뉴를 가져오는 Ajax 부분.
     */
    public function doAjax_getFolderNav(Request $request){
        $mode = 'folder';
        

        if($request->has('folder_id') && !empty($request->input('folder_id'))){
            // 유효성 검증
            $validatedData = $request->validate([
                'archive_id' => 'required|integer',
                'folder_id' => 'required|integer'
            ]);
            // 파라미터
            $archiveId = $request->input('archive_id');
            $folderId = $request->input('folder_id');
        } else if($request->has('archive_id') && !empty($request->input('archive_id'))){
            // 유효성 검증
            $validatedData = $request->validate([
                'archive_id' => 'required|integer'
            ]);
            // 파라미터
            $archiveId = $request->input('archive_id');
            $mode = 'archive';
        } else {
            // 에러
            abort(500, 'parameters wrong.');
        }

        // 권한 체크 및 조회
        $archive = SAArchive::select(['id', 'name'])
            ->where ( [['user_id', Auth::id() ],['id',$archiveId]])
            ->firstOrFail ();

        if($mode == 'folder'){
            // 현재의 폴더 정보
            $currentFolder = SAFolder::select(['id','name','parent_id','depth', 'system_path'])
                ->where ( 'id', $folderId )
                ->firstOrFail ();
            //$currentPath = $currentFolder->system_path;
            //$currentPaths = $this->getPathsFromIds($currentFolder->system_path);
            $currentPaths = $currentFolder->paths();


            // 하위 폴더 목록
            $masterList = DB::select("select      p6.parent_id as parent6_id,
                    p5.parent_id as parent5_id,
                    p4.parent_id as parent4_id,
                    p3.parent_id as parent3_id,
                    p2.parent_id as parent2_id,
                                p1.parent_id as parent_id,
                                p1.id,
                    p1.name,
                                p1.doc_count,
                                p1.depth,
                                p1.system_path
            from        sa_folders p1
            left join   sa_folders p2 on p2.id = p1.parent_id 
            left join   sa_folders p3 on p3.id = p2.parent_id 
            left join   sa_folders p4 on p4.id = p3.parent_id  
            left join   sa_folders p5 on p5.id = p4.parent_id  
            left join   sa_folders p6 on p6.id = p5.parent_id
            where       ? in (p1.parent_id, 
                        p2.parent_id, 
                        p3.parent_id, 
                        p4.parent_id, 
                        p5.parent_id, 
                        p6.parent_id)
            order       by p1.index, p2.index, p3.index, p4.index, p5.index, 7;",[$folderId]);
            
            

        } else {
            // 하위 폴더 목록
            $masterList = DB::select("select      p6.parent_id as parent6_id,
                    p5.parent_id as parent5_id,
                    p4.parent_id as parent4_id,
                    p3.parent_id as parent3_id,
                    p2.parent_id as parent2_id,
                                p1.parent_id as parent_id,
                                p1.id,
                    p1.name,
                                p1.doc_count,
                                p1.depth,
                                p1.system_path
            from        sa_folders p1
            left join   sa_folders p2 on p2.id = p1.parent_id 
            left join   sa_folders p3 on p3.id = p2.parent_id 
            left join   sa_folders p4 on p4.id = p3.parent_id  
            left join   sa_folders p5 on p5.id = p4.parent_id  
            left join   sa_folders p6 on p6.id = p5.parent_id
            where       p1.archive_id = ?
            order       by p1.index, p2.index, p3.index, p4.index, p5.index, 7;",[$archiveId]);
        }

        $dataSet = array();
        if(isset($currentFolder)){
            $dataSet['currentFolder'] = $currentFolder;
            $dataSet['currentPaths'] = $currentPaths;
        }
        $dataSet['archive'] = $archive;
        $dataSet['list'] = $masterList;
        return response()->json($dataSet);
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
     * 
     * @return string[]
     */
    protected function createViewData() {
        $data = array ();
        $data ['ROUTE_ID'] = self::ROUTE_ID;
        $data ['VIEW_PATH'] = self::VIEW_PATH;
        $data ['parameters'] = array();
        $data ['paginationParams'] = array();
        return $data;
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
        $this->ArchiveProfile = SAArchive::select(['id','name','route'])
            ->where ( [['user_id', Auth::id() ],['id',$profileId]])
            ->firstOrFail ();
    }

    
    /**
     * boardList 테이블의 count 값을 갱신
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
     * @param Request $rqeust
     * @return string
     */
    protected function makePreviousListLink(Request $request, $profileId)
    {
        $previous = url()->previous();
        
        $routeLink = route ( self::ROUTE_ID . '.index', ['profile'=> $profileId]);

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