<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\SArchive\SAArchive;
use App\Models\SArchive\SAFolder;
use App\Models\SArchive\SADocument;
use App\Models\SArchive\SACategory;
use App\Models\ArchiveCategoryParentRel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller {
    protected const VIEW_PATH = 'app.category';
    protected const ROUTE_ID = 'category';


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
        $childCategories = ArchiveCategoryParentRel::where('parent',$categoryName)
            ->orderBy('child')
            ->pluck('child');

        // create dataSet
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
        $dataSet ['archive'] = $archive;
        $dataSet ['category'] = $category;
        $dataSet ['childCategories'] = $childCategories;

        $dataSet ['parameters']['category'] = $categoryName;
        $dataSet ['parameters']['profile'] = $archiveId;
        return view ( self::VIEW_PATH . '.show', $dataSet );
    }
    
    /**
     * 카테고리 정보 수정
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
        $dataSet ['parameters']['profile'] = $archiveId;
        //$dataSet ['parameters']['categoryId'] = $categoryId;
        return view ( self::VIEW_PATH . '.edit', $dataSet );
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request        	
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $archiveId, $categoryId) {

        $archive = SAArchive::find($archiveId);

        $category = SACategory::findOrFail ($id);
        
        // saving
        $category->comments = $request->input ('comments');
        $category->category = $request->input ('category');
        $category->save ();
        
        // 분류끼리의 릴레이션 처리
        {
            // 상위 분류에 대한 기존 연결을 제거
            ArchiveCategoryParentRel::where([
                ['archive_id',$archiveId],
                ['child',$category->name]
            ])->delete();

            
            // 상위 분류에 대한 연결을 생성
            foreach($archiveCategory->parent_array as $item){
                ArchiveCategoryParentRel::create([
                    'profile_id' => $profileId,
                    'parent'=>$item,
                    'child'=>$archiveCategory->name
                ]);
            }
        }


        // after processing
        if ($request->action === 'continue') {
            return redirect ()->back ()->withSuccess ( 'Post saved.' );
        }
        return redirect ()->route ( self::ROUTE_ID.'.show', ['profile'=>$profileId,'category'=>urlencode($name)])->withSuccess ( 'Post saved.' );
    }
    
    /**
     * '현재 분류'와 '상위 분류'의 릴레이션 갱신
     * @param $archiveId 아카이브 id
     * @param $currentName 현재 분류의 이름
     * @param $categoryNames 상위 분류의 이름을 가진 배열
     */
    private function updateCategoryParentRel($archiveId, $currentName, $categoryNames){

        // 상위 분류에 대한 기존 연결을 제거
        ArchiveCategoryParentRel::where('archive_id',$archiveId)
            ->where('child',$currentName)
            ->delete();
        
        if(count($categoryNames) > 0){
            // insert 할 데이터 생성
            $datas = array();
            foreach($categoryNames as $k => $categoryName){
                if(strlen(trim($categoryName))>0){
                    $datas[$k] = [
                        'archive_id' => $archiveId,
                        'parent' => trim($categoryName),
                        'child' => $currentName,
                        'created_at' => Carbon::now()
                    ];
                }
            }

            // 대량 할당
            if(count($datas)>0){
                ArchiveCategoryParentRel::insert($datas);
            }
        }


        // 상위 분류에 대한 연결을 생성
        foreach($archiveCategory->parent_array as $item){
            ArchiveCategoryParentRel::create([
                'profile_id' => $profileId,
                'parent'=>$item,
                'child'=>$archiveCategory->name
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id        	
     * @return \Illuminate\Http\Response
     */
    public function destroy($profileId, $id) {

        $item = SACategory::findOrFail($id);
        $item->delete();
        
        return redirect()
        ->route(self::ROUTE_ID.'.show', ['profile'=>$profileId,'category'=>$name_enc])
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
        
}
