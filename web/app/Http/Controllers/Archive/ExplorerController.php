<?php

namespace App\Http\Controllers\Archive;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;
use App\Models\SArchive\SADocument;
use App\Models\SArchive\SACategory;
use App\Models\SArchive\SACategoryRel;


class ExplorerController extends BaseController {
    protected const VIEW_PATH = 'app.explorer';
    protected const ROUTE_ID = 'archives';


    /**
     * 현재 보고 있는 Archive 개체
     */
    protected $archive = null;

    protected $docColumns = ['sa_documents.id', 'title','summary_var',
    'reference','folder_id','category',
    'sa_documents.created_at','sa_documents.updated_at'];


    /**
     * 생성자
     */
    public function __construct() {
        $this->middleware ( 'auth' )->except(['doAjax_getFolderNav','doAjax_getHeaderNav']);
    }


    /**
     * 'archive_id' 기준으로 문서 조회
     *
     */
    public function showDocsByArchive(Request $request, $archiveId){
        // 파라미터
        $is_only = (bool)$request->input( 'only' , false);

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // 아카이브의 문서 조회
        $qry = SADocument::select($this->docColumns)
            ->where ( 'archive_id',$archiveId )
            ->with ('folder');

        if($is_only) $qry = $qry->whereNull('folder_id');

        $masterList = $qry->orderBy ( 'created_at', 'desc' )->paginate(15);

        // dataSet 생성
        $viewData = $this->createViewData ();
        $viewData['masterList'] = $masterList;
        $viewData['archive'] = $archive;
        if($is_only) $viewData['paginationParams']['only'] = true;
        return view ( self::VIEW_PATH . '.index', $viewData );
    }


    /**
     * 'folder_id'기준으로 문서 조회
     *
     */
    public function showDocsByFolder(Request $request, $folderId) {

        $folder = SAFolder::findOrFail($folderId);
        $archiveId = $folder->archive_id;
        //$archive = $folder->archive;

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

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
                ->paginate(15);
                //print_r(DB::getQueryLog());
        } else {
            /**
             * 해당 폴더에만 해당하는 게시물 목록 조회 (하위 폴더 제외함)
             */
            $masterList = SADocument::select($this->docColumns)
            ->where ( 'folder_id',$folder->id )
            ->with ('folder')
            ->orderBy ( 'created_at', 'desc' )
            ->paginate(15);
        }

