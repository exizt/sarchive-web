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

class FolderController extends Controller {
    protected const VIEW_PATH = 'app.folder';
    protected const ROUTE_ID = 'folders';

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
     * 문서 생성
     * 
     * archive_id 파라미터를 필수로 한다. 
     */
    public function create(Request $request) {
        
        /*
        // 유효성 검증
        $validatedData = $request->validate([
            'archive_id' => 'required|integer'
        ]);
        */

        // 파라미터
        $archiveId = $request->input('archive');
        $parentId = $request->input('parent');

        // 파라미터 체크
        if(empty($archiveId) && empty($parentId)){
            abort(403);
        }

        // parentId
        if(!empty($parentId)){
            $parentFolder = SAFolder::findOrFail($parentId);
            $archiveId = $parentFolder->archive_id;
        } else {
            $parentFolder = null;
        }

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        $folder = new SAFolder;

        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $folder;
        $dataSet ['parameters']['archive_id'] = $archiveId;
        $dataSet ['parentFolder'] = $parentFolder;
        $dataSet ['cancelButtonLink'] = url()->previous();
        return view ( self::VIEW_PATH . '.create', $dataSet );
    }
    


    /**
     * 문서 편집
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $folderId) {

        // 문서 내용 조회
        $folder = SAFolder::findOrFail($folderId);
        $archiveId = $folder->archive_id;
        $parentId = $folder->parent_id;

        // parentId
        if(!empty($parentId)){
            $parentFolder = SAFolder::find($parentId);
        } else {
            $parentFolder = null;
        }

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $folder;
        $dataSet ['parentFolder'] = $parentFolder;
        $dataSet ['cancelButtonLink'] = url()->previous();
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
        $archiveId = $request->input('archive_id');
        $name = $request->input('name');
        $comments = $request->input('comments');
        $parentId = $request->input('parent_id');

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);
        
        // 데이터 insert	
        $folder = new SAFolder;
        $folder->archive_id = $archive->id;
        $folder->name = $name;
        $folder->comments = $comments;
        $folder->save ();

        /**
         * System Path 처리
         * 
         * 주의 사항
         * (순서에 주의) 'folder.id' 값을 필요로 하므로 
         * 앞서 저장작업이 마무리되어 있어야 한다.
         */
        if(isset($parentId) && $parentId != 0){
            // 상위 폴더 조회 및 검증
            $parentFolder = SAFolder::findOrFail($parentId);
            $folder->parent_id = $parentFolder->id;

            // System Path 및 Depth 처리
            $folder->system_path = SAFolder::generateSystemPathAppend($folder->id, $parentFolder->system_path);
            $folder->depth = $parentFolder->depth + 1;
            $folder->save();
        } else {
            $folder->system_path = SAFolder::generateSystemPath($folder->id);
            $folder->depth = 1;
            $folder->save();
        }
        return redirect("/folders/{$folder->id}")
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
        $name = $request->input('name');
        $comments = $request->input('comments');
        $parentId = $request->input('parent_id');
        // 저장 옵션
        $submitAction = $request->input ('action');

        // 문서 데이터 조회
        $folder = SAFolder::findOrFail ( $id );

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($folder->archive_id);


        /**
         * parent_id 및 system_path 변경
         * parent_id = 0 일 때는 '상위 폴더 삭제'를 의미함.
         * parent_id = null 일 때는 '상위 폴더' 기능 자체를 이용하지 않을 때.
         */
        $beforeParentId = $folder->parent_id;
        $beforeSystemPath = $folder->system_path;
        $beforeDepth = $folder->depth;
        $isParentChanged = false;
        if(isset($parentId) && ($beforeParentId != $parentId)){
            if($parentId == 0||$parentId == '0'){
                $folder->system_path = SAFolder::generateSystemPath($folder->id);
                $folder->parent_id = 0;
                $folder->depth = 1;
            
                $isParentChanged = true;
            } else {
                // parentFolder 조회
                $parentFolder = SAFolder::findOrFail($parentId);
                
                // systemPath 처리
                $folder->system_path = SAFolder::generateSystemPathAppend($folder->id, $parentFolder->system_path);
    
                // parent_id 변경
                $folder->parent_id = $parentFolder->id;
                $folder->depth = $parentFolder->depth + 1;

                $isParentChanged = true;
            }
        }
        
