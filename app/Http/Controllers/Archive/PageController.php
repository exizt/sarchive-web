<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller {
	protected const VIEW_PATH = 'app.page';
	protected const ROUTE_ID = 'page';

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
	public function show(Request $request, $pageTitle) {
	    $pageData = Page::where ( 'title', $pageTitle )->firstOrFail ();
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
		$dataSet ['page'] = $pageData;
		if($request->has('profile')){
			$dataSet ['parameters']['profile'] = $request->input('profile');
		}
	    return view ( self::VIEW_PATH . '.show', $dataSet );
	}
	
	/**
	 * 글 본문 읽기
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function staticPage(Request $request, $uri) {
	    
	    // create dataSet
	    $dataSet = $this->createViewData ();
		if($request->has('archiveId')){
			$dataSet ['parameters']['archiveId'] = $request->input('archiveId');
		}

		switch ($uri) {
			case 'shortcut':
				$blade = 'shortcut';
				break;
			
			default:
				abort(404);
				break;
		}
	    return view ( 'app.static_pages.'.$blade, $dataSet );
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
