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
use App\Models\SArchive\SACategory;
use App\Models\SArchive\SACategoryRel;

class CategoryController extends Controller {
    protected const VIEW_PATH = 'app.category';
    protected const ROUTE_ID = 'category';

    /**
     * 연관된 archive 개체
     */
    protected $archive = null;

    /**
     * 생성자
     */
    public function __construct() {
        $this->middleware ( 'auth' );
    }


    /**
     * 카테고리 목록
     */
    public function index(Request $request, $archiveId) {
        $masterList = SACategory::where('archive_id', $archiveId)
        ->paginate(20);

        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
        //$dataSet ['parameters']['profile'] = $archiveId;
        return view ( self::VIEW_PATH . '.index', $dataSet );

    }
    

    /**
     * 카테고리에 해당하는 문서 목록 및 카테고리 정보
     *
     */
    public function show(Request $request, $archiveId, $encodedName) {
        // 카테고리명
        $categoryName = urldecode($encodedName);
        
        $archive = SAArchive::find($archiveId);

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
            ->paginate(20);

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
        //$dataSet ['parameters']['profile'] = $archiveId;
        return view ( self::VIEW_PATH . '.show', $dataSet );
    }
    
    /**
     * 카테고리 편집
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $archiveId, $categoryId) {
        //$categoryName = urldecode($encodedName);

        $archive = SAArchive::find($archiveId);

        //
        //$category = SACategory::firstOrNew (['profile_id'=>$archiveId,'name'=>$categoryName]);
        $category = SACategory::find($categoryId);
        
        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['item'] = $category;
        $dataSet ['archive'] = $archive;
        //$dataSet ['parameters']['profile'] = $archiveId;
        //$dataSet ['parameters']['categoryId'] = $categoryId;
        return view ( self::VIEW_PATH . '.edit', $dataSet );
        
    }

    /**
     * 카테고리 편집 > 저장
     *
     * @param \Illuminate\Http\Request $request        	
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $archiveId, $categoryId) {

        $archive = SAArchive::find($archiveId);
        
        /**
         * 파라미터
         */
        $comments = $request->input ('comments');
        $category = $request->input ('category');
        
        // 카테고리 데이터 조회
        $item = SACategory::findOrFail ($categoryId);
        
        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($item->archive_id);

        // saving
        $beforeCategory = $item->category;
        $item->comments = $comments;
        $item->category = $category;
        $item->save ();
        
        // 카테고리끼리의 릴레이션을 갱신
        if($beforeCategory != $category){
            $this->updateCategoryRel($archiveId, $item->name, $item->category_array);
        }


        // 결과 처리
        if ($request->action === 'continue') {
            return redirect()->back()->with('message', '저장되었습니다.');
        }
        return redirect ()->route ( self::ROUTE_ID.'.show', ['archiveId'=>$archiveId,'category'=>urlencode($item->name)])
        ->with('message', '저장되었습니다.' );
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function destroy($archiveId, $id) {

        $item = SACategory::findOrFail($id);
        $item->delete();
        
        SACategoryRel::where('archive_id',$archiveId)
            ->where('child_category_name',$item->name)
            ->delete();

        return redirect()
        ->route(self::ROUTE_ID.'.show', ['archiveId'=>$archiveId, 'category'=>urlencode($item->name)])
        ->with('message', '삭제되었습니다.' );
    }


    /**
     * '현재 분류'와 '상위 분류'의 릴레이션 갱신
     * @param $archiveId 아카이브 id
     * @param $currentName 현재 분류의 이름
     * @param $categoryNames 상위 분류의 이름을 가진 배열
     */
    private function updateCategoryRel($archiveId, $currentName, $categoryNames){

        // 상위 분류에 대한 기존 연결을 제거
        SACategoryRel::where('archive_id',$archiveId)
            ->where('child_category_name',$currentName)
            ->delete();
        
        if(count($categoryNames) > 0){
            // insert 할 데이터 생성
            $datas = array();
            foreach($categoryNames as $k => $categoryName){
                if(strlen(trim($categoryName))>0){
                    $datas[$k] = [
                        'archive_id' => $archiveId,
                        'category_name' => trim($categoryName),
                        'child_category_name' => $currentName,
                        'created_at' => Carbon::now()
                    ];
                }
            }

            // 대량 할당
            if(count($datas)>0){
                SACategoryRel::insert($datas);
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
        $dataSet = array ();
        $dataSet ['ROUTE_ID'] = self::ROUTE_ID;
        $dataSet ['VIEW_PATH'] = self::VIEW_PATH;
        $dataSet ['parameters'] = array();
        return $dataSet;
    }
        
}
