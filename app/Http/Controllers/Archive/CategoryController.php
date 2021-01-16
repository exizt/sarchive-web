<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\SArchive\SADocument;
use App\Models\ArchiveCategory;
use App\Models\ArchiveCategoryRel;
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
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, $profileId) {
	    $masterList = Page::paginate(20);

		// dataSet 생성
        $dataSet = $this->createViewData ();
		$dataSet ['masterListSet'] = $masterList;
		$dataSet ['parameters']['profile'] = $profileId;
        return view ( self::VIEW_PATH . '.index', $dataSet );

	}
	
	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $profileId, $name_enc) {
		$name = urldecode($name_enc);
		
		$archiveCategory = ArchiveCategory::firstOrNew (['profile_id'=>$profileId,'name'=>$name]);

		// 이 분류에 속하는 문서 목록을 출력해준다
		$masterList = SADocument::select(['sa_archives.id', 'sa_archives.title','sa_archives.summary_var','sa_archives.reference','sa_archives.board_id','sa_archives.created_at','sa_archives.updated_at'])
	      	->join("sa_category_archive_rel as rel",'rel.archive_id','=','id')
			->where ( 'rel.category',$name )
			->orderBy ( 'sa_archives.created_at', 'desc' )
			->paginate(20);

		$childCategories = ArchiveCategoryParentRel::where('parent',$name)
			->orderBy('child')
			->pluck('child');

	    // create dataSet
	    $dataSet = $this->createViewData ();
		$dataSet ['archives'] = $masterList;
		$dataSet ['ArchiveCategory'] = $archiveCategory;
		$dataSet ['childCategories'] = $childCategories;
		$dataSet ['parameters']['category'] = $name;
		$dataSet ['parameters']['profile'] = $profileId;
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $profileId, $name_enc) {
		$name = urldecode($name_enc);

	    //
	    $archiveCategory = ArchiveCategory::firstOrNew (['profile_id'=>$profileId,'name'=>$name]);
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
		$dataSet ['item'] = $archiveCategory;
		$dataSet ['parameters']['profile'] = $profileId;
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
	public function update(Request $request, $profileId, $name_enc) {
		$name = urldecode($name_enc);

		$id = $request->input ('id');

		if(is_numeric($id) && $id > 0){
			$archiveCategory = ArchiveCategory::findOrFail ($id);
		} else {
			$archiveCategory = ArchiveCategory::firstOrNew (['profile_id'=>$profileId,'name'=>$name]);
		}
		
		// saving
		$archiveCategory->text = $request->input ('text');
		$archiveCategory->parent = $request->input ('parent');
		$archiveCategory->save ();
		
		//상위 분류에 대한 처리
		{
			// 상위 분류에 대한 기존 연결을 제거
			ArchiveCategoryParentRel::where([
				['profile_id','=',$profileId],
				['child',$archiveCategory->name]
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
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($profileId, $id) {

	    $item = ArchiveCategory::findOrFail($id);
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