        // 저장 처리
        $folder->name = $name;
        $folder->comments = $comments;        
        $folder->save();

        /**
         * 하위 폴더의 system Path 갱신
         */
        if($isParentChanged){
            // 하위 폴더가 있는지 조회해보고.
            $childCount = SAFolder::where('parent_id', $folder->id)->count();
            $depthGap = $folder->depth - $beforeDepth;
            if($childCount >= 1){
                SAFolder::whereRaw('left(system_path, length(?)) = ?',[$beforeSystemPath,$beforeSystemPath])
                ->update([
                    'system_path'=>DB::raw("replace(system_path, '{$beforeSystemPath}', '{$folder->system_path}')"),
                    'depth'=>DB::raw("depth + {$depthGap}")
                ]);
            }
        }

        /**
         * 카운트 갱신
         * 
         * 기존 것의 parent folder id 의 갱신 (1), 새로 바뀐 folder id 관련 갱신 (2).
         * 
         * 주의 사항
         * (순서에 주의) 'system_path'값을 참조로 재조회를 하기 때문에, 앞서 변경저장이
         * 마무리 되어있어야 한다.
         */
        if($isParentChanged){
            /// -- 상위 폴더 정보 변경이 있을 때에만 아래로 구문이 동작.

            // 예전 parentId 값이 유효할 때에만 아래 구문 동작
            if(!empty($beforeParentId)){
                $this->updateFolderDocCount($beforeParentId);
            }
            // 새로 바뀐 folder_id 에 대하여 문서 카운트 갱신.
            $this->updateFolderDocCount($folder->id);
        }

        // 결과 처리
        if ($submitAction === 'continue') {
            return redirect()->back()->with('message', '저장되었습니다.');
        }
        return redirect( "/folders/{$folder->id}")
            ->with('message', '저장되었습니다.' );
    }

    

    /**
     * 문서 삭제.
     *
     * 
     * 하위 폴더를 어떻게 처리할지 결정해야 함. 가능하면 하위 폴더가 있는 상태에서는 삭제되지
     * 않도록 하는 것도 방법일 듯함.
     * 
     * 하위 폴더가 존재할 때에는 삭제되지 않도록 한다. 
     * (하위 폴더까지 탐색하면서 처리하는 것이 다소 부담스러움. 예상되는 것으로는
     * 하위 폴더를 다른 곳으로 이동시킨다면 하위 폴더의 parent_id 변경, system_path 변경. 
     * 만약 같이 삭제한다면 해당되는 document들의 folder_id 변경. 
     * 카운트도 다 변경해주어야 함...)
     * 
     * 삭제된 폴더의 문서들의 folder_id 는 null 또는 상위 폴더로 변경해줌.
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // 데이터 조회
        $folder = SAFolder::findOrFail($id);

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($folder->archive_id);


        $childCount = SAFolder::where('parent_id', $folder->id)->count();
        if($childCount > 0){
            return redirect()->back()->with('message', '하위 폴더를 먼저 삭제해주세요.');
        } else {
            // 해당하는 게시물의 folder_id 를 상위 폴더의 id 또는 null로 변경
            $tempFolderId = ($folder->parent_id == 0)? null : $folder->parent_id;
            SADocument::where('folder_id', $folder->id)->update(
                ['folder_id'=> $tempFolderId]
            );

            // delete 실행
            $folder->delete();
            
            // 상위 folder 의 문서 수 변경.
            if(!empty($tempFolderId)){
                $this->updateFolderDocCount($folder->parent_id);
            }
            
            // @todo 폴더의 게시물 목록 or 아카이브의 게시물 목록으로 이동
            if(!empty($folder->parent_id)){
                return redirect('/folders/'.$folder->parent_id)
                ->with('message', '삭제되었습니다.' );
            } else {
                return redirect('/archives/'.$archive->id)
                ->with('message', '삭제되었습니다.' );
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
     * folder 테이블의 doc_count 값을 갱신
     */
    private function updateFolderDocCount($folderId)
    {
        if(empty($folderId)) return;
        SAFolder::updateDocCountAll($folderId);
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