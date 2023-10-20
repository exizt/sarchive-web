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
use App\App\ListLinker;

/**
 *
 */
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

        // view 처리
        return $this->renderDocumentListView($request, [
            'masterList' => $masterList,
            'archive' => $archive,
            'is_only' => $is_only
        ]);

        // view 호출
        // return view( self::VIEW_PATH.'.index', $viewData );
    }

    private function makeQueryString($arr): string{
        return http_build_query($arr);
    }


    /**
     * 'folder_id'기준으로 문서 조회
     *
     */
    public function showDocsByFolder(Request $request, $folderId) {
        // 해당 폴더에 해당하는 것만 조회하는 옵션. false 일 때에는 하위 폴더까지 조회.
        $is_only = (bool)$request->input( 'only' , false);

        // folder 정보 조회
        $folder = SAFolder::findOrFail($folderId);
        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($folder->archive_id);

        if( !$is_only ){
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

        // view 처리
        return $this->renderDocumentListView($request, [
            'masterList' => $masterList,
            'archive' => $archive,
            'folder' => $folder,
            'is_only' => $is_only
        ]);
    }

    /**
     * Url 진입점인 showDocsByArchive, showDocsByFolder, showDocsByCategory의 공통적인 부분을 추리고
     * view로 전달하는 과정을 동일하게 함.
     */
    protected function renderDocumentListView(Request $request, $data){
        $archive = $data['archive'];
        $folder = isset($data['folder']) ? $data['folder'] : null;
        $is_only = isset($data['is_only']) ? $data['is_only'] : null;
        $masterList = isset($data['masterList']) ? $data['masterList'] : null;

        // viewData 생성
        $viewData = $this->createViewData ();
        $viewData['archive'] = $archive;
        if ($folder) {
            $viewData['folder'] = $folder;
            $viewData['folder']->paths = $folder->paths();
            $viewData['bodyParams']['folder'] = $folder->id;
            $viewData['parameters']['folder'] = $folder;
        }
        if($is_only) $viewData['paginationParams']['only'] = true;
        $viewData['masterList'] = $masterList;

        // actionLinks
        $actionLinks = (object)[];
        // 액션 folders.create, doc.create 등에서 사용되는 파라미터.
        $actionParams = ['archive'=>$archive->id];
        if($folder) {
            $actionParams['folder'] = $folder->id;
        }
        // 새 폴더, 새 문서에서는 archive, folder, category 정도의 정보면 충분하다.
        $actionLinks->new_doc = route('doc.create', $actionParams, false);
        $actionLinks->new_folder = route('folders.create', $actionParams, false);

        // 문서 목록에서 활용될 링크 파라미터. 링크에 따라붙이기 위한 목적으로만 사용되는 파라미터. page 등.
        $trackedLinkParams = ListLinker::getLinkParameters($request, ['lpage'=>'page']);
        // $trackedLinkParams['archive'] = $archive->id;
        if($folder) {
            $trackedLinkParams['lfolder'] = $folder->id;
        }

        $viewData['actionLinks'] = $actionLinks;
        $viewData['trackedLinkParams'] = $trackedLinkParams;


        // view 호출
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

    /**
     * 폴더 선택
     */
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
     * 하위 폴더를 조회
     */
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

            $folder_path = $currentFolder->system_path;
            $max_depth = $currentFolder->depth + 2;

            // 하위 폴더 목록
            $masterList = SAFolder::select(['id','name','parent_id','depth', 'system_path', 'doc_count', 'doc_count_all as count'])
                ->whereRaw ( 'left(system_path, length(?)) = ?', [$folder_path, $folder_path] )
                ->where ('id', '!=', $currentFolder->id)
                ->where ('archive_id', $archive->id)
                ->where ('depth', '<=' , $max_depth)
                ->orderBy ( 'depth', 'asc' )
                ->orderBy ( 'index', 'asc' )
                ->get();

            /*
            $masterList = DB::select("select
                        n.parent_id as parent_id,
                        n.id,
                        n.name,
                        n.doc_count_all as count,
                        n.depth,
                        n.system_path
            from        sa_folders n
            left join   sa_folders p1 on p1.id = n.parent_id
            where       ? in (n.parent_id,
                p1.parent_id)
            order by    n.depth, n.index;",[$folderId]);
            */

        } else {
            // 아카이브의 하위 폴더 목록
            // 3단계까지만 조회하게 제한함.
            $masterList = SAFolder::select(['id','name','parent_id','depth', 'system_path', 'doc_count', 'doc_count_all as count'])
                ->where ('archive_id', $archive->id)
                ->where ('depth', '<=' , 2)
                ->orderBy ( 'depth', 'asc' )
                ->orderBy ( 'index', 'asc' )
                ->get();

            /*
            $masterList = DB::select("select
                                n.parent_id as parent_id,
                                n.id,
                                n.name,
                                n.doc_count_all as count,
                                n.depth,
                                n.system_path
            from        sa_folders n
            where       n.archive_id = ?
            and         n.depth <= 2
            order by    n.depth, n.index;",[$archiveId]);
            //order       by p1.index, p2.index, p3.index, p4.index, p5.index, p1.id;
            */
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

    /**
     * 상단 네비게이션 아이템 조회
     * @deprecated
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
}
