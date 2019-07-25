<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveCategory;
use App\Models\ArchiveCategoryRel;
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
	public function index(Request $request) {
	    $masterList = Page::paginate(20);

		// dataSet 생성
        $dataSet = $this->createViewData ();
        $dataSet ['masterListSet'] = $masterList;
        return view ( self::VIEW_PATH . '.index', $dataSet );

	}
	
	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $name_enc) {
		$name = urldecode($name_enc);
		
		// 이 분류에 속하는 문서 목록을 출력해준다
		$masterList = Archive::select(['sa_archives.id', 'sa_archives.title','sa_archives.summary_var','sa_archives.reference','sa_archives.board_id','sa_archives.created_at','sa_archives.updated_at'])
	      	->join("sa_category_archive_rel as rel",'rel.archive_id','=','id')
			->where ( 'rel.category',$name )
			->orderBy ( 'sa_archives.created_at', 'desc' )
			->paginate(20);


	    // create dataSet
	    $dataSet = $this->createViewData ();
		$dataSet ['archives'] = $masterList;
		$dataSet ['parameters']['category'] = $name;
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $name_enc) {
		$name = urldecode($name_enc);

	    //
	    $archiveCategory = ArchiveCategory::firstOrNew (['name'=>$name]);
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
	    $dataSet ['item'] = $archiveCategory;
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
	public function update(Request $request, $name_enc) {
		$name = urldecode($name_enc);

		$id = $request->input ('id');

		if(is_numeric($id) && $id > 0){
			$archiveCategory = ArchiveCategory::findOrFail ($id);
		} else {
			$archiveCategory = ArchiveCategory::firstOrNew (['name'=>$name]);
		}
		
		// saving
		$archiveCategory->text = $request->input ('text');
		$archiveCategory->parent = $request->input ('parent');
		$archiveCategory->save ();
		
		// after processing
		if ($request->action === 'continue') {
			return redirect ()->back ()->withSuccess ( 'Post saved.' );
		}
		return redirect ()->route ( self::ROUTE_ID . '.show', $name_enc)->withSuccess ( 'Post saved.' );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {

	    $item = ArchiveCategory::findOrFail($id);
	    $item->delete();
	    
		return redirect()
		->route(self::ROUTE_ID.'.index')
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