        // viewData 생성
        $viewData = $this->createViewData ();
        $viewData['masterList'] = $masterList;
        $viewData['folder'] = $folder;
        $viewData['folder']->paths = $folder->paths();
        $viewData['archive'] = $archive;
        $viewData['bodyParams']['folder'] = $folderId;
        $viewData['parameters']['folder'] = $folder;
        if($is_only) $viewData['paginationParams']['only'] = true;
        return view ( self::VIEW_PATH . '.index', $viewData );
    }




    /**
     * 카테고리에 해당하는 문서 목록 및 카테고리 정보
     *
     */
    public function showDocsByCategory(Request $request, $archiveId, $encodedName) {
        // 카테고리명
        $categoryName = urldecode($encodedName);

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // 아카이브 카테고리 (조회하고 없으면 새로 insert함)
        $category = SACategory::firstOrCreate(['archive_id'=>$archiveId, 'name'=>$categoryName]);

        // 이 분류에 속하는 문서 목록을 조회
        $masterList = SADocument::select([
            'sa_documents.id', 'sa_documents.title','sa_documents.summary_var',
            'sa_documents.reference','sa_documents.folder_id','sa_documents.category',
            'sa_documents.created_at','sa_documents.updated_at'])
              ->join("sa_category_document_rel as rel",'rel.document_id','=','sa_documents.id')
            ->where ( 'rel.category_name',$categoryName )
            ->orderBy ( 'sa_documents.created_at', 'desc' )
            ->paginate(15);

        // 하위 카테고리 조회
        $childCategories = SACategoryRel::where('category_name',$categoryName)
            ->orderBy('child_category_name')
            ->pluck('child_category_name');

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
        $dataSet ['archive'] = $archive;
        $dataSet ['category'] = $category;
        $dataSet ['childCategories'] = $childCategories;
        $dataSet ['parameters']['category'] = $categoryName;
        return view ( self::VIEW_PATH . '.category', $dataSet );
    }



    /**
     * 검색 결과
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function search(Request $request, $archiveId) {

        // 유효성 검증
        /*
        $validatedData = $request->validate([
            'archive_id' => 'required|integer'
        ]);
        */

        // 파라미터
        //$archiveId = $request->input('archiveId');
        $word = $request->input('q','');


        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);


        if(mb_strlen($word) < 2){
            echo '검색어가 너무 짧음.';
        } else {
            $masterList = SADocument::select($this->docColumns)
                ->where ( 'archive_id',$archiveId )
                ->orderBy ( 'created_at', 'desc' )
                ->search($word)
                ->paginate(30);

            // dataSet 생성
            $dataSet = $this->createViewData ();
            $dataSet ['masterList'] = $masterList;
            $dataSet ['parameters']['q'] = $word;
            $dataSet ['paginationParams']['q'] = $word;
            return view ( self::VIEW_PATH . '.search', $dataSet );
        }
    }




    public function folderSelector(Request $request){
        $archiveId = $request->input('archive');
        $excluded = $request->input('excluded');
        $folderIdReturn = $request->input('idReturn');
        $folderNameReturn = $request->input('nameReturn');

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);


        $dataSet = $this->createViewData ();
        if(!empty($excluded) && $excluded != 'undefined'){
            $dataSet['bodyParams']['excluded'] = $excluded;
        }
        if(!empty($folderIdReturn) && $folderIdReturn != 'undefined'){
            $dataSet['bodyParams']['folder-id-of-parent'] = $folderIdReturn;
        }
        if(!empty($folderNameReturn) && $folderNameReturn != 'undefined'){
            $dataSet['bodyParams']['folder-name-of-parent'] = $folderNameReturn;
        }
        return view ( self::VIEW_PATH . '.folder-selector', $dataSet );
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

        $masterList = SAFolder::select(['id','name','parent_id','depth'])
        ->where('depth','1')
        ->where('archive_id', $archiveId)
        ->orderBy('index','asc')->get();

        $dataSet['list'] = $masterList;
        return response()->json($dataSet);
    }


    public function doAjax_getChildFolder(Request $request){
        // 파라미터
        $archiveId = $request->input('archive_id');
        $folderId = $request->input('folder_id');

        $cols = ['id','name as text','parent_id as parent','depth'];
        if(!empty($archiveId)){
            $folders = SAFolder::getRootsByArchive($archiveId, $cols);

            if(!empty($folders)){
                foreach($folders as $k => $v){
                    $item = $v;
                    if($item->parent == '0'){
                        $item->parent = '#';
                    }
                    $folders[$k] = $item;
                }
            }
        } else {
            $folders = SAFolder::getChildFolders($folderId, $cols);
        }


        $dataSet['list'] = $folders;
        return response()->json($folders);
    }

    /**
     * 아카이브 화면에서 nav 메뉴를 가져오는 Ajax 부분.
     */
    public function doAjax_getFolderNav(Request $request){
        $mode = 'folder';

        // 유효성 검증
        if($request->has('folder_id') && !empty($request->input('folder_id'))){
            // folder_id 값이 있을 때 비어있지 않아야 함.
            $validatedData = $request->validate([
                'archive_id' => 'required|integer',
                'folder_id' => 'required|integer'
            ]);
            // 파라미터
            $archiveId = $request->input('archive_id');
            $folderId = $request->input('folder_id');
        } else if($request->has('archive_id') && !empty($request->input('archive_id'))){
            // folder_id 값이 없으면서 (선행 조건에서 걸러짐)
            // archive_id 값이 있을 때 비어있지 않아야 함.
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

        // Archive의 Id에 대한 권한 보유 여부를 조사
        $archive = SAArchive::select(['id', 'name'])
            ->where ( [['user_id', Auth::id() ],['id',$archiveId]])
            ->firstOrFail ();

        if($mode == 'folder'){
            // 선택된 폴더의 정보
            $currentFolder = SAFolder::select(['id','name','parent_id','depth', 'system_path', 'doc_count'])
                ->where ( 'id', $folderId )
                ->firstOrFail ();

            // 하위 폴더 목록
            // 노드의 부모 p2, p3, p4를 left join하여 만든 후 탐색을 하는 쿼리.
            $masterList = DB::select("select
                        n.parent_id as parent_id,
                        n.id,
                        n.name,
                        n.doc_count_all as count,
                        n.depth,
                        n.system_path
            from        sa_folders n
            left join   sa_folders p1 on p1.id = n.parent_id
            left join   sa_folders p2 on p2.id = p1.parent_id
            left join   sa_folders p3 on p3.id = p2.parent_id
            where       ? in (n.parent_id,
                p1.parent_id,
                p2.parent_id,
                p3.parent_id)
            order by    n.depth, n.index;",[$folderId]);


        } else {
            // 아카이브의 하위 폴더 목록
            // 3단계까지만 조회하게 제한함.
            $masterList = DB::select("select
                                n.parent_id as parent_id,
                                n.id,
                                n.name,
                                n.doc_count_all as count,
                                n.depth,
                                n.system_path
            from        sa_folders n
            left join   sa_folders p1 on p1.id = n.parent_id
            left join   sa_folders p2 on p2.id = p1.parent_id
            left join   sa_folders p3 on p3.id = p2.parent_id
            where       n.archive_id = ?
            and         n.depth <= 3
            order by    n.depth, n.index;",[$archiveId]);
            //order       by p1.index, p2.index, p3.index, p4.index, p5.index, p1.id;
        }

        $dataSet = array();
        if(isset($currentFolder)){
            $dataSet['currentFolder'] = $currentFolder;
            // $dataSet['currentPaths'] = $currentPaths;
        }
        $dataSet['archive'] = $archive;
        $dataSet['list'] = $masterList;
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
        $data ['paginationParams'] = array();

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
     * 이전 링크 주소.
     * 바로 이전 주소를 가지고 셋팅을 하는데, '새로고침' 을 하는 경우도 있기 때문에, 세션에 넣어두고 활용한다.
     * 뭔가 동작이 원하는 느낌이 아니다...살펴봐야 할 듯...
     * @param Request $rqeust
     * @return string
     * @deprecated
     */
    protected function makePreviousListLink(Request $request, $profileId)
    {
        $previous = url()->previous();

        $routeLink = route ( self::ROUTE_ID . '.index', ['profile'=> $profileId]);

        return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
    }


    /**
     * '취소 링크' 생성.
     * @deprecated
     */
    protected function makePreviousShowLink(Request $request, $profileId, $archiveId)
    {
        $previous = url()->previous();

        $routeLink = route ( self::ROUTE_ID . '.show', ['profile'=> $profileId, 'archive'=>$archiveId]);

        return (strtok($previous,'?') == $routeLink) ? $previous : $routeLink;
    }
}
