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
     *
     * @deprecated 사용하지 않음.
     */
    public function index(Request $request, $archiveId) {
        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

        // 카테고리 목록 조회
        $masterList = SACategory::where('archive_id', $archiveId)
        ->paginate(20);

        // dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterList'] = $masterList;
        //$dataSet ['parameters']['profile'] = $archiveId;
        return view ( self::VIEW_PATH . '.index', $dataSet );

    }



    /**
     * 카테고리 편집
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $archiveId, $categoryId) {

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($archiveId);

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
        return redirect ()->route ( 'explorer.category', ['archive'=>$archiveId,'category'=>urlencode($item->name)])
        ->with('message', '저장되었습니다.' );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($archiveId, $id) {

        // 데이터 유무 확인 및 조회
        $item = SACategory::findOrFail($id);

        // archiveId 권한 체크 및 조회
        $archive = $this->retrieveAuthArchive($item->archive_id);

        // 삭제 진행
        $item->delete();

        // 카테고리 릴레이션 정보 삭제
        SACategoryRel::where('archive_id',$archiveId)
            ->where('child_category_name',$item->name)
            ->delete();

        // 결과 처리
        return redirect()
        ->route(self::ROUTE_ID.'.show', ['archive'=>$archiveId, 'category'=>urlencode($item->name)])
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
            $dataSet = array();
            foreach($categoryNames as $k => $categoryName){
                if(strlen(trim($categoryName))>0){
                    $dataSet[$k] = [
                        'archive_id' => $archiveId,
                        'category_name' => trim($categoryName),
                        'child_category_name' => $currentName,
                        'created_at' => Carbon::now()
                    ];
                }
            }

            // 대량 할당
            if(count($dataSet)>0){
                SACategoryRel::insert($dataSet);
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

}
